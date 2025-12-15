<?php

use App\Models\User;
use App\Models\FactUserDailyMetric;
use Illuminate\Support\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate Petani User (Get first Petani)
$user = User::where('peran', 'petani')->first();
if (!$user) {
    echo "No Petani user found.\n";
    exit;
}
echo "Testing for User: {$user->name} ({$user->id_user})\n";

// Logic from DashboardService
$endDate = Carbon::now();
$startDate = Carbon::now()->subWeeks(4)->startOfWeek();

echo "Start Date: " . $startDate->toDateTimeString() . "\n";
echo "End Date: " . $endDate->toDateTimeString() . "\n";

$metrics = FactUserDailyMetric::where('user_id', $user->id_user)
    ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
    ->orderBy('date')
    ->get();

echo "Metrics Found: " . $metrics->count() . "\n";
foreach ($metrics as $m) {
    echo " - Metric Date: {$m->date}, Income: {$m->total_income}\n";
}

echo "\n--- Loop Trace ---\n";
for ($date = $startDate->copy(); $date->lte($endDate); $date->addWeek()) {
    $weekStart = $date->copy()->startOfWeek();
    $weekEnd = $date->copy()->endOfWeek();
    
    echo "Loop Iteration: " . $date->toDateTimeString() . "\n";
    echo "  Range: " . $weekStart->toDateTimeString() . " - " . $weekEnd->toDateTimeString() . "\n";
    
    // Filter metrics within this week (String Comparison)
    $weekMetrics = $metrics->filter(function($m) use ($weekStart, $weekEnd) {
        $md = substr($m->date, 0, 10);
        return $md >= $weekStart->toDateString() && $md <= $weekEnd->toDateString();
    });
    
    echo "  Week Range: " . $weekStart->toDateString() . " to " . $weekEnd->toDateString() . "\n";
    echo "  Matches: " . $weekMetrics->count() . "\n";
    if ($weekMetrics->count() > 0) {
        echo "    - Sample: " . substr($weekMetrics->first()->date, 0, 10) . "\n";
    }
}
