<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Transaksi;
use App\Models\FactUserDailyMetric;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateDashboardFacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:aggregate {date? : Tanggal yang ingin di-aggregate (Y-m-d), default: kemarin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ETL Process: Aggregate daily transaction data into Fact Table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateParam = $this->argument('date');
        
        if ($dateParam === 'all') {
            $startDate = Carbon::now()->subDays(30);
            $endDate = Carbon::now();
            
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $this->processDate($date);
            }
            $this->info("Completed batch processing.");
            return;
        }

        $date = $dateParam ? Carbon::parse($dateParam) : Carbon::yesterday();
        $this->processDate($date);
    }

    private function processDate($date)
    {
        $dateStr = $date->toDateString();
        $this->info("Starting ETL process for date: $dateStr");
        
        // 1. Ambil List User yang aktif bertransaksi pada tanggal tersebut
        // Kita cari transaksi yang 'disetujui' atau 'completed'
        // Baik sebagai penjual atau pembeli
        $activeUserIds = Transaksi::whereDate('updated_at', $dateStr)
            ->whereIn('status_transaksi', ['disetujui', 'completed'])
            ->pluck('id_penjual')
            ->concat(
                Transaksi::whereDate('updated_at', $dateStr)
                ->whereIn('status_transaksi', ['disetujui', 'completed'])
                ->pluck('id_pembeli')
            )
            ->unique()
            ->filter();
            
        $this->info("Found " . $activeUserIds->count() . " active users.");

        foreach ($activeUserIds as $userId) {
            $user = User::find($userId);
            if (!$user) continue;
            
            $this->processUser($user, $dateStr);
        }
    }
    
    private function processUser($user, $dateStr)
    {
        // Calculate Metrics from Main DB
        
        // 1. Total Income (Sebagai Penjual)
        $income = Transaksi::where('id_penjual', $user->id_user)
            ->whereDate('updated_at', $dateStr)
            ->whereIn('status_transaksi', ['disetujui', 'completed'])
            ->get()
            ->sum(function($t) {
                return ($t->harga_akhir ?? $t->harga_awalan ?? 0) * $t->jumlah;
            });
            
        // 2. Total Expense (Sebagai Pembeli)
        $expense = Transaksi::where('id_pembeli', $user->id_user)
            ->whereDate('updated_at', $dateStr)
            ->whereIn('status_transaksi', ['disetujui', 'completed'])
            ->get()
            ->sum(function($t) {
                return ($t->harga_akhir ?? $t->harga_awalan ?? 0) * $t->jumlah;
            });
            
        // 3. Kg Sold
        $kgSold = Transaksi::where('id_penjual', $user->id_user)
            ->whereDate('updated_at', $dateStr)
            ->whereIn('status_transaksi', ['disetujui', 'completed'])
            ->sum('jumlah');
            
        // 4. Kg Bought
        $kgBought = Transaksi::where('id_pembeli', $user->id_user)
            ->whereDate('updated_at', $dateStr)
            ->whereIn('status_transaksi', ['disetujui', 'completed'])
            ->sum('jumlah');
            
        // 5. Transaction Count
        $txCount = Transaksi::where(function($q) use ($user) {
                $q->where('id_penjual', $user->id_user)
                  ->orWhere('id_pembeli', $user->id_user);
            })
            ->whereDate('updated_at', $dateStr)
            ->whereIn('status_transaksi', ['disetujui', 'completed'])
            ->count();
            
        // Load into Data Warehouse (Fact Table)
        FactUserDailyMetric::updateOrCreate(
            [
                'date' => $dateStr,
                'user_id' => $user->id_user,
            ],
            [
                'role' => $user->peran,
                'total_income' => $income,
                'total_expense' => $expense,
                'total_kg_sold' => (int) $kgSold,
                'total_kg_bought' => (int) $kgBought,
                'transaction_count' => $txCount,
            ]
        );
        
        $this->line("Processed user {$user->id_user} ({$user->nama}): Income=$income, Expense=$expense");
    }
}
