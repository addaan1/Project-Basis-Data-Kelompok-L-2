<?php
use App\Models\Transaksi;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = Transaksi::count();
    echo "Total Data Transaksi: $count\n";
    
    if ($count > 0) {
        $sample = Transaksi::latest()->first();
        echo "Sample Transaction:\n";
        echo "ID: " . $sample->id_transaksi . "\n";
        echo "Status: " . $sample->status_transaksi . "\n";
        echo "Updated At: " . $sample->updated_at . "\n";
        echo "Tanggal: " . $sample->tanggal . "\n";
        
        // Debug query ETL logic
        $dateStr = $sample->updated_at->toDateString();
        echo "Testing ETL Query for date: $dateStr\n";
        
        $query = Transaksi::whereDate('updated_at', $dateStr)
            ->whereIn('status_transaksi', ['disetujui', 'completed']);
            
        echo "Query SQL: " . $query->toSql() . "\n";
        echo "Query Count: " . $query->count() . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
