<?php

use App\Models\User;
use App\Models\Transaksi;
use App\Models\ProdukBeras;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "--- LAST TRANSACTION ---\n";
$trx = Transaksi::latest()->first();
if ($trx) {
    echo "ID: " . $trx->id_transaksi . "\n";
    echo "Status: " . $trx->status_transaksi . "\n";
    echo "Seller ID: " . $trx->id_penjual . "\n";
    echo "Buyer ID: " . $trx->id_pembeli . "\n";
    echo "Product ID: " . $trx->id_produk . "\n";
    echo "Amount: " . $trx->harga_akhir . " x " . $trx->jumlah . "\n";
    echo "Created At: " . $trx->created_at . "\n";

    echo "\n--- SELLER INFO ---\n";
    $seller = User::find($trx->id_penjual);
    if ($seller) {
        echo "Name: " . $seller->nama . "\n";
        echo "Role: " . $seller->peran . "\n";
        echo "Saldo: " . number_format($seller->saldo) . "\n";
    } else {
        echo "Seller NOT FOUND in Users table.\n";
    }

} else {
    echo "No transactions found.\n";
}
