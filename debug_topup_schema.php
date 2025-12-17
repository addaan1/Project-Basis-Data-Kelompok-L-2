<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking 'top_ups' table structure...\n";
$columns = Schema::getColumnListing('top_ups');
foreach ($columns as $column) {
    echo "Column: $column\n";
    // Get column type/details if possible (DB specific)
}

// Check raw column info from MySQL information_schema
$info = DB::select("SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'projek_basdat' AND TABLE_NAME = 'top_ups'");
print_r($info);

// Check latest TopUp
$latest = \App\Models\TopUp::latest()->first();
if ($latest) {
    echo "\nLatest TopUp ID: " . $latest->id . "\n";
    echo "Status: " . $latest->status . "\n";
    echo "Bukti: " . $latest->bukti_transfer . "\n";
    echo "Created At: " . $latest->created_at . "\n";
} else {
    echo "\nNo TopUps found.\n";
}
