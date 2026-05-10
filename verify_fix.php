<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pegawai;

echo "============================================================\n";
echo "VERIFIKASI FIX: Perhitungan PNS, PPPK, PPPK PW\n";
echo "============================================================\n\n";

$query = Pegawai::aktif();

// PNS (sama seperti di controller)
$totalPns = (clone $query)
    ->where('status_cpns_pns', 'P')
    ->where(function ($q) {
        $q->whereIn('kedudukan_hukum_id', ['01', '02', '03', '04', '15'])
          ->orWhereNull('kedudukan_hukum_id');
    })
    ->count();

// CPNS
$totalCpns = (clone $query)
    ->where('status_cpns_pns', 'C')
    ->where(function ($q) {
        $q->whereIn('kedudukan_hukum_id', ['01', '02', '03', '04', '15'])
          ->orWhereNull('kedudukan_hukum_id');
    })
    ->count();

// PPPK (FIXED - tanpa fallback NULL)
$totalPppk = (clone $query)
    ->whereIn('kedudukan_hukum_id', ['71', '73'])
    ->count();

// PPPK PW (FIXED - tanpa fallback NULL)
$totalPppkPw = (clone $query)
    ->where('kedudukan_hukum_id', '101')
    ->count();

$totalPegawai = (clone $query)->count();

echo "=== Hasil Perhitungan (Setelah Fix) ===\n";
echo "  Total Pegawai Aktif: {$totalPegawai}\n";
echo "  PNS:      {$totalPns}    (Target: 5.259)\n";
echo "  CPNS:     {$totalCpns}\n";
echo "  PPPK:     {$totalPppk}    (Target: 5.510)\n";
echo "  PPPK PW:  {$totalPppkPw}    (Target: 1.720)\n";
echo "\n";

$sum = $totalPns + $totalCpns + $totalPppk + $totalPppkPw;
echo "  Sum (PNS+CPNS+PPPK+PPPK PW): {$sum}\n";
echo "  Selisih vs Total Pegawai: " . ($totalPegawai - $sum) . "\n";

echo "\n=== Validasi ===\n";
$pnsOk = $totalPns == 5259 ? '✅' : '❌';
$pppkOk = $totalPppk == 5510 ? '✅' : '❌';
$pppkPwOk = $totalPppkPw == 1720 ? '✅' : '❌';

echo "  PNS:     {$pnsOk} ({$totalPns} vs 5259)\n";
echo "  PPPK:    {$pppkOk} ({$totalPppk} vs 5510)\n";
echo "  PPPK PW: {$pppkPwOk} ({$totalPppkPw} vs 1720)\n";

echo "\n=== Raw SQL Verification ===\n";
// Direct SQL queries from implementation plan
$sqlPns = DB::select("SELECT COUNT(*) as cnt FROM pegawai 
    WHERE (kedudukan_hukum_id IN ('01','02','03','04','15') OR kedudukan_hukum_id IS NULL)
    AND status_cpns_pns = 'P'
    AND kedudukan_hukum_id != '17'")[0]->cnt ?? 0;

$sqlPppk = DB::select("SELECT COUNT(*) as cnt FROM pegawai 
    WHERE kedudukan_hukum_id IN ('71','73')")[0]->cnt ?? 0;

$sqlPppkPw = DB::select("SELECT COUNT(*) as cnt FROM pegawai 
    WHERE kedudukan_hukum_id = '101'")[0]->cnt ?? 0;

echo "  SQL PNS:     {$sqlPns}\n";
echo "  SQL PPPK:    {$sqlPppk}\n";
echo "  SQL PPPK PW: {$sqlPppkPw}\n";

echo "\n=== DONE ===\n";
