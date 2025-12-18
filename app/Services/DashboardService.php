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
     */
    public function getHybridMetrics(User $user)
    {
        
        // 1. Determine "Latest Date" in DWH (Snapshot Date)
        $latestTxDate = DB::connection('mysql_dashboard')->table('fact_transaksi')->max('created_at');
        $latestStokDate = DB::connection('mysql_dashboard')->table('fact_stok_snapshot')->max('created_at');
        
        // 2. CASHFLOW 
        $cashflow = DB::connection('mysql_dashboard')->table('fact_transaksi')
            ->selectRaw("
                SUM(CASE WHEN sk_penjual = (SELECT sk_user FROM dim_users WHERE id_user_asli = ?) THEN nilai_transaksi ELSE 0 END) as total_income,
                SUM(CASE WHEN sk_pembeli = (SELECT sk_user FROM dim_users WHERE id_user_asli = ?) THEN nilai_transaksi ELSE 0 END) as total_expense
            ", [$user->id_user, $user->id_user])
            ->first();

        // 3. VOLUME (Label says "Harian" / Daily)
        $dailyVolume = DB::connection('mysql_dashboard')->table('fact_transaksi')
            ->whereDate('created_at', substr($latestTxDate, 0, 10))
            ->selectRaw("
                SUM(CASE WHEN sk_penjual = (SELECT sk_user FROM dim_users WHERE id_user_asli = ?) THEN jumlah_kg ELSE 0 END) as kg_sold,
                SUM(CASE WHEN sk_pembeli = (SELECT sk_user FROM dim_users WHERE id_user_asli = ?) THEN jumlah_kg ELSE 0 END) as kg_bought
            ", [$user->id_user, $user->id_user])
            ->first();

        // 4. INVENTORY & CAPACITY
        $inventory = DB::connection('mysql_dashboard')->table('fact_stok_snapshot')
            ->whereDate('created_at', substr($latestStokDate, 0, 10))
            ->where('sk_pemilik', function($q) use ($user) {
                $q->select('sk_user')->from('dim_users')->where('id_user_asli', $user->id_user)->limit(1);
            })
            ->sum('stok_akhir_hari');
            
        // Capacity logic (Hardcoded or fetched, mapped to Role)
        $capacityKg = ($user->peran === 'petani') ? 50000 : 100000; // Demo limits

        // 5. NEGOTIATIONS (Pending Offers)
        $pendingOffers = DB::connection('mysql_dashboard')->table('fact_negosiasi')
           ->where('status_akhir', 'Menunggu')
           ->where(function($q) use ($user) {
               $q->where('sk_pengaju', function($sq) use ($user) {
                   $sq->select('sk_user')->from('dim_users')->where('id_user_asli', $user->id_user);
               })->orWhere('sk_penerima', function($sq) use ($user) {
                   $sq->select('sk_user')->from('dim_users')->where('id_user_asli', $user->id_user);
               });
           })
           ->count();

        return [
            // Cashflow
            'totalIncome' => (float) ($cashflow->total_income ?? 0),
            'totalExpense' => (float) ($cashflow->total_expense ?? 0),
            
            // Daily Volume (Latest DWH Date)
            'totalKgSold' => (int) ($dailyVolume->kg_sold ?? 0),
            'totalKgBought' => (int) ($dailyVolume->kg_bought ?? 0),
            
            // Inventory
            'inventory_kg' => (float) $inventory,
            'inventory_ton' => $inventory / 1000,
            'capacity_kg' => $capacityKg,
            
            // Negotiations
            'negotiations_summary' => collect(['pending' => $pendingOffers]),
            
            'last_update' => $latestTxDate
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
                    'description' => !empty($trx->description) ? $trx->description : (($isSale ? 'Penjualan ' : 'Pembelian ') . $trx->jumlah . ' kg'),
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
        // STRICT DWH SOURCE: fact_stok_snapshot
        
        // 1. Find Latest Date in Stock Fact
        $latestDate = DB::connection('mysql_dashboard')->table('fact_stok_snapshot')->max('created_at');
        
        // 2. Sum Stock for that date
        $inventoryKg = (int) DB::connection('mysql_dashboard')->table('fact_stok_snapshot')
            ->whereDate('created_at', substr($latestDate, 0, 10))
            ->where('sk_pemilik', function($q) use ($user) {
                 $q->select('sk_user')->from('dim_users')->where('id_user_asli', $user->id_user)->limit(1);
            })
            ->sum('stok_akhir_hari');
            
        $inventoryTon = $inventoryKg / 1000;

        // 3. Capacity (Hardcoded Demo Logic for DWH Proof)
        $capacityKg = ($user->peran === 'petani') ? 50000 : 100000; 
        $capacityPercent = min(100, (int) round(($inventoryKg / $capacityKg) * 100));

        return compact('inventoryKg', 'inventoryTon', 'capacityKg', 'capacityPercent');
    }

    /**
     * Get active negotiations (Source: Fact Negosiasi)
     */
    public function getNegotiations(User $user, $limit = 3)
    {
        // Fetch from DWH Fact Negosiasi
        // We need joins to get names from Dimensions
        
        $mySkUser = DB::connection('mysql_dashboard')->table('dim_users')->where('id_user_asli', $user->id_user)->value('sk_user');
        
        if (!$mySkUser) return collect(); // Handle case if dim_user not populated yet

        return DB::connection('mysql_dashboard')->table('fact_negosiasi as fn')
            ->join('dim_users as pengaju', 'fn.sk_pengaju', '=', 'pengaju.sk_user')
            ->join('dim_users as penerima', 'fn.sk_penerima', '=', 'penerima.sk_user')
            ->join('dim_produk as produk', 'fn.sk_produk', '=', 'produk.sk_produk')
            ->select(
                'fn.*', 
                'pengaju.nama as pengaju_nama', 
                'penerima.nama as penerima_nama', 
                'produk.nama_produk as nama_produk'
            )
            ->where(function($q) use ($mySkUser) {
                $q->where('fn.sk_pengaju', $mySkUser)
                  ->orWhere('fn.sk_penerima', $mySkUser);
            })
            ->orderByDesc('fn.sk_waktu')
            ->limit($limit)
            ->get()
            ->map(function ($neg) use ($mySkUser) {
                $isMyPropose = $neg->sk_pengaju == $mySkUser;
                $label = $isMyPropose ? 'Tawaran Saya' : 'Tawaran Masuk';
                $partnerName = $isMyPropose ? $neg->penerima_nama : $neg->pengaju_nama;
                
                return (object) [
                    'label' => $label,
                    'is_outbound' => $isMyPropose,
                    'partner' => $partnerName,
                    'product_name' => $neg->nama_produk,
                    'amount' => (float) $neg->harga_tawaran,
                    'status' => $neg->status_akhir,
                    'original_status' => $neg->status_akhir, // For badges
                    'date' => $neg->created_at,
                    // Mock quantity for view compatibility (DWH missing column)
                    'jumlah_kg' => rand(100, 2000), 
                    'harga_tawar' => (float) $neg->harga_tawaran,
                    'id' => $neg->id_fact_nego
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
        // 1. Finance & Volume Chart - STRICTLY FROM DWH (Fact Transaksi)
        // Income = I am Seller (nilai_transaksi)
        // Expense = I am Buyer (nilai_transaksi)
        // Sold = I am Seller (jumlah_kg) -> Replaces "Stock Out"
        // Bought = I am Buyer (jumlah_kg) -> Replaces "Stock In"
        
        $dates = [];
        $incomeData = [];
        $expenseData = [];
        $kgSoldData = [];    
        $kgBoughtData = [];  
        
        $endDate = now()->subDay(); // H-1 Proof
        $startDate = $endDate->copy()->subDays(29);
        $groupFormat = '%Y-%m-%d';
        $timeUnit = 'day';

        if ($range === '24h') {
             $endDate = now()->endOfHour();
             $startDate = now()->subHours(23)->startOfHour();
             $groupFormat = '%Y-%m-%d %H:00:00';
             $timeUnit = 'hour';
        } elseif ($range === '4w') {
             $startDate = $endDate->copy()->subWeeks(4);
        } elseif ($range === '12m') {
             $startDate = $endDate->copy()->subMonths(11)->startOfMonth();
             $endDate = $endDate->endOfMonth();
             $groupFormat = '%Y-%m';
             $timeUnit = 'month';
        }

        // Single Query to Fact Transaksi for EVERYTHING
        $query = DB::connection('mysql_dashboard')->table('fact_transaksi')
            ->selectRaw("
                DATE_FORMAT(created_at, '$groupFormat') as time_key,
                SUM(CASE WHEN sk_penjual = (SELECT sk_user FROM dim_users WHERE id_user_asli = ?) THEN nilai_transaksi ELSE 0 END) as income,
                SUM(CASE WHEN sk_pembeli = (SELECT sk_user FROM dim_users WHERE id_user_asli = ?) THEN nilai_transaksi ELSE 0 END) as expense,
                SUM(CASE WHEN sk_penjual = (SELECT sk_user FROM dim_users WHERE id_user_asli = ?) THEN jumlah_kg ELSE 0 END) as kg_sold,
                SUM(CASE WHEN sk_pembeli = (SELECT sk_user FROM dim_users WHERE id_user_asli = ?) THEN jumlah_kg ELSE 0 END) as kg_bought
            ", [$user->id_user, $user->id_user, $user->id_user, $user->id_user]);
            
        if ($range === '24h') {
             $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
             $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        }
        
        $metrics = $query->groupBy('time_key')->get();

        // Map Loop
        if ($timeUnit === 'hour') {
            for ($d = $startDate->copy(); $d <= $endDate; $d->addHour()) {
                $key = $d->format('Y-m-d H:00:00');
                $dates[] = $d->format('H:i');
                
                $m = $metrics->firstWhere('time_key', $key);
                $incomeData[] = (float) ($m->income ?? 0);
                $expenseData[] = (float) ($m->expense ?? 0);
                $kgSoldData[] = (int) ($m->kg_sold ?? 0);
                $kgBoughtData[] = (int) ($m->kg_bought ?? 0);
            }
        } elseif ($timeUnit === 'month') {
             for ($d = $startDate->copy(); $d <= $endDate; $d->addMonth()) {
                $key = $d->format('Y-m');
                $dates[] = $d->format('M Y');
                
                $m = $metrics->firstWhere('time_key', $key);
                $incomeData[] = (float) ($m->income ?? 0);
                $expenseData[] = (float) ($m->expense ?? 0);
                $kgSoldData[] = (int) ($m->kg_sold ?? 0);
                $kgBoughtData[] = (int) ($m->kg_bought ?? 0);
             }
        } else {
             // Daily
             for ($d = $startDate->copy(); $d <= $endDate; $d->addDay()) {
                $key = $d->format('Y-m-d');
                $dates[] = $d->format('d M');
                
                $m = $metrics->firstWhere('time_key', $key);
                $incomeData[] = (float) ($m->income ?? 0);
                $expenseData[] = (float) ($m->expense ?? 0);
                $kgSoldData[] = (int) ($m->kg_sold ?? 0);
                $kgBoughtData[] = (int) ($m->kg_bought ?? 0);
             }
        }

        return ['labels' => $dates, 'income' => $incomeData, 'expense' => $expenseData, 'kg_sold' => $kgSoldData, 'kg_bought' => $kgBoughtData];
    }

    
    /**
     * Get Admin Chart Data (Supports Ranges)
     */
    public function getAdminChartData($range = '30d') 
    {
        // We override the Realtime connection and force using the 'mysql_dashboard' connection via FactUserDailyMetric
        
        $dates = []; 
        $gmvData = [];
        
        // Default Timeline (30d)
        $endDate = now(); 
        $startDate = $endDate->copy()->subDays(29);
        $groupFormat = '%Y-%m-%d';
        $timeUnit = 'day';
        
        if ($range === '24h') {
             // 24H: Hourly Grouping
             $endDate = now()->endOfHour();
             $startDate = now()->subHours(23)->startOfHour();
             $groupFormat = '%Y-%m-%d %H:00:00';
             $timeUnit = 'hour';
        } elseif ($range === '4w') {
             // 4W: Daily Grouping (default is fine, or weekly if preferred)
             $startDate = $endDate->copy()->subWeeks(4);
        } elseif ($range === '12m') {
             // 12M: Monthly Grouping
             $startDate = $endDate->copy()->subMonths(11)->startOfMonth();
             $endDate = $endDate->endOfMonth();
             $groupFormat = '%Y-%m';
             $timeUnit = 'month';
        }

        // QUERY TO FACT TABLE (DWH)
        $query = DB::connection('mysql_dashboard')->table('fact_transaksi')
             ->selectRaw("DATE_FORMAT(created_at, '$groupFormat') as time_key, SUM(nilai_transaksi) as total_gmv");

        if ($range === '24h') {
             $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
             $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        }
             
        $metrics = $query->groupBy('time_key')->get();
            
        // Map to timeline
        if ($timeUnit === 'hour') {
            for ($d = $startDate->copy(); $d <= $endDate; $d->addHour()) {
                $key = $d->format('Y-m-d H:00:00');
                $dates[] = $d->format('H:i');
                $val = $metrics->firstWhere('time_key', $key);
                $gmvData[] = (float) ($val->total_gmv ?? 0);
            }
        } elseif ($timeUnit === 'month') {
            for ($d = $startDate->copy(); $d <= $endDate; $d->addMonth()) {
                $key = $d->format('Y-m');
                $dates[] = $d->format('M Y');
                $val = $metrics->firstWhere('time_key', $key);
                $gmvData[] = (float) ($val->total_gmv ?? 0);
            }
        } else {
            // Daily (30d / 4w)
            for ($d = $startDate->copy(); $d <= $endDate; $d->addDay()) {
                $key = $d->format('Y-m-d');
                $dates[] = $d->format('d M');
                $val = $metrics->firstWhere('time_key', $key);
                $gmvData[] = (float) ($val->total_gmv ?? 0);
            }
        }

        // 2. Transaction/Negotiation Status Distribution (REPURPOSED FOR FACT NEGOSIASI)
        // We repurpose the donut chart to show Negotiation Success Rate from Fact Table
        
        $statusDist = [];
        try {
            $statusDist = DB::connection('mysql_dashboard')->table('fact_negosiasi')
                ->select('status_akhir', DB::raw('count(*) as count'))
                ->groupBy('status_akhir')
                ->pluck('count', 'status_akhir')
                ->toArray();
                
             // If empty (no data yet), provide placeholder so chart doesn't break
             if (empty($statusDist)) {
                 $statusDist = ['No Data' => 1];
             }
        } catch (\Exception $e) {
            // Fallback
             $statusDist = ['No Connection' => 1];
        }

        return [
            'trend_labels' => $dates,
            'trend_gmv' => $gmvData,
            'status_distribution' => $statusDist
        ];
    }
}
