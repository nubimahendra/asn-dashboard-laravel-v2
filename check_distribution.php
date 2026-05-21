<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$totalPegawai = App\Models\Pegawai::count();
echo "Total rows in pegawai table: " . $totalPegawai . "\n";

$aktifPegawai = App\Models\Pegawai::aktif()->count();
echo "Total aktif pegawai: " . $aktifPegawai . "\n";

echo "\n--- Kedudukan Hukum Distribution ---\n";
$distribution = App\Models\Pegawai::select('kedudukan_hukum_id')
    ->selectRaw('COUNT(*) as count')
    ->groupBy('kedudukan_hukum_id')
    ->get();

$activeIds = App\Models\Pegawai::ACTIVE_KEDUDUKAN_HUKUM;

foreach ($distribution as $row) {
    $id = $row->kedudukan_hukum_id;
    $count = $row->count;
    
    if (is_null($id)) {
        echo "NULL (Aktif) : $count\n";
    } else {
        $isActive = in_array((string)$id, $activeIds) ? "Aktif" : "Non-Aktif / Tidak Dihitung";
        echo "ID '$id' ($isActive) : $count\n";
    }
}
