<?php
// Run via: php artisan tinker < check_tanpa_opd.php

use App\Models\Pegawai;
use App\Models\RefEselonMapping;
use App\Models\RefUnor;

// 1. Total pegawai aktif
$total = Pegawai::aktif()->count();
echo "Total Pegawai Aktif: $total\n";

// 2. Pegawai tanpa unor_id (NULL)
$tanpaUnorId = Pegawai::aktif()->whereNull('unor_id')->count();
echo "Tanpa unor_id (NULL): $tanpaUnorId\n";

// 3. Pegawai dengan unor_id tapi unor soft-deleted/missing
$unorDeleted = Pegawai::aktif()->whereNotNull('unor_id')->whereDoesntHave('unor')->count();
echo "unor_id ada tapi ref_unor deleted/missing: $unorDeleted\n";

// 4. Pegawai dengan unor_id, unor exists, tapi nama null/empty
$unorNamaEmpty = Pegawai::aktif()->whereHas('unor', function($q) {
    $q->where('nama', '')->orWhereNull('nama');
})->count();
echo "unor exists tapi nama NULL/empty: $unorNamaEmpty\n";

// 5. Total "Tanpa OPD"
echo "Expected Tanpa OPD total: " . ($tanpaUnorId + $unorDeleted + $unorNamaEmpty) . "\n";

// 6. Pegawai struktural
$struktural = Pegawai::aktif()->where('jenis_jabatan_id', '1')->count();
echo "Pegawai Struktural (jenis_jabatan_id=1): $struktural\n";

// 7. Eselon mapping count
$eselonMappingCount = RefEselonMapping::count();
echo "Eselon Mapping Count: $eselonMappingCount\n";

// 8. Check kedudukan_hukum_id = 101 count (exclusion in filter)
$kh101 = Pegawai::where('kedudukan_hukum_id', '101')->count();
echo "Kedudukan Hukum 101 count: $kh101\n";

// 9. Pegawai aktif with unor relationship but unor->nama is available
$withNama = Pegawai::aktif()->whereHas('unor', function($q) {
    $q->where('nama', '!=', '')->whereNotNull('nama');
})->count();
echo "Pegawai aktif with valid OPD (unor->nama): $withNama\n";

// 10. Sample pegawai tanpa OPD - check what unor_id they have
$samples = Pegawai::aktif()->whereNull('unor_id')->limit(5)->get(['id', 'nama', 'nip_baru', 'unor_id', 'jabatan_id', 'jenis_jabatan_id', 'kedudukan_hukum_id']);
echo "\nSample pegawai tanpa unor_id:\n";
foreach ($samples as $s) {
    echo "  ID={$s->id} NIP={$s->nip_baru} Nama={$s->nama} KH={$s->kedudukan_hukum_id}\n";
}

// 11. Sample pegawai where unor is deleted
$samples2 = Pegawai::aktif()->whereNotNull('unor_id')->whereDoesntHave('unor')->limit(5)->get(['id', 'nama', 'nip_baru', 'unor_id']);
echo "\nSample pegawai with unor_id but unor missing:\n";
foreach ($samples2 as $s) {
    echo "  ID={$s->id} NIP={$s->nip_baru} Nama={$s->nama} unor_id={$s->unor_id}\n";
}

// 12. Check distinct jenis_jabatan_id values
$jenisJabatan = Pegawai::aktif()->select('jenis_jabatan_id')->distinct()->pluck('jenis_jabatan_id');
echo "\nDistinct jenis_jabatan_id: " . $jenisJabatan->implode(', ') . "\n";

// 13. Sample jabatan names for struktural
$sampleJabatan = Pegawai::aktif()->where('jenis_jabatan_id', '1')->with('jabatan')->limit(20)->get();
echo "\nSample jabatan struktural:\n";
foreach ($sampleJabatan as $p) {
    echo "  Jabatan: " . ($p->jabatan->nama ?? 'null') . " | jabatan_id: {$p->jabatan_id}\n";
}
