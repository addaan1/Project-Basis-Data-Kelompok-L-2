<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\DashboardService;

$user = User::first();
$service = app(DashboardService::class);

echo "Calling getChartData('4w') for User " . $user->id_user . PHP_EOL;
$data = $service->getChartData($user, '4w');

print_r($data);
