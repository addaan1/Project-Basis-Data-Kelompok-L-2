<?php

use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$sellerId = 2; // Pak Budi based on previous debug
$seller = User::find($sellerId);

echo "--- CHECKING SALDO FOR: " . $seller->nama . " (ID: $sellerId) ---\n";
echo "Current Stored Saldo: " . number_format($seller->saldo, 2) . "\n";

// Calculate expected saldo from Sales (Transactions where seller is ID 2 and status is approved/completed)
$sales = Transaksi::where('id_penjual', $sellerId)
    ->whereIn('status_transaksi', ['disetujui', 'completed'])
    ->get();

$totalSalesIncome = 0;
echo "\n--- INCOME FROM SALES ---\n";
foreach ($sales as $sale) {
    $amount = ($sale->harga_akhir ?? $sale->harga_awalan) * $sale->jumlah;
    $totalSalesIncome += $amount;
    echo "ID: {$sale->id_transaksi} | Date: {$sale->created_at} | Amount: " . number_format($amount) . "\n";
}
echo "Total Calculated Income: " . number_format($totalSalesIncome, 2) . "\n";

// Check if there are other potential sources (e.g. initial seeded balance?)
// We can't know the initial balance easily without a ledger, but we can see if the last transaction *specifically* added its amount.

$lastTrx = Transaksi::where('id_penjual', $sellerId)->latest()->first();
echo "\n--- LAST TRANSACTION ANALYSIS ---\n";
if ($lastTrx) {
    $amount = ($lastTrx->harga_akhir ?? $lastTrx->harga_awalan) * $lastTrx->jumlah;
    echo "Last Trx ID: {$lastTrx->id_transaksi}\n";
    echo "Status: {$lastTrx->status_transaksi}\n";
    echo "Expected Add Amount: " . number_format($amount) . "\n";
    
    // We can't prove it was added just by looking at the final sum unless we knew the previous sum.
    // But we can check if the current saldo is roughly consistent with "Total Income".
    
    $diff = $seller->saldo - $totalSalesIncome;
    $log = "Difference (Current - Calculated Sales): " . number_format($diff, 2) . "\n";
    if ($diff == 0) {
        $log .= "PERFECT MATCH: Saldo equals total sales history.\n";
    } elseif ($diff > 0) {
        $log .= "Current saldo is HIGHER. (Old balance or other income)\n";
    } else {
        $log .= "Current saldo is LOWER. (Potential missing funds!)\n";
    }
    echo $log;
    file_put_contents('debug_log.txt', $log, FILE_APPEND);
}
file_put_contents('debug_log.txt', "\n--- END ---\n", FILE_APPEND);
