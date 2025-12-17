<?php

use App\Models\ProdukBeras;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$updates = [
    'Pandan Wangi' => 'produk/beras-pandan-wangi.jpg',
    'IR 64' => 'produk/beras-ir64.jpg',
    'Merah' => 'produk/beras-merah.jpg',
    'Rojolele' => 'produk/beras-rojolele.jpg',
    'Mentik Wangi' => 'produk/beras-mentik-wangi.jpg',
];

foreach ($updates as $type => $path) {
    if (file_exists(storage_path('app/public/' . $path))) {
        echo "Updating $type with $path...\n";
        ProdukBeras::where('jenis_beras', $type)
                  ->update(['foto' => $path]);
    } else {
        echo "Skipping $type: Image $path not found.\n";
    }
}

echo "Database update process completed.\n";
