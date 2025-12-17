<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$latest = \App\Models\TopUp::latest()->first();
echo "\nLatest TopUp ID: " . $latest->id . "\n";
echo "Status: " . $latest->status . "\n";
echo "Bukti: " . $latest->bukti_transfer . "\n";
