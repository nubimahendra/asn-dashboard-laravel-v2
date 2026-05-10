<?php
/**
 * Re-sync pegawai table from the latest staging batch (#16: 1778121424_merged_import)
 * 
 * This script:
 * 1. Re-analyzes all staging records (diff against current pegawai)
 * 2. Processes all new/changed records into pegawai table
 * 3. Reports results
 */
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StgPegawaiImport;
use App\Models\Pegawai;
use App\Services\PegawaiDiffService;
use App\Services\PegawaiImportService;
use Illuminate\Support\Facades\Log;

$sourceFile = '1778121424_merged_import'; // Latest batch with complete data

echo "============================================================\n";
echo "RE-SYNC: Pegawai dari batch terbaru ({$sourceFile})\n";
echo "============================================================\n\n";

// Step 1: Count staging records
$stagingRecords = StgPegawaiImport::where('source_file', $sourceFile)->get();
echo "Total staging records: {$stagingRecords->count()}\n\n";

if ($stagingRecords->isEmpty()) {
    echo "ERROR: Tidak ada staging records untuk file ini!\n";
    exit(1);
}

// Step 2: Re-analyze diff (reset sync_status based on current pegawai data)
echo "=== Step 1: Re-analyzing diff status... ===\n";
$diffService = new PegawaiDiffService();
$counts = ['new' => 0, 'changed' => 0, 'unchanged' => 0];

foreach ($stagingRecords as $row) {
    // Force update to process all records
    $row->update([
        'data_hash' => 'forced', // invalidate old hash
        'sync_status' => 'changed',
        'change_summary' => null,
        'is_processed' => false,
        'processed_at' => null,
    ]);
    $counts['changed']++;
}

echo "  New:       {$counts['new']}\n";
echo "  Changed:   {$counts['changed']}\n";
echo "  Unchanged: {$counts['unchanged']}\n";
echo "  Total:     " . array_sum($counts) . "\n\n";

$toProcess = $counts['new'] + $counts['changed'];
echo "=== Step 2: Processing {$toProcess} records (new + changed)... ===\n";

if ($toProcess === 0) {
    echo "Tidak ada record baru/berubah. Data sudah sinkron.\n";
    exit(0);
}

// Step 3: Process the records (same logic as ProcessPegawaiImport job)
$importService = app(PegawaiImportService::class);
$processedCount = 0;
$errorCount = 0;
$errors = [];

// Reload staging records with updated sync_status
$stagingRecords = StgPegawaiImport::where('source_file', $sourceFile)
    ->where('is_processed', false)
    ->get();

foreach ($stagingRecords as $staging) {
    try {
        // Skip unchanged
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

        // Progress indicator
        if ($processedCount % 500 == 0) {
            echo "  Processed: {$processedCount}/{$stagingRecords->count()}...\n";
        }
    } catch (\Exception $e) {
        $errorCount++;
        $errors[] = "ID={$staging->id} PNS={$staging->pns_id}: " . $e->getMessage();
        if ($errorCount <= 5) {
            echo "  ERROR: {$staging->pns_id} - {$e->getMessage()}\n";
        }
        continue;
    }
}

// Update batch status
$batch = \App\Models\ImportBatch::where('source_file', $sourceFile)->first();
if ($batch) {
    $batch->update(['status' => 'synced']);
}

echo "\n=== Step 3: Hasil Re-sync ===\n";
echo "  Processed: {$processedCount}\n";
echo "  Errors:    {$errorCount}\n";

// Step 4: Verify counts
echo "\n=== Step 4: Verifikasi Counts ===\n";
$query = Pegawai::aktif();

$totalPns = (clone $query)
    ->where('status_cpns_pns', 'P')
    ->where(function ($q) {
        $q->whereIn('kedudukan_hukum_id', ['01', '02', '03', '04', '15'])
          ->orWhereNull('kedudukan_hukum_id');
    })
    ->count();

$totalCpns = (clone $query)
    ->where('status_cpns_pns', 'C')
    ->where(function ($q) {
        $q->whereIn('kedudukan_hukum_id', ['01', '02', '03', '04', '15'])
          ->orWhereNull('kedudukan_hukum_id');
    })
    ->count();

$totalPppk = (clone $query)
    ->whereIn('kedudukan_hukum_id', ['71', '73'])
    ->count();

$totalPppkPw = (clone $query)
    ->where('kedudukan_hukum_id', '101')
    ->count();

$totalPegawai = (clone $query)->count();

echo "  Total Pegawai Aktif: {$totalPegawai}\n";
echo "  PNS:      {$totalPns}    (Target: 5.259)\n";
echo "  CPNS:     {$totalCpns}\n";
echo "  PPPK:     {$totalPppk}    (Target: 5.510)\n";
echo "  PPPK PW:  {$totalPppkPw}    (Target: 1.720)\n\n";

$pnsOk = $totalPns == 5259 ? '✅' : '❌';
$pppkOk = $totalPppk == 5510 ? '✅' : '❌';
$pppkPwOk = $totalPppkPw == 1720 ? '✅' : '❌';
echo "  PNS:     {$pnsOk} ({$totalPns} vs 5259)\n";
echo "  PPPK:    {$pppkOk} ({$totalPppk} vs 5510)\n";
echo "  PPPK PW: {$pppkPwOk} ({$totalPppkPw} vs 1720)\n";

$sum = $totalPns + $totalCpns + $totalPppk + $totalPppkPw;
echo "\n  Sum:     {$sum}\n";
echo "  Selisih: " . ($totalPegawai - $sum) . "\n";

echo "\n=== DONE ===\n";
