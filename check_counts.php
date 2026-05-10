<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Pegawai by kedudukan_hukum_id & status_cpns_pns ===\n";
$counts = DB::table('pegawai')
    ->selectRaw('COALESCE(CAST(kedudukan_hukum_id AS CHAR), "NULL") as khid, status_cpns_pns, COUNT(*) as total')
    ->groupBy('kedudukan_hukum_id','status_cpns_pns')
    ->orderByRaw('COALESCE(CAST(kedudukan_hukum_id AS CHAR), "NULL")')
    ->get();
foreach ($counts as $c) {
    echo "kh_id={$c->khid} | cpns_pns={$c->status_cpns_pns} | total={$c->total}\n";
}

echo "\n=== Summary per User Request ===\n";
echo "Total ALL: " . DB::table('pegawai')->count() . "\n";
echo "PNS Aktif (01,02,05,15): " . DB::table('pegawai')->whereIn('kedudukan_hukum_id',['01','02','05','15'])->count() . "\n";
echo "PPPK (71): " . DB::table('pegawai')->where('kedudukan_hukum_id','71')->count() . "\n";
echo "PPPK PW (101): " . DB::table('pegawai')->where('kedudukan_hukum_id','101')->count() . "\n";
echo "NULL kedudukan: " . DB::table('pegawai')->whereNull('kedudukan_hukum_id')->count() . "\n";

echo "\n=== Current Code Logic Counts ===\n";
echo "PNS (01,02,03,04,15 + status P): " . DB::table('pegawai')->whereIn('kedudukan_hukum_id',['01','02','03','04','15'])->where('status_cpns_pns','P')->count() . "\n";
echo "PNS (01,02,03,04,15 + status P) + NULL kedudukan P: " . DB::table('pegawai')->where(function($q){$q->whereIn('kedudukan_hukum_id',['01','02','03','04','15'])->orWhereNull('kedudukan_hukum_id');})->where('status_cpns_pns','P')->count() . "\n";
echo "CPNS (01,02,03,04,15 + status C): " . DB::table('pegawai')->whereIn('kedudukan_hukum_id',['01','02','03','04','15'])->where('status_cpns_pns','C')->count() . "\n";
echo "PPPK (71,73): " . DB::table('pegawai')->whereIn('kedudukan_hukum_id',['71','73'])->count() . "\n";
echo "PPPK PW (101): " . DB::table('pegawai')->where('kedudukan_hukum_id','101')->count() . "\n";

echo "\n=== ACTIVE_KEDUDUKAN_HUKUM scope counts ===\n";
$activeIds = ['01', '02', '03', '04', '101', '15', '71', '73'];
echo "Active scope (01,02,03,04,101,15,71,73): " . DB::table('pegawai')->whereIn('kedudukan_hukum_id', $activeIds)->count() . "\n";
echo "Active scope + NULL: " . DB::table('pegawai')->where(function($q) use ($activeIds) { $q->whereIn('kedudukan_hukum_id', $activeIds)->orWhereNull('kedudukan_hukum_id'); })->count() . "\n";

echo "\n=== All kedudukan_hukum_id values in pegawai ===\n";
$allKdh = DB::table('pegawai')->selectRaw('kedudukan_hukum_id, COUNT(*) as total')->groupBy('kedudukan_hukum_id')->orderBy('kedudukan_hukum_id')->get();
foreach ($allKdh as $k) {
    echo "kdh_id=" . ($k->kedudukan_hukum_id ?? 'NULL') . " | count={$k->total}\n";
}

echo "\n=== Kode 05 check ===\n";
echo "kedudukan_hukum_id=05: " . DB::table('pegawai')->where('kedudukan_hukum_id','05')->count() . "\n";
echo "kedudukan_hukum_id=5: " . DB::table('pegawai')->where('kedudukan_hukum_id','5')->count() . "\n";
