<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$latestBatch = App\Models\ImportBatch::orderBy('id', 'desc')->first();
echo "Latest Batch ID: " . $latestBatch->id . "\n";
echo "Source File: " . $latestBatch->source_file . "\n";
echo "Total Rows in Batch: " . $latestBatch->total_rows . "\n";
echo "Processed: " . $latestBatch->processed_count . "\n";

echo "\n--- Staging Data ---\n";
$stagingCount = App\Models\StgPegawaiImport::where('batch_id', $latestBatch->id)->count();
echo "Total rows in staging for this batch: " . $stagingCount . "\n";

$processed = App\Models\StgPegawaiImport::where('batch_id', $latestBatch->id)->where('is_processed', true)->count();
echo "Total processed: " . $processed . "\n";

$anomali = App\Models\StgPegawaiImport::where('batch_id', $latestBatch->id)->where('is_anomali', true)->count();
echo "Total anomali: " . $anomali . "\n";

$errors = App\Models\StgPegawaiImport::where('batch_id', $latestBatch->id)->whereNotNull('processing_error')->count();
echo "Total errors: " . $errors . "\n";

echo "\n--- Database pegawais ---\n";
echo "Total pegawais: " . App\Models\Pegawai::count() . "\n";
