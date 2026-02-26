<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pegawaiList = \App\Models\Pegawai::whereHas('kedudukanHukum', function ($q) {
    $q->where('wajib_iuran', true);
})->with(['riwayatJabatanAktif.jabatan', 'riwayatJabatanAktif.unor'])->get();

$generator = app(\App\Services\IuranKorpriGeneratorService::class);
$refMethod = new \ReflectionMethod(\App\Services\IuranKorpriGeneratorService::class, 'getKelasJabatan');
$refMethod->setAccessible(true);

$missing = [];

foreach ($pegawaiList as $pegawai) {
    $riwayat = $pegawai->riwayatJabatanAktif;
    if (!$riwayat)
        continue;

    $kelas = $refMethod->invoke($generator, $riwayat->jabatan_id, $riwayat->unor_id);

    if (!$kelas) {
        $jabatanNama = $riwayat->jabatan ? $riwayat->jabatan->nama : 'JABATAN TIDAK DIKETAHUI';
        $opdNama = $riwayat->unor ? $riwayat->unor->nama : 'OPD TIDAK DIKETAHUI';

        $key = $opdNama . " | " . $jabatanNama;

        if (!isset($missing[$key])) {
            $missing[$key] = [
                'opd' => $opdNama,
                'jabatan' => $jabatanNama,
                'count' => 0
            ];
        }
        $missing[$key]['count']++;
    }
}

// Sort by count descending
usort($missing, function ($a, $b) {
    return $b['count'] <=> $a['count'];
});

// Output top 50 
echo "Daftar 50 Jabatan & OPD terbanyak yang belum terpetakan Kelas Jabatannya:\n";
echo str_pad("Jumlah", 8) . " | " . str_pad("OPD", 40) . " | " . "Jabatan\n";
echo str_repeat("-", 100) . "\n";

$count = 0;
foreach ($missing as $m) {
    echo str_pad($m['count'], 8) . " | " .
        str_pad(substr($m['opd'], 0, 38), 40) . " | " .
        $m['jabatan'] . "\n";
    $count++;
    if ($count >= 50)
        break;
}

echo "\nTotal kelompok/variasi Jabatan+OPD yang belum terpetakan: " . count($missing) . "\n";
