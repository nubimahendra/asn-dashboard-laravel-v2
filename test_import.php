$staging = \App\Models\StgPegawaiImport::find(5224);
if (!$staging) {
    echo "Staging record 5224 not found.\n";
    exit;
}

$service = app(\App\Services\PegawaiImportService::class);
try {
    $service->processStagingRecord($staging);
    echo "Successfully processed staging record.\n";
    $pegawai = \App\Models\Pegawai::where('pns_id', $staging->pns_id)->first();
    echo "Pegawai is_anomali: " . ($pegawai->is_anomali ? 'true' : 'false') . "\n";
    echo "Staging is_anomali: " . ($staging->is_anomali ? 'true' : 'false') . "\n";
    echo "Staging Catatan anomali: " . $staging->catatan_anomali . "\n";
} catch (\Exception $e) {
    echo "Failed to process staging record: " . $e->getMessage() . "\n";
}
echo "END_TEST\n";
