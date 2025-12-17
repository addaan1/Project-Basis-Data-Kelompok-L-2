<?php

use App\Models\User;
use App\Models\ProdukBeras;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$budi = User::where('peran', 'petani')->where('nama', 'like', '%Budi%')->first();
$pengepul = User::where('peran', 'pengepul')->first();

if (!$budi) {
    echo "Pak Budi not found. Finding any Petani...\n";
    $budi = User::where('peran', 'petani')->first();
}

$product = $budi ? ProdukBeras::where('id_petani', $budi->id_user)->first() : null;

$data = [
    'petani' => $budi ? [
        'id' => $budi->id_user,
        'nama' => $budi->nama,
        'saldo' => $budi->saldo,
        'email' => $budi->email
    ] : null,
    'pengepul' => $pengepul ? [
        'id' => $pengepul->id_user,
        'nama' => $pengepul->nama,
        'saldo' => $pengepul->saldo,
        'email' => $pengepul->email
    ] : null,
    'product' => $product ? [
        'id' => $product->id_produk,
        'nama' => $product->nama_produk,
        'stok' => $product->stok,
        'harga' => $product->harga,
    ] : null
];

echo json_encode($data, JSON_PRETTY_PRINT);
