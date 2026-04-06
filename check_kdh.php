<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Pegawai by kedudukan_hukum_id & status_cpns_pns ===\n";
$counts = DB::table('pegawai')
    ->whereNull('deleted_at')
    ->selectRaw('COALESCE(CAST(kedudukan_hukum_id AS CHAR), "NULL") as khid, status_cpns_pns, COUNT(*) as total')
    ->groupBy('kedudukan_hukum_id','status_cpns_pns')
    ->orderByRaw('COALESCE(CAST(kedudukan_hukum_id AS CHAR), "NULL")')
    ->get();
foreach ($counts as $c) {
    echo "kh_id={$c->khid} | cpns_pns={$c->status_cpns_pns} | total={$c->total}\n";
}

echo "\n=== Summary Counts ===\n";
echo "Total all: " . DB::table('pegawai')->whereNull('deleted_at')->count() . "\n";
echo "PNS (1-4,15 + P): " . DB::table('pegawai')->whereNull('deleted_at')->whereIn('kedudukan_hukum_id',[1,2,3,4,15])->where('status_cpns_pns','P')->count() . "\n";
echo "CPNS (1-4,15 + C): " . DB::table('pegawai')->whereNull('deleted_at')->whereIn('kedudukan_hukum_id',[1,2,3,4,15])->where('status_cpns_pns','C')->count() . "\n";
echo "PPPK (71,73): " . DB::table('pegawai')->whereNull('deleted_at')->whereIn('kedudukan_hukum_id',[71,73])->count() . "\n";
echo "PPPK PW (101): " . DB::table('pegawai')->whereNull('deleted_at')->where('kedudukan_hukum_id',101)->count() . "\n";
echo "NULL kedudukan all: " . DB::table('pegawai')->whereNull('deleted_at')->whereNull('kedudukan_hukum_id')->count() . "\n";
echo "NULL kedud + P: " . DB::table('pegawai')->whereNull('deleted_at')->whereNull('kedudukan_hukum_id')->where('status_cpns_pns','P')->count() . "\n";
echo "NULL kedud + C: " . DB::table('pegawai')->whereNull('deleted_at')->whereNull('kedudukan_hukum_id')->where('status_cpns_pns','C')->count() . "\n";
