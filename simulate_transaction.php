<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;
use App\Models\ProdukBeras;
use App\Models\Transaksi;
use App\Models\Negosiasi;
use App\Http\Controllers\MarketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// 1. Setup Actors
$pengepul = User::where('peran', 'pengepul')->first();
$petani = User::where('peran', 'petani')->where('nama', 'like', '%Budi%')->first(); // Pak Budi

if (!$pengepul || !$petani) {
    die("Error: Users not found.\n");
}

Auth::login($pengepul);
echo "Logged in as Pengepul: {$pengepul->nama} (ID: {$pengepul->id_user})\n";
echo "Initial Saldo: " . number_format($pengepul->saldo) . "\n";

$product = ProdukBeras::where('id_petani', $petani->id_user)->first();
if (!$product) {
    die("Error: Product not found for Pak Budi.\n");
}
echo "Target Product: {$product->nama_produk} (Stok: {$product->stok}, Harga: {$product->harga})\n";

// ---------------------------------------------------------
// TEST 1: Direct Purchase (Logic Fix Check)
// ---------------------------------------------------------
echo "\n[TEST 1] Testing Direct Purchase (10 kg)...\n";
$controller = new MarketController();

// Create Mock Request
$buyRequest = Request::create('/market/buy/'.$product->id_produk, 'POST', [
    'jumlah' => 10
]);

// Call Controller
try {
    // We can't easily capture redirect content, but we can check side effects
    $initialSellerSaldo = $petani->fresh()->saldo;
    $initialBuyerSaldo = $pengepul->fresh()->saldo;
    
    // We need to bind the request to the container for validation to work if it uses $request->validate
    // But MarketController uses $request->validate which looks at the passed object.
    
    // NOTE: validation might fail if session/csrf are missing. 
    // We'll bypass validation middleware by manually calling the logic? 
    // Controller method: public function buy(Request $request, ProdukBeras $market)
    
    $response = $controller->buy($buyRequest, $product);
    
    // Check Result
    $trx = Transaksi::where('id_pembeli', $pengepul->id_user)
        ->orderByDesc('created_at')
        ->first();
        
    echo "Transaction ID: " . ($trx ? $trx->id_transaksi : 'NULL') . "\n";
    echo "Status: " . ($trx ? $trx->status_transaksi : 'NULL') . " (Expected: disetujui)\n";
    
    $finalSellerSaldo = $petani->fresh()->saldo;
    $finalBuyerSaldo = $pengepul->fresh()->saldo;
    
    echo "Seller Saldo Diff: " . ($finalSellerSaldo - $initialSellerSaldo) . "\n";
    echo "Buyer Saldo Diff: " . ($finalBuyerSaldo - $initialBuyerSaldo) . "\n";
    
    if ($trx && $trx->status_transaksi === 'disetujui') {
        echo "✅ SUCCESS: Direct purchase was auto-approved.\n";
    } else {
        echo "❌ FAILED: Transaction status is not 'disetujui'.\n";
    }

} catch (\Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

// ---------------------------------------------------------
// TEST 2: Negotiation
// ---------------------------------------------------------
echo "\n[TEST 2] Testing Negotiation (5 kg @ Rp 1000)...\n";
$negRequest = Request::create('/market/negotiate/'.$product->id_produk, 'POST', [
    'tawaran_harga' => 1000,
    'jumlah' => 5,
    'pesan' => 'Tes Nego'
]);

try {
    $response = $controller->negotiate($negRequest, $product);
    
    $neg = Negosiasi::where('id_pengepul', $pengepul->id_user)
        ->orderByDesc('created_at')
        ->first();
        
    echo "Negosiasi ID: " . ($neg ? $neg->id : 'NULL') . "\n";
    echo "Status: " . ($neg ? $neg->status : 'NULL') . " (Expected: dalam_proses)\n";
    
    if ($neg && $neg->status === 'dalam_proses') {
        echo "✅ SUCCESS: Negotiation created successfully.\n";
    } else {
        echo "❌ FAILED: Negotiation creation failed.\n";
    }
} catch (\Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
}
