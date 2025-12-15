<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaksi;
use Illuminate\Support\Facades\Schema;

echo "Total Transactions: " . Transaksi::count() . "\n";

$tx = Transaksi::first();
if ($tx) {
    print_r($tx->toArray());
} else {
    echo "No transactions found.\n";
}

// Check Enum columns
$columns = Schema::getColumnListing('transaksis');
// Note: getting enum values programmatically in Laravel is tricky without raw SQL, 
// so we'll just check the data first.
