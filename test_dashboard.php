<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate login
$user = \App\Models\User::first();
if ($user) {
    auth()->login($user);
}

$controller = app()->make(\App\Http\Controllers\DashboardController::class);
$request = \Illuminate\Http\Request::create('/');
try {
    $response = $controller->index($request);
    echo $response->render();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
