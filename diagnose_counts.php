<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "============================================================\n";
echo "DIAGNOSTIC: Analisis Ketidakcocokan Data PPPK & PPPK PW\n";
echo "============================================================\n\n";

// 1. Data aktual di tabel pegawai
echo "=== 1. Tabel PEGAWAI: distribusi kedudukan_hukum_id ===\n";
$allKdh = DB::table('pegawai')
    ->selectRaw('COALESCE(kedudukan_hukum_id, "NULL") as khid, COUNT(*) as total')
    ->groupBy('kedudukan_hukum_id')
    ->orderBy('kedudukan_hukum_id')
    ->get();
foreach ($allKdh as $k) {
    echo "  kdh_id={$k->khid} | count={$k->total}\n";
}
echo "  TOTAL: " . DB::table('pegawai')->count() . "\n";

// 2. Data di staging table
echo "\n=== 2. Tabel STG_PEGAWAI_IMPORT: distribusi kedudukan_hukum_id ===\n";
$stgKdh = DB::table('stg_pegawai_import')
    ->selectRaw('COALESCE(kedudukan_hukum_id, "NULL") as khid, COUNT(*) as total')
    ->groupBy('kedudukan_hukum_id')
    ->orderBy('kedudukan_hukum_id')
    ->get();
foreach ($stgKdh as $k) {
    echo "  kdh_id={$k->khid} | count={$k->total}\n";
}
echo "  TOTAL: " . DB::table('stg_pegawai_import')->count() . "\n";

// 3. Import batches
echo "\n=== 3. Import Batches ===\n";
$batches = DB::table('import_batches')->orderBy('created_at', 'desc')->get();
foreach ($batches as $b) {
    echo "  ID={$b->id} | file={$b->source_file} | status={$b->status} | deactivated={$b->deactivated_count} | created={$b->created_at}\n";
}

// 4. Staging per batch/source_file
echo "\n=== 4. Staging per source_file ===\n";
$stgFiles = DB::table('stg_pegawai_import')
    ->selectRaw('source_file, COUNT(*) as total')
    ->groupBy('source_file')
    ->get();
foreach ($stgFiles as $f) {
    echo "  file={$f->source_file} | rows={$f->total}\n";
    
    // Distribution per file
    $fileKdh = DB::table('stg_pegawai_import')
        ->where('source_file', $f->source_file)
        ->selectRaw('COALESCE(kedudukan_hukum_id, "NULL") as khid, COUNT(*) as total')
        ->groupBy('kedudukan_hukum_id')
        ->orderBy('kedudukan_hukum_id')
        ->get();
    foreach ($fileKdh as $k) {
        echo "    kdh_id={$k->khid} | count={$k->total}\n";
    }
}

// 5. Pegawai yang kdh=17: apakah seharusnya PPPK/PPPK PW?
echo "\n=== 5. Pegawai dengan kdh_id=17: Cek jenis_pegawai ===\n";
$nonAktifByJenis = DB::table('pegawai')
    ->where('kedudukan_hukum_id', '17')
    ->join('ref_jenis_pegawai', 'pegawai.jenis_pegawai_id', '=', 'ref_jenis_pegawai.id')
    ->selectRaw('ref_jenis_pegawai.nama as jenis, COUNT(*) as total')
    ->groupBy('ref_jenis_pegawai.nama')
    ->get();
foreach ($nonAktifByJenis as $j) {
    echo "  jenis={$j->jenis} | count={$j->total}\n";
}

$nonAktifNullJenis = DB::table('pegawai')
    ->where('kedudukan_hukum_id', '17')
    ->whereNull('jenis_pegawai_id')
    ->count();
echo "  jenis=NULL | count={$nonAktifNullJenis}\n";

// 6. Cross-check: staging PPPK records vs pegawai table
echo "\n=== 6. Staging PPPK (kdh=71): Status di tabel pegawai ===\n";
$stgPppkPnsIds = DB::table('stg_pegawai_import')
    ->where('kedudukan_hukum_id', '71')
    ->pluck('pns_id')
    ->toArray();
