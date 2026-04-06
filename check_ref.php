<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== REF JENIS PEGAWAI ===\n";
$jp = DB::table('ref_jenis_pegawai')->get();
foreach($jp as $r) {
    echo $r->id . " | " . $r->nama . "\n";
}

echo "\n=== REF JENIS JABATAN ===\n";
$jj = DB::table('ref_jenis_jabatan')->get();
foreach($jj as $r) {
    echo $r->id . " | " . $r->nama . "\n";
}
