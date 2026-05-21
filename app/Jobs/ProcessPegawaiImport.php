<?php

namespace App\Jobs;

use App\Models\StgPegawaiImport;
use App\Services\PegawaiImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPegawaiImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sourceFile;
    protected $deleteRemoved;

    /**
     * Create a new job instance.
     */
    public function __construct($sourceFile, $deleteRemoved = false)
    {
        $this->sourceFile = $sourceFile;
        $this->deleteRemoved = $deleteRemoved;
    }

    /**
     * Execute the job.
     */
    public function handle(PegawaiImportService $importService): void
    {
        Log::info("Processing pegawai import from file: {$this->sourceFile}");

        $pegawaiBefore = \App\Models\Pegawai::aktif()->count();
        $summaryImported = StgPegawaiImport::where('source_file', $this->sourceFile)->count();

        // Get actual summary counts from staging
        $summaryNew = StgPegawaiImport::where('source_file', $this->sourceFile)->where('sync_status', 'new')->count();
        $summaryChanged = StgPegawaiImport::where('source_file', $this->sourceFile)->where('sync_status', 'changed')->count();

        $totalRecords = StgPegawaiImport::where('source_file', $this->sourceFile)
            ->where('is_processed', false)
            ->count();
            
        $processedCount = 0;
        $errorCount = 0;

        Log::info("Found {$totalRecords} records to process (out of {$summaryImported} total in file)");

        StgPegawaiImport::where('source_file', $this->sourceFile)
            ->where('is_processed', false)
            ->chunkById(500, function ($stagingRecords) use ($importService, &$processedCount, &$errorCount, $totalRecords) {
                foreach ($stagingRecords as $staging) {
                    try {
                        // Skip if sync_status is unchanged, but mark as processed
                        if ($staging->sync_status === 'unchanged') {
                            $staging->update([
                                'is_processed' => true,
                                'processed_at' => now(),
                                'processing_error' => null,
                            ]);
                            $processedCount++;
                            continue;
                        }

                        $importService->processStagingRecord($staging);
                        $processedCount++;

                        // Log progress every 100 records to reduce log spam
                        if ($processedCount % 100 == 0 || $processedCount == $totalRecords) {
                            Log::info("Processed {$processedCount}/{$totalRecords} records");
                        }
                    } catch (\Exception $e) {
                        $errorCount++;
                        
                        // PERBAIKAN: Mark failed record as processed to prevent progress bar from getting stuck
                        $staging->update([
                            'is_processed' => true,
                            'processed_at' => now(),
                        ]);
                        
                        Log::error("Error processing staging ID {$staging->id}: " . $e->getMessage());
                        // Continue processing other records even if one fails
                        continue;
                    }
                }
                
                // Update batch progress after every chunk
                $batch = \App\Models\ImportBatch::where('source_file', $this->sourceFile)->first();
                if ($batch) {
                    $batch->update([
                        'processed_count' => $processedCount,
                        'error_count' => $errorCount,
                    ]);
                }
            });

        // Handle soft-delete if requested
        $deactivatedCount = 0;
        if ($this->deleteRemoved) {
            $deactivatedCount = \App\Models\Pegawai::whereNotNull('nip_baru')
                ->whereNotIn('nip_baru', function ($query) {
                    $query->select('nip_baru')
                        ->from('stg_pegawai_import')
                        ->where('source_file', $this->sourceFile)
                        ->whereNotNull('nip_baru');
                })
                ->where(function ($q) {
                    $q->whereNotIn('kedudukan_hukum_id', ['17'])
                      ->orWhereNull('kedudukan_hukum_id');
                })
                ->update(['kedudukan_hukum_id' => '17']);

            if ($deactivatedCount > 0) {
                Log::info("Deactivated {$deactivatedCount} pegawai (kedudukan_hukum_id -> 17) not found in import file {$this->sourceFile}");
            }
        }

        $pegawaiAfter = \App\Models\Pegawai::aktif()->count();

        // Update batch record with final status and deactivation count
        $batch = \App\Models\ImportBatch::where('source_file', $this->sourceFile)->first();
        if ($batch) {
            $status = 'synced';
            if ($errorCount > 0) {
                $status = ($processedCount - $errorCount > 0) ? 'partial' : 'failed';
            }
            
            $batch->update([
                'total_pegawai_before' => $pegawaiBefore,
                'total_pegawai_after' => $pegawaiAfter,
                'summary_imported' => $summaryImported,
                'summary_new' => $summaryNew,
                'summary_changed' => $summaryChanged,
                'deactivated_count' => $deactivatedCount,
                'synced_at' => now(),
                'status' => $status,
                'processed_count' => $processedCount,
                'error_count' => $errorCount,
            ]);
        }

        Log::info("Import completed. Processed: {$processedCount}, Errors: {$errorCount}. Pegawai count: {$pegawaiBefore} -> {$pegawaiAfter}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Pegawai import job failed for file {$this->sourceFile}: " . $exception->getMessage());
    }
}
