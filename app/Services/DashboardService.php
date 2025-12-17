<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaksi;
use App\Models\TopUp;
use App\Models\Expenditure;
use App\Models\FactUserDailyMetric;
use App\Models\Negosiasi;
use App\Models\Inventory;
use App\Models\Pengepul;
use App\Models\Petani;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get hybrid metrics (Historical from Warehouse + Realtime from Transaction Table)
     */
    public function getHybridMetrics(User $user)
    {
        // 1. Historical (Fact Table)
        $history = FactUserDailyMetric::where('user_id', $user->id_user)
            ->selectRaw('
                COALESCE(SUM(total_income), 0) as income, 
                COALESCE(SUM(total_expense), 0) as expense, 
                COALESCE(SUM(total_kg_sold), 0) as kg_sold, 
                COALESCE(SUM(total_kg_bought), 0) as kg_bought,
                COALESCE(SUM(transaction_count), 0) as tx_count
            ')
            ->first();

        // 2. Realtime Today (Transactions updated today)
        $today = now()->toDateString();
        
        // We perform the aggregation in SQL for performance
        $todayMetrics = Transaksi::whereDate('updated_at', $today)
            ->whereIn('status_transaksi', ['disetujui', 'completed', 'confirmed'])
            ->where(function($q) use ($user) {
                $q->where('id_penjual', $user->id_user)
                  ->orWhere('id_pembeli', $user->id_user);
            })
            ->selectRaw("
                SUM(CASE WHEN id_penjual = ? THEN (COALESCE(harga_akhir, harga_awalan, 0) * jumlah) ELSE 0 END) as income,
                SUM(CASE WHEN id_pembeli = ? THEN (COALESCE(harga_akhir, harga_awalan, 0) * jumlah) ELSE 0 END) as expense,
                SUM(CASE WHEN id_penjual = ? THEN jumlah ELSE 0 END) as kg_sold,
                SUM(CASE WHEN id_pembeli = ? THEN jumlah ELSE 0 END) as kg_bought,
                COUNT(*) as tx_count
            ", [$user->id_user, $user->id_user, $user->id_user, $user->id_user])
            ->first();

        return [
            'totalIncome' => (float) ($history->income + ($todayMetrics->income ?? 0)),
            'totalExpense' => (float) ($history->expense + ($todayMetrics->expense ?? 0)),
            'totalKgSold' => (int) ($history->kg_sold + ($todayMetrics->kg_sold ?? 0)),
            'totalKgBought' => (int) ($history->kg_bought + ($todayMetrics->kg_bought ?? 0)),
            'txCount' => (int) ($history->tx_count + ($todayMetrics->tx_count ?? 0)),
        ];
    }

    /**
     * Get recent activity from various sources (Transactions, Topups, Expenditures)
     */
    public function getRecentActivity(User $user, $limit = 5)
    {
        // Fetch recent transactions
        $transactions = Transaksi::where(function ($q) use ($user) {
                $q->where('id_penjual', $user->id_user)
                  ->orWhere('id_pembeli', $user->id_user);
            })
            ->latest('updated_at')
            ->take($limit)
            ->get()
            ->map(function ($trx) use ($user) {
                $isSale = $trx->id_penjual == $user->id_user;
                return (object) [
                    'type' => $isSale ? 'sale' : 'purchase',
                    'description' => ($isSale ? 'Penjualan ' : 'Pembelian ') . $trx->jumlah . ' kg',
                    'amount' => (float) ($trx->harga_akhir ?? 0) * (float) ($trx->jumlah ?? 0),
                    'date' => $trx->tanggal,
                    'status' => $trx->status_transaksi,
                    'timestamp' => $trx->updated_at
                ];
            });

        // Fetch recent topups
        $topUps = TopUp::where('user_id', $user->id_user)
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($topup) {
                return (object) [
                    'type' => 'topup',
                    'description' => 'Topup saldo',
                    'amount' => (float) ($topup->amount ?? 0),
                    'date' => optional($topup->created_at)->toDateString(),
                    'status' => 'success',
                    'timestamp' => $topup->created_at
                ];
            });

        // Fetch expenditures
        $expenditures = Expenditure::where('user_id', $user->id_user)
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($exp) {
                return (object) [
                    'type' => 'expenditure',
                    'description' => $exp->description,
                    'amount' => (float) ($exp->amount ?? 0),
                    'date' => $exp->date,
                    'status' => 'pending',
                    'timestamp' => Carbon::parse($exp->date) // Approximation if created_at is missing
                ];
            });

        // Merge and sort
        return $transactions->concat($topUps)
            ->concat($expenditures)
            ->sortByDesc('timestamp')
            ->take($limit)
            ->values();
    }

    /**
     * Get inventory and capacity metrics
     */
    public function getInventoryMetrics(User $user)
    {
        $inventoryKg = (int) Inventory::where('id_user', $user->id_user)->sum('jumlah');
        $inventoryTon = $inventoryKg / 1000;

        $capacityKg = 10000; // Default
        if (($user->peran ?? null) === 'pengepul') {
            $capacityKg = (int) (Pengepul::where('id_user', $user->id_user)->value('kapasitas_tampung') ?? 0);
        } elseif (($user->peran ?? null) === 'petani') {
            $capacityKg = (int) (Petani::where('id_user', $user->id_user)->value('kapasitas_panen') ?? 0);
        }
        
        if ($capacityKg <= 0) $capacityKg = 10000;
        
        $capacityPercent = min(100, (int) round(($inventoryKg / $capacityKg) * 100));

        return compact('inventoryKg', 'inventoryTon', 'capacityKg', 'capacityPercent');
    }

    /**
     * Get active negotiations
     */
    public function getNegotiations(User $user, $limit = 3)
    {
        return Negosiasi::where(function ($q) use ($user) {
                $q->where('id_petani', $user->id_user)
                  ->orWhere('id_pengepul', $user->id_user);
            })
            ->with(['produk'])
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($neg) use ($user) {
                $isMyPropose = $neg->id_pengepul == $user->id_user; // Assuming Pengepul proposes
                $label = $isMyPropose ? 'Tawaran Saya' : 'Tawaran Masuk';
                
                return (object) [
                    'label' => $label,
                    'product_name' => $neg->produk->nama_produk ?? 'Produk Dihapus',
                    'jumlah_kg' => (int) $neg->jumlah_kg,
                    'harga_tawar' => (float) $neg->harga_penawaran,
                    'status' => ucfirst(str_replace('_', ' ', $neg->status)),
                    'original_status' => $neg->status
                ];
            });
    }

    /**
     * Get Admin Global Stats
     */
    public function getAdminStats()
    {
        $today = now()->toDateString();
        
        // GMV: Historical + Today
        $historicalGMV = FactUserDailyMetric::sum('total_income');
        $todayGMV = Transaksi::whereDate('updated_at', $today)
            ->whereIn('status_transaksi', ['disetujui', 'completed', 'confirmed'])
            ->sum(DB::raw('COALESCE(harga_akhir, harga_awalan, 0) * jumlah'));

        return [
            'gmv' => $historicalGMV + $todayGMV,
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', $today)->count(),
            'total_tx_today' => Transaksi::whereDate('updated_at', $today)->count(),
            'pending_nego' => Negosiasi::where('status', 'Menunggu')->count(),
        ];
    }
    /**
     * Get Chart Data for Dashboard (Last 30 Days)
     */
    /**
     * Get Chart Data for Dashboard (Supports Ranges)
     * Range: 24h, 30d, 4w, 12m
     */
    public function getChartData(User $user, $range = '30d')
    {
        $dates = [];
        $incomeData = [];
        $expenseData = [];
        $kgSoldData = [];
        $kgBoughtData = [];
        $format = 'd M'; // Default format

        if ($range === '24h') {
            // Special Case: Realtime Query from Transaction Table (Hourly)
            $startTime = Carbon::now()->subHours(23)->startOfHour();
            $endTime = Carbon::now()->endOfHour();
            $format = 'H:i';

            // Query hourly stats
            $hourlyStats = Transaksi::whereBetween('updated_at', [$startTime, $endTime])
                ->whereIn('status_transaksi', ['disetujui', 'completed', 'confirmed'])
                ->selectRaw('
                    DATE_FORMAT(updated_at, "%Y-%m-%d %H:00:00") as hour_key,
                    SUM(CASE WHEN id_penjual = ? THEN (COALESCE(harga_akhir, harga_awalan, 0) * jumlah) ELSE 0 END) as income,
                    SUM(CASE WHEN id_pembeli = ? THEN (COALESCE(harga_akhir, harga_awalan, 0) * jumlah) ELSE 0 END) as expense,
                    SUM(CASE WHEN id_penjual = ? THEN jumlah ELSE 0 END) as kg_sold,
                    SUM(CASE WHEN id_pembeli = ? THEN jumlah ELSE 0 END) as kg_bought
                ', [$user->id_user, $user->id_user, $user->id_user, $user->id_user])
                ->groupBy('hour_key')
                ->get()
                ->keyBy('hour_key');

             for ($date = $startTime->copy(); $date->lte($endTime); $date->addHour()) {
                $key = $date->format('Y-m-d H:00:00');
                $dates[] = $date->format($format);
                
                $stat = $hourlyStats->get($key);
                $incomeData[] = $stat ? (float)$stat->income : 0;
                $expenseData[] = $stat ? (float)$stat->expense : 0;
                $kgSoldData[] = $stat ? (int)$stat->kg_sold : 0;
                $kgBoughtData[] = $stat ? (int)$stat->kg_bought : 0;
             }

        } else {
            // Aggregate from Fact Table
            $query = FactUserDailyMetric::where('user_id', $user->id_user);
            
            if ($range === '4w') {
                $startDate = Carbon::now()->subWeeks(4)->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $dbFormat = '%x-%v'; // Year-Week
                $format = 'W M';     // Week Number - Month
                $interval = '1 week';
            } elseif ($range === '12m') {
                $startDate = Carbon::now()->subMonths(11)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $dbFormat = '%Y-%m'; // Year-Month
                $format = 'M Y';     // Month Year
                $interval = '1 month';
            } else { // 30d default
                $startDate = Carbon::now()->subDays(29);
                $endDate = Carbon::now();
                $dbFormat = '%Y-%m-%d';
                $format = 'd M';
                $interval = '1 day';
            }

            $query->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);

            if ($range === '30d') {
                $metrics = $query->orderBy('date')->get();
            } else {
                 // Grouping for 4w and 12m
                 $metrics = $query->selectRaw("
                        DATE_FORMAT(date, '$dbFormat') as time_key,
                        SUM(total_income) as total_income,
                        SUM(total_expense) as total_expense,
                        SUM(total_kg_sold) as total_kg_sold,
                        SUM(total_kg_bought) as total_kg_bought
                    ")
                    ->groupBy('time_key')
                    ->orderBy('time_key')
                    ->get();
            }

            // Loop and Fill
            $metricsKeyed = $metrics->keyBy(fn($item) => $item->time_key ?? substr($item->date, 0, 10));

            for ($date = $startDate->copy(); $date->lte($endDate); $date->add($interval)) {
                 if ($range === '4w') {
                    $key = $date->format('Y-W'); // Match DB Year-Week logic approximately or rely on standard formatting
                    // Note: MySQL %v is week (01-53). PHP 'W' is ISO-8601 week number.
                    // For simplicity in this env, we might rely on simple key matching or just loop dates
                    // Let's rely on standard logic:
                    $key = $date->format('o-W'); // ISO Year-Week
                    $dates[] = 'W' . $date->week . ' ' . $date->format('M');
                 } elseif ($range === '12m') {
                    $key = $date->format('Y-m');
                    $dates[] = $date->format('M Y');
                 } else { // 30d
                    $key = $date->toDateString();
                    $dates[] = $date->format('d M');
                 }
                 
                 // Fallback for key matching if exact format differs slightly
                 $metric = $metricsKeyed->first(function($item, $k) use ($key, $range) {
                     if ($range === '30d') return substr($k, 0, 10) === $key;
                     if ($range === '12m') return substr($k, 0, 7) === $key;
                     return false; // For weeks exact match is tricky, let's keep it simple
                 });
                 
                 // If metric not found by key, try finding by date range overlap (improves weekly accuracy)
                 if (!$metric && $range === '4w') {
                      $weekEnd = $date->copy()->endOfWeek();
                      $metric = $metrics->filter(function ($m) use ($date, $weekEnd) {
                          $d = Carbon::parse($m->date ?? $m->time_key /* if grouped */); 
                          // This logic is complex for grouped queries. 
                          // Simplified approach: Just return the grouped key match if exists
                          return false; 
                      })->first();
                      
                      // Actually, for grouped queries, just use the key from SQL
                      $metric = $metricsKeyed->get($key); 
                      // Note: MySQL %v might differ from PHP W. 
                      // Alternative: Don't group in SQL, group in Collection for perfect PHP implementation
                 }

                 if ($range !== '30d' && $range !== '24h') {
                      // Collection grouping is safer for Week logic consistency
                      if ($range === '4w') {
                          $startW = $date->copy()->startOfWeek();
                          $endW = $date->copy()->endOfWeek();
                          $metric = new \stdClass();
                          $filtered = $metrics->filter(function($m) use ($startW, $endW) {
                               $d = Carbon::parse($m->date ?? $m->time_key); 
                               // If we didn't group in SQL, $m->date exists. If we did, $m->time_key exists.
                               // Let's refactor to Collection Grouping for 4W/12M to be safe.
                               return false;
                          });
                      }
                 }
                 
                 // RE-REFACTORING STRATEGY FOR 4W/12M to avoid SQL Group Key Mismatch:
                 // Fetch Daily Data -> Group in PHP.
            }
            // ... Redoing Logic Below within the tool call ...
        }
        
        // --- REVISED IMPLEMENTATION (Simpler & Safer) ---
        return $this->processChartData($user, $range);
    }

    private function processChartData(User $user, $range) {
         if ($range === '24h') {
            // ... (Same Hourly Logic as above) ...
            $startTime = Carbon::now()->subHours(23)->startOfHour();
            $endTime = Carbon::now()->endOfHour();
            
            $hourlyStats = Transaksi::whereBetween('updated_at', [$startTime, $endTime])
                ->whereIn('status_transaksi', ['disetujui', 'completed', 'confirmed'])
                ->selectRaw('
                    DATE_FORMAT(updated_at, "%Y-%m-%d %H:00:00") as hour_key,
                    SUM(CASE WHEN id_penjual = ? THEN (COALESCE(harga_akhir, harga_awalan, 0) * jumlah) ELSE 0 END) as income,
                    SUM(CASE WHEN id_pembeli = ? THEN (COALESCE(harga_akhir, harga_awalan, 0) * jumlah) ELSE 0 END) as expense,
                    SUM(CASE WHEN id_penjual = ? THEN jumlah ELSE 0 END) as kg_sold,
                    SUM(CASE WHEN id_pembeli = ? THEN jumlah ELSE 0 END) as kg_bought
                ', [$user->id_user, $user->id_user, $user->id_user, $user->id_user])
                ->groupBy('hour_key')
                ->pluck('income', 'hour_key'); // Simplified for brevity in thought, but need full objects.
                
            // Re-query for full objects
            $hourlyStats = Transaksi::whereBetween('updated_at', [$startTime, $endTime])
                ->whereIn('status_transaksi', ['disetujui', 'completed', 'confirmed'])
                ->selectRaw('
                    DATE_FORMAT(updated_at, "%Y-%m-%d %H:00:00") as hour_key,
                    SUM(CASE WHEN id_penjual = ? THEN (COALESCE(harga_akhir, harga_awalan, 0) * jumlah) ELSE 0 END) as income,
                    SUM(CASE WHEN id_pembeli = ? THEN (COALESCE(harga_akhir, harga_awalan, 0) * jumlah) ELSE 0 END) as expense,
                    SUM(CASE WHEN id_penjual = ? THEN jumlah ELSE 0 END) as kg_sold,
                    SUM(CASE WHEN id_pembeli = ? THEN jumlah ELSE 0 END) as kg_bought
                ', [$user->id_user, $user->id_user, $user->id_user, $user->id_user])
                ->groupBy('hour_key')
                ->get()
                ->keyBy('hour_key');

             $dates = []; $income = []; $expense = []; $sold = []; $bought = [];
             for ($date = $startTime->copy(); $date->lte($endTime); $date->addHour()) {
                $key = $date->format('Y-m-d H:00:00');
                $dates[] = $date->format('H:i');
                $stat = $hourlyStats->get($key);
                $income[] = $stat ? (float)$stat->income : 0;
                $expense[] = $stat ? (float)$stat->expense : 0;
                $sold[] = $stat ? (int)$stat->kg_sold : 0;
                $bought[] = $stat ? (int)$stat->kg_bought : 0;
             }
             return ['labels' => $dates, 'income' => $income, 'expense' => $expense, 'kg_sold' => $sold, 'kg_bought' => $bought];
         }

         // Non-Hourly: Use Fact Table + PHP Grouping
         $endDate = Carbon::now();
         if ($range === '4w') $startDate = Carbon::now()->subWeeks(4)->startOfWeek();
         elseif ($range === '12m') $startDate = Carbon::now()->subMonths(12)->startOfMonth();
         else $startDate = Carbon::now()->subDays(29); // 30d

         $metrics = FactUserDailyMetric::where('user_id', $user->id_user)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date')
            ->get();

          $dates = []; $income = []; $expense = []; $sold = []; $bought = [];
          
          if ($range === '30d') {
              for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                    $dateStr = $date->toDateString();
                    $dates[] = $date->format('d M');
                    
                    // Filter using string comparison on Carbon object works if cast, but let's be safe
                    $m = $metrics->first(function($item) use ($dateStr) {
                         // Safely handle Carbon object or string
                         $d = $item->date instanceof \Carbon\Carbon ? $item->date->toDateString() : substr($item->date, 0, 10);
                         return $d === $dateStr;
                    });

                    $income[] = $m ? (float)$m->total_income : 0;
                    $expense[] = $m ? (float)$m->total_expense : 0;
                    $sold[] = $m ? (int)$m->total_kg_sold : 0;
                    $bought[] = $m ? (int)$m->total_kg_bought : 0;
              }
          } elseif ($range === '4w') {
               // Use strict object comparison
               for ($date = $startDate->copy(); $date->lte($endDate); $date->addWeek()) {
                   $weekStart = $date->copy()->startOfWeek()->startOfDay();
                   $weekEnd = $date->copy()->endOfWeek()->endOfDay();
                   
                   // Format Label: "17 Nov - 23 Nov"
                   $dates[] = $weekStart->format('d M') . ' - ' . $weekEnd->format('d M'); 
                   
                   $weekMetrics = $metrics->filter(function($m) use ($weekStart, $weekEnd) {
                       $d = $m->date instanceof \Carbon\Carbon ? $m->date->copy()->startOfDay() : Carbon::parse($m->date)->startOfDay();
                       return $d->gte($weekStart) && $d->lte($weekEnd);
                   });

                   $income[] = $weekMetrics->sum('total_income');
                   $expense[] = $weekMetrics->sum('total_expense');
                   $sold[] = $weekMetrics->sum('total_kg_sold');
                   $bought[] = $weekMetrics->sum('total_kg_bought');
               }
          } elseif ($range === '12m') {
               for ($date = $startDate->copy(); $date->lte($endDate); $date->addMonth()) {
                   $monthStart = $date->copy()->startOfMonth()->startOfDay();
                   $monthEnd = $date->copy()->endOfMonth()->endOfDay();
                   $dates[] = $monthStart->format('M Y');
                   
                   $monthMetrics = $metrics->filter(function($m) use ($monthStart, $monthEnd) {
                       $d = $m->date instanceof \Carbon\Carbon ? $m->date->copy()->startOfDay() : Carbon::parse($m->date)->startOfDay();
                       return $d->gte($monthStart) && $d->lte($monthEnd);
                   });
                   
                   $income[] = $monthMetrics->sum('total_income');
                   $expense[] = $monthMetrics->sum('total_expense');
                   $sold[] = $monthMetrics->sum('total_kg_sold');
                   $bought[] = $monthMetrics->sum('total_kg_bought');
               }
          }
          
          return ['labels' => $dates, 'income' => $income, 'expense' => $expense, 'kg_sold' => $sold, 'kg_bought' => $bought];
    }
    
    /**
     * Get Admin Chart Data
     */
    /**
     * Get Admin Chart Data (Supports Ranges)
     */
    public function getAdminChartData($range = '30d') 
    {
        // Reuse logic logic for GMV Trend, but tailored for Admin (Global Aggregation)
        // 1. GMV and Trend
        $dates = []; $gmvData = [];

        if ($range === '24h') {
             $startTime = Carbon::now()->subHours(23)->startOfHour();
             $endTime = Carbon::now()->endOfHour();
             
             $hourlyGMV = Transaksi::whereBetween('updated_at', [$startTime, $endTime])
                ->whereIn('status_transaksi', ['disetujui', 'completed', 'confirmed'])
                ->selectRaw('DATE_FORMAT(updated_at, "%Y-%m-%d %H:00:00") as hour_key, SUM(COALESCE(harga_akhir, harga_awalan, 0) * jumlah) as gmv')
                ->groupBy('hour_key')
                ->get()
                ->keyBy('hour_key');
             
             for ($date = $startTime->copy(); $date->lte($endTime); $date->addHour()) {
                $key = $date->format('Y-m-d H:00:00');
                $dates[] = $date->format('H:i');
                $stat = $hourlyGMV->get($key);
                $gmvData[] = $stat ? (float)$stat->gmv : 0;
             }
        } else {
             // Non-hourly
             $endDate = Carbon::now();
             if ($range === '4w') $startDate = Carbon::now()->subWeeks(4)->startOfWeek();
             elseif ($range === '12m') $startDate = Carbon::now()->subMonths(12)->startOfMonth();
             else $startDate = Carbon::now()->subDays(29);

             $query = FactUserDailyMetric::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);
             
             // Efficient fetching - fetch all daily rows, then sum in PHP for W/M
             // Or Group By in SQL. Let's do Group By in SQL for Admin (Data Volume might be larger)
             
             if ($range === '30d') {
                 $rows = $query->selectRaw('date, SUM(total_income) as total_gmv')
                    ->groupBy('date')->orderBy('date')->get();
                 
                 for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                     $dStr = $date->toDateString();
                     $dates[] = $date->format('d M');
                     $r = $rows->first(fn($i) => substr($i->date, 0, 10) === $dStr);
                     $gmvData[] = $r ? (float)$r->total_gmv : 0;
                 }
             } elseif ($range === '4w') {
                 // Fetch all daily sums
                 $rows = $query->selectRaw('date, SUM(total_income) as total_gmv')
                    ->groupBy('date')->orderBy('date')->get();
                 
                 for ($date = $startDate->copy(); $date->lte($endDate); $date->addWeek()) {
                     $ws = $date->copy()->startOfWeek();
                     $we = $date->copy()->endOfWeek();
                     $dates[] = 'Week ' . $ws->week;
                     $sum = $rows->filter(fn($r) => Carbon::parse($r->date)->between($ws, $we))->sum('total_gmv');
                     $gmvData[] = $sum;
                 }
             } elseif ($range === '12m') {
                 $rows = $query->selectRaw('date, SUM(total_income) as total_gmv')
                    ->groupBy('date')->orderBy('date')->get();

                 for ($date = $startDate->copy(); $date->lte($endDate); $date->addMonth()) {
                     $ms = $date->copy()->startOfMonth();
                     $me = $date->copy()->endOfMonth();
                     $dates[] = $ms->format('M Y');
                     $sum = $rows->filter(fn($r) => Carbon::parse($r->date)->between($ms, $me))->sum('total_gmv');
                     $gmvData[] = $sum;
                 }
             }
        }

        // 2. Transaction Status Distribution (Always Realtime/All-time)
        $statusDist = Transaksi::select('status_transaksi', DB::raw('count(*) as count'))
            ->groupBy('status_transaksi')
            ->pluck('count', 'status_transaksi')
            ->toArray();
            
        return [
            'trend_labels' => $dates,
            'trend_gmv' => $gmvData,
            'status_distribution' => $statusDist
        ];
    }
}
