<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RefUnor;
use App\Helpers\UptFilterHelper;

echo "--- OPD Pendidikan ---\n";
$opds = RefUnor::where('nama', 'LIKE', '%Pendidikan%')->distinct()->pluck('nama');
foreach($opds as $opd) {
    echo "- '$opd'\n";
    var_dump(UptFilterHelper::hasUptFilter($opd));
    $groups = UptFilterHelper::getUptListGrouped($opd);
    echo "  Groups count: " . count($groups) . "\n";
    foreach($groups as $label => $g) {
        echo "  - $label: " . count($g['items']) . " items\n";
    }
}

echo "\n--- OPD Kesehatan ---\n";
$opds = RefUnor::where('nama', 'LIKE', '%Kesehatan%')->distinct()->pluck('nama');
foreach($opds as $opd) {
    echo "- '$opd'\n";
    var_dump(UptFilterHelper::hasUptFilter($opd));
    $groups = UptFilterHelper::getUptListGrouped($opd);
    echo "  Groups count: " . count($groups) . "\n";
    foreach($groups as $label => $g) {
        echo "  - $label: " . count($g['items']) . " items\n";
    }
}

echo "\n--- UPT SD ---\n";
$sd = RefUnor::where('nama_opd', 'LIKE', 'UPT SD%')->distinct()->pluck('nama_opd');
echo "Count: " . count($sd) . "\n";
if (count($sd) > 0) {
    echo "Sample: " . $sd[0] . "\n";
}

echo "\n--- UPT SMP ---\n";
$smp = RefUnor::where('nama_opd', 'LIKE', 'UPT SMP%')->distinct()->pluck('nama_opd');
echo "Count: " . count($smp) . "\n";

echo "\n--- UPT Puskesmas ---\n";
$pusk = RefUnor::where('nama_opd', 'LIKE', 'UPT Puskesmas%')->distinct()->pluck('nama_opd');
echo "Count: " . count($pusk) . "\n";
