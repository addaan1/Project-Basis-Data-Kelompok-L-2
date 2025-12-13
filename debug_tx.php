<?php
use App\Models\Transaksi;
use App\Models\User;
use App\Models\ProdukBeras;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Attempting to create transaction...\n";
    // Find valid IDs
    $seller = User::where('peran', 'petani')->first();
    $buyer = User::where('peran', 'pengepul')->first();
    $product = ProdukBeras::first();

    if (!$seller || !$buyer || !$product) {
        die("Missing seed data (user/product)\n");
    }

    $tx = Transaksi::create([
        'id_penjual' => $seller->id_user,
        'id_pembeli' => $buyer->id_user,
        'id_produk' => $product->id_produk,
        'jumlah' => 10,
        'harga_awalan' => 10000,
        'harga_akhir' => 10000,
        'tanggal' => now(),
        'jenis_transaksi' => 'jual',
        'status_transaksi' => 'disetujui'
    ]);
    
    echo "Transaction created successfully. ID: " . $tx->id_transaksi . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
