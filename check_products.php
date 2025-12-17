<?php

use App\Models\ProdukBeras;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = ProdukBeras::distinct()->get(['jenis_beras']);
foreach ($products as $product) {
    echo $product->jenis_beras . PHP_EOL;
}
