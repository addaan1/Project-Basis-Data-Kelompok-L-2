<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\FactUserDailyMetric;
use Illuminate\Support\Carbon;

$user = User::first();
echo "Testing for User ID: " . $user->id_user . PHP_EOL;

$startDate = Carbon::now()->subWeeks(4)->startOfWeek();
$endDate = Carbon::now();

echo "Range: " . $startDate->toDateString() . " to " . $endDate->toDateString() . PHP_EOL;

$metrics = FactUserDailyMetric::where('user_id', $user->id_user)
    ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
    ->orderBy('date')
    ->get();

echo "Metrics Found: " . $metrics->count() . PHP_EOL;

if ($metrics->count() > 0) {
    echo "First Metric Date: " . $metrics->first()->date . PHP_EOL;
    echo "Last Metric Date: " . $metrics->last()->date . PHP_EOL;
} else {
    echo "No metrics found!" . PHP_EOL;
}

for ($date = $startDate->copy(); $date->lte($endDate); $date->addWeek()) {
   $weekStart = $date->copy()->startOfWeek();
   $weekEnd = $date->copy()->endOfWeek();
   
   $weekMetrics = $metrics->filter(function($m) use ($weekStart, $weekEnd) {
       $md = substr($m->date, 0, 10);
       return $md >= $weekStart->toDateString() && $md <= $weekEnd->toDateString();
   });
   
   echo "Week " . $weekStart->toDateString() . " - " . $weekEnd->toDateString() . ": Count=" . $weekMetrics->count() . ", Sum=" . $weekMetrics->sum('total_income') . PHP_EOL;
}
