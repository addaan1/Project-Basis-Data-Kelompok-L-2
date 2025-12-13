<?php
use App\Models\ProdukBeras;

$product = ProdukBeras::latest()->first();
if ($product) {
    echo "Last Product: " . $product->nama_produk . "\n";
    echo "Foto Path: " . $product->foto . "\n";
} else {
    echo "No products found.\n";
}