echo "  Staging kdh=71 count: " . count($stgPppkPnsIds) . "\n";

if (!empty($stgPppkPnsIds)) {
    $pegawaiPppkStatus = DB::table('pegawai')
        ->whereIn('pns_id', $stgPppkPnsIds)
        ->selectRaw('COALESCE(kedudukan_hukum_id, "NULL") as khid, COUNT(*) as total')
        ->groupBy('kedudukan_hukum_id')
        ->orderBy('kedudukan_hukum_id')
        ->get();
    echo "  Pegawai tabel status untuk staging PPPK:\n";
    foreach ($pegawaiPppkStatus as $p) {
        echo "    kdh_id={$p->khid} | count={$p->total}\n";
    }
    
    $notInPegawai = count($stgPppkPnsIds) - DB::table('pegawai')->whereIn('pns_id', $stgPppkPnsIds)->count();
    echo "  Tidak ada di tabel pegawai: {$notInPegawai}\n";
}

// 7. Staging PPPK PW (kdh=101): Status di tabel pegawai
echo "\n=== 7. Staging PPPK PW (kdh=101): Status di tabel pegawai ===\n";
$stgPppkPwPnsIds = DB::table('stg_pegawai_import')
    ->where('kedudukan_hukum_id', '101')
    ->pluck('pns_id')
    ->toArray();
echo "  Staging kdh=101 count: " . count($stgPppkPwPnsIds) . "\n";

if (!empty($stgPppkPwPnsIds)) {
    $pegawaiPwStatus = DB::table('pegawai')
        ->whereIn('pns_id', $stgPppkPwPnsIds)
        ->selectRaw('COALESCE(kedudukan_hukum_id, "NULL") as khid, COUNT(*) as total')
        ->groupBy('kedudukan_hukum_id')
        ->orderBy('kedudukan_hukum_id')
        ->get();
    echo "  Pegawai tabel status untuk staging PPPK PW:\n";
    foreach ($pegawaiPwStatus as $p) {
        echo "    kdh_id={$p->khid} | count={$p->total}\n";
    }
    
    $notInPegawai = count($stgPppkPwPnsIds) - DB::table('pegawai')->whereIn('pns_id', $stgPppkPwPnsIds)->count();
    echo "  Tidak ada di tabel pegawai: {$notInPegawai}\n";
}

// 8. Check if there are multiple staging records per pns_id (different source files)
echo "\n=== 8. Duplikat pns_id di staging (beda source_file) ===\n";
$dupPnsIds = DB::table('stg_pegawai_import')
    ->selectRaw('pns_id, COUNT(DISTINCT source_file) as file_count, COUNT(*) as total')
    ->groupBy('pns_id')
    ->havingRaw('COUNT(DISTINCT source_file) > 1')
    ->limit(20)
    ->get();
echo "  Total pns_id yang ada di >1 source_file: " . DB::table('stg_pegawai_import')
    ->selectRaw('pns_id')
    ->groupBy('pns_id')
    ->havingRaw('COUNT(DISTINCT source_file) > 1')
    ->get()->count() . "\n";
foreach ($dupPnsIds->take(5) as $d) {
    echo "  pns_id={$d->pns_id} | files={$d->file_count} | rows={$d->total}\n";
}

// 9. Check the latest staging record per pns_id for PPPK
echo "\n=== 9. Latest staging record per pns_id: kdh distribution ===\n";
$latestStaging = DB::select("
    SELECT COALESCE(s.kedudukan_hukum_id, 'NULL') as khid, COUNT(*) as total
    FROM stg_pegawai_import s
    INNER JOIN (
        SELECT pns_id, MAX(id) as max_id
        FROM stg_pegawai_import
        GROUP BY pns_id
    ) latest ON s.id = latest.max_id
    GROUP BY s.kedudukan_hukum_id
    ORDER BY s.kedudukan_hukum_id
");
foreach ($latestStaging as $l) {
    echo "  kdh_id={$l->khid} | count={$l->total}\n";
}

echo "\n=== DONE ===\n";
