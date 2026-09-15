<?php

require __DIR__.'/../../../../../../Kukuh/Web/asn-dashboard-laravel-v2/vendor/autoload.php';
$app = require_once __DIR__.'/../../../../../../Kukuh/Web/asn-dashboard-laravel-v2/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::find(1);
auth()->login($user);

$request = new \Illuminate\Http\Request();
$request->merge(['opd' => 'Kecamatan Bakung', 'pns' => 1, 'pppk' => 1]);

$controller = new \App\Http\Controllers\IuranKorpriController();

// Use Reflection to call calculateRealtime
$reflection = new \ReflectionMethod($controller, 'calculateRealtime');
$reflection->setAccessible(true);
$allIuranRates = \App\Models\IuranKorpri::all()->keyBy('golongan_key');
$calcData = $reflection->invoke($controller, $allIuranRates, 1, 1, 'Kecamatan Bakung', null);
echo "calculateRealtime total_pegawai: " . $calcData['globalTotals']['total_pegawai'] . "\n";
echo "calculateRealtime total_ber_golongan: " . $calcData['globalTotals']['total_ber_golongan'] . "\n";

// Call invoice
$invoiceResponse = $controller->invoice($request);
$invoiceData = $invoiceResponse->getData();
echo "invoice totalPegawai: " . $invoiceData['totalPegawai'] . "\n";
