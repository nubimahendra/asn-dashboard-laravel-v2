<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$stg = \Illuminate\Support\Facades\DB::table('stg_pegawai_import')->select('gol_akhir')->distinct()->pluck('gol_akhir')->toArray();
$ref = \Illuminate\Support\Facades\DB::table('ref_golongan')->select('id', 'nama')->get()->toArray();

$data = [
    'stg' => $stg,
    'ref' => $ref
];

file_put_contents('out.json', json_encode($data, JSON_PRETTY_PRINT));
