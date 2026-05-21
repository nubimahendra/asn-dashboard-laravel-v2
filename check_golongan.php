<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check staging data for the collision IDs
echo "=== Staging: gol_akhir_id=21 data (should be II/a for PNS, V for PPPK) ===\n";

echo "\n--- PNS with gol_akhir_id = 21 ---\n";
$pns21 = App\Models\StgPegawaiImport::where('gol_akhir_id', '21')
    ->whereIn('kedudukan_hukum_id', ['01','02','03','04','15'])
    ->select('pns_id','nama','gol_akhir_id','gol_akhir','kedudukan_hukum_id')
    ->limit(3)->get();
foreach ($pns21 as $r) {
    echo "  {$r->nama} | gol_akhir_id={$r->gol_akhir_id} | gol_akhir='{$r->gol_akhir}' | kh={$r->kedudukan_hukum_id}\n";
}

echo "\n--- PPPK with gol_akhir_id = 21 ---\n";
$pppk21 = App\Models\StgPegawaiImport::where('gol_akhir_id', '21')
    ->whereIn('kedudukan_hukum_id', ['71','73','101'])
    ->select('pns_id','nama','gol_akhir_id','gol_akhir','kedudukan_hukum_id')
    ->limit(3)->get();
foreach ($pppk21 as $r) {
    echo "  {$r->nama} | gol_akhir_id={$r->gol_akhir_id} | gol_akhir='{$r->gol_akhir}' | kh={$r->kedudukan_hukum_id}\n";
}

echo "\n--- PNS with gol_akhir_id = 23 ---\n";
$pns23 = App\Models\StgPegawaiImport::where('gol_akhir_id', '23')
    ->whereIn('kedudukan_hukum_id', ['01','02','03','04','15'])
    ->select('pns_id','nama','gol_akhir_id','gol_akhir','kedudukan_hukum_id')
    ->limit(3)->get();
foreach ($pns23 as $r) {
    echo "  {$r->nama} | gol_akhir_id={$r->gol_akhir_id} | gol_akhir='{$r->gol_akhir}' | kh={$r->kedudukan_hukum_id}\n";
}

echo "\n--- PPPK with gol_akhir_id = 23 ---\n";
$pppk23 = App\Models\StgPegawaiImport::where('gol_akhir_id', '23')
    ->whereIn('kedudukan_hukum_id', ['71','73','101'])
    ->select('pns_id','nama','gol_akhir_id','gol_akhir','kedudukan_hukum_id')
    ->limit(3)->get();
foreach ($pppk23 as $r) {
    echo "  {$r->nama} | gol_akhir_id={$r->gol_akhir_id} | gol_akhir='{$r->gol_akhir}' | kh={$r->kedudukan_hukum_id}\n";
}
