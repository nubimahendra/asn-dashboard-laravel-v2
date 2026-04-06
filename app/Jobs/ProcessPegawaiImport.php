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

        // Get all unprocessed records from this source file
        $stagingRecords = StgPegawaiImport::where('source_file', $this->sourceFile)
            ->where('is_processed', false)
            ->get();

        $totalRecords = $stagingRecords->count();
        $processedCount = 0;
        $errorCount = 0;

        Log::info("Found {$totalRecords} records to process");

        foreach ($stagingRecords as $staging) {
            /** @var StgPegawaiImport $staging */
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

                // Log progress every 10 records
                if ($processedCount % 10 == 0) {
                    Log::info("Processed {$processedCount}/{$totalRecords} records");
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Error processing staging ID {$staging->id}: " . $e->getMessage());

                // Continue processing other records even if one fails
                continue;
            }
        }

        // Handle soft-delete if requested
        if ($this->deleteRemoved) {
            $importedNips = StgPegawaiImport::where('source_file', $this->sourceFile)
                ->whereNotNull('nip_baru')
                ->pluck('nip_baru')
                ->toArray();

            if (!empty($importedNips)) {
                $deletedCount = \App\Models\Pegawai::whereNotNull('nip_baru')
                    ->whereNotIn('nip_baru', $importedNips)
                    ->delete(); // Soft delete

                Log::info("Soft-deleted {$deletedCount} pegawai not found in import file {$this->sourceFile}");
            }
        }

        Log::info("Import completed. Processed: {$processedCount}, Errors: {$errorCount}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Pegawai import job failed for file {$this->sourceFile}: " . $exception->getMessage());
    }
}
