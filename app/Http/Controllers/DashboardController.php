<?php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TopUp;
use App\Models\Expenditure;
use App\Models\Inventory;
use App\Models\Pengepul;
use App\Models\Petani;
use App\Models\FactUserDailyMetric;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function index()
    {
        try {
            $user = auth()->user();
            $saldo = (float) ($user->saldo ?? 0);

            // --- 1. HYBRID ETL METRICS (Historical + Realtime Today) ---
            
            // A. Historical (from Data Warehouse)
            $history = FactUserDailyMetric::where('user_id', $user->id_user)
                ->selectRaw('
                    SUM(total_income) as income, 
                    SUM(total_expense) as expense, 
                    SUM(total_kg_sold) as kg_sold, 
                    SUM(total_kg_bought) as kg_bought,
                    SUM(transaction_count) as tx_count
                ')
                ->first();

            // B. Realtime Today (Direct Query)
            $today = now()->toDateString();
            $todayMetrics = Transaksi::where(function($q) use ($user) {
                    $q->where('id_penjual', $user->id_user)
                      ->orWhere('id_pembeli', $user->id_user);
                })
                ->whereDate('updated_at', $today)
                ->whereIn('status_transaksi', ['disetujui', 'completed'])
                ->get()
                ->reduce(function($carry, $t) use ($user) {
                    $isSale = $t->id_penjual == $user->id_user;
                    $val = ($t->harga_akhir ?? $t->harga_awalan ?? 0) * $t->jumlah;
                    
                    if ($isSale) {
                        $carry['income'] += $val;
                        $carry['kg_sold'] += $t->jumlah;
                    } else {
                        $carry['expense'] += $val;
                        $carry['kg_bought'] += $t->jumlah;
                    }
                    $carry['tx_count']++;
                    return $carry;
                }, ['income'=>0, 'expense'=>0, 'kg_sold'=>0, 'kg_bought'=>0, 'tx_count'=>0]);

            // C. Combine
            $totalIncome = ($history->income ?? 0) + $todayMetrics['income'];
            $totalExpense = ($history->expense ?? 0) + $todayMetrics['expense'];
            $totalKgSold = ($history->kg_sold ?? 0) + $todayMetrics['kg_sold'];
            $totalKgBought = ($history->kg_bought ?? 0) + $todayMetrics['kg_bought'];


            // --- 2. RECENT ACTIVITY FEED (Realtime, Limit 5) ---
            $marketTransactions = Transaksi::where(function ($q) use ($user) {
                    $q->where('id_penjual', $user->id_user)
                      ->orWhere('id_pembeli', $user->id_user);
                })
                ->select(['id_penjual', 'id_pembeli', 'jumlah', 'harga_akhir', 'tanggal', 'status_transaksi'])
                ->latest('updated_at')
                ->take(5)
                ->get()
                ->map(function ($trx) use ($user) {
                    $isSale = $trx->id_penjual == $user->id_user;
                    return (object) [
                        'type' => $isSale ? 'sale' : 'purchase',
                        'description' => ($isSale ? 'Penjualan ' : 'Pembelian ') . $trx->jumlah . ' kg',
                        'amount' => (float) ($trx->harga_akhir ?? 0) * (float) ($trx->jumlah ?? 0),
                        'date' => $trx->tanggal,
                        'status' => $trx->status_transaksi
                    ];
                });

            $topUps = TopUp::where('user_id', $user->id_user)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($topup) {
                    return (object) [
                        'type' => 'topup',
                        'description' => 'Topup saldo',
                        'amount' => (float) ($topup->amount ?? 0),
                        'date' => optional($topup->created_at)->toDateString(),
                        'status' => 'success'
                    ];
                });

            $expenditures = Expenditure::where('user_id', $user->id_user)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($exp) {
                    return (object) [
                        'type' => 'expenditure',
                        'description' => $exp->description,
                        'amount' => (float) ($exp->amount ?? 0),
                        'date' => $exp->date,
                        'status' => 'pending' // Asumsi pending jika ada di expenditure
                    ];
                });

            $activities = $marketTransactions->concat($topUps)->concat($expenditures)->sortByDesc('date')->take(5);


            // --- 3. INVENTORY & CAPACITY (Realtime Snapshot) ---
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


            // --- 4. NEGOTIATION SUMMARY (Realtime) ---
            $negotiationsSummary = \App\Models\Negosiasi::where(function ($q) use ($user) {
                    $q->where('id_petani', $user->id_user)
                      ->orWhere('id_pengepul', $user->id_user);
                })
                ->with(['produk'])
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($neg) use ($user) {
                    $isMyPropose = $neg->id_pengepul == $user->id_user; // Pengepul mengajukan
                    $label = $isMyPropose ? 'Tawaran Saya' : 'Tawaran Masuk';
                    
                    return (object) [
                        'label' => $label,
                        'product_name' => $neg->produk->nama_produk ?? 'Produk Dihapus',
                        'jumlah_kg' => (int) $neg->jumlah_kg,
                        'harga_tawar' => (float) $neg->harga_penawaran,
                        'status' => ucfirst(str_replace('_', ' ', $neg->status)),
                    ];
                });

            // --- ADMIN SPECIAL LOGIC (GLOBAL STATS) ---
            $adminStats = [];
            if ($user->peran === 'admin') {
                // GMV (Gross Merchandise Value) - Total Money Flow
                $adminStats['gmv'] = FactUserDailyMetric::sum('total_income') 
                    + Transaksi::whereDate('updated_at', $today)->where('status_transaksi', 'completed')->sum(DB::raw('harga_akhir * jumlah'));
                
                // Total User Base
                $adminStats['total_users'] = \App\Models\User::count();
                $adminStats['new_users_today'] = \App\Models\User::whereDate('created_at', $today)->count();
                
                // System Health - Active Transactions Today
                $adminStats['total_tx_today'] = Transaksi::whereDate('updated_at', $today)->count();
                $adminStats['pending_nego'] = \App\Models\Negosiasi::where('status', 'Menunggu')->count();
            }

            $view = match ($user->peran) {
                'admin' => 'admin.dashboard',
                'petani' => 'petani.dashboard',
                'pengepul' => 'pengepul.dashboard',
                default => 'dashboard',
            };

            return view($view, compact(
                'saldo',
                'activities',
                'inventoryKg',
                'inventoryTon',
                'capacityKg',
                'capacityPercent',
                'negotiationsSummary',
                'lastUpdate',
                'totalIncome',
                'totalExpense',
                'totalKgSold',
                'totalKgBought'
            ) + [
                'stokBeras' => $inventoryKg,
                'negotiationsCount' => $negotiationsSummary->where('status', 'Dalam Proses')->count(),
                'adminStats' => $adminStats // Pass admin stats
            ]);
            
        } catch (\Throwable $e) {
            Log::error('Dashboard index error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // Fallback empty view on error
            return view('dashboard', [
                'saldo'=>0, 
                'activities'=>collect(), 
                'inventoryKg'=>0,
                'inventoryTon'=>0,
                'capacityKg'=>10000,
                'capacityPercent'=>0,
                'negotiationsSummary'=>collect(),
                'lastUpdate'=>now(),
                'totalIncome'=>0,
                'totalExpense'=>0,
                'totalKgSold'=>0,
                'totalKgBought'=>0
            ]);
        }
    }
}
