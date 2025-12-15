<?php
namespace App\Http\Controllers;

<<<<<<< Updated upstream
=======
use App\Models\Transaksi;
use App\Models\TopUp;
use App\Models\Expenditure;
use App\Models\Inventory;
use App\Models\Pengepul;
use App\Models\Petani;
use App\Models\FactUserDailyMetric;
>>>>>>> Stashed changes
use App\Services\DashboardService;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller {
    
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        try {
            $user = auth()->user();
            $saldo = (float) ($user->saldo ?? 0);

            // 1. Hybrid Metrics (Historical + Realtime)
            $metrics = $this->dashboardService->getHybridMetrics($user);

            // 2. Charts
            if ($user->peran === 'admin') {
                $chartData = $this->dashboardService->getAdminChartData();
            } else {
                $chartData = $this->dashboardService->getChartData($user);
            }

            // 3. Recent Activity
            $activities = $this->dashboardService->getRecentActivity($user);

            // 3. Inventory & Capacity
            $inventory = $this->dashboardService->getInventoryMetrics($user);

            // 4. Negotiations
            $negotiationsSummary = $this->dashboardService->getNegotiations($user);

            // 5. Admin Stats (if active)
            $adminStats = [];
            if ($user->peran === 'admin') {
                $adminStats = $this->dashboardService->getAdminStats();
            }

            $view = match ($user->peran) {
                'admin' => 'admin.dashboard',
                'petani' => 'petani.dashboard',
                'pengepul' => 'pengepul.dashboard',
                default => 'dashboard',
            };

            return view($view, [
                'saldo' => $saldo,
                'activities' => $activities,
                
                // Unpack inventory metrics
                'inventoryKg' => $inventory['inventoryKg'],
                'inventoryTon' => $inventory['inventoryTon'],
                'capacityKg' => $inventory['capacityKg'],
                'capacityPercent' => $inventory['capacityPercent'],
                
                // Negotiaton
                'negotiationsSummary' => $negotiationsSummary,
                'negotiationsCount' => $negotiationsSummary->where('original_status', 'Dalam Proses')->count(), // Filter from mapped object? Need check if status mapped properly

                // Metrics
                'totalIncome' => $metrics['totalIncome'],
                'totalExpense' => $metrics['totalExpense'],
                'totalKgSold' => $metrics['totalKgSold'],
                'totalKgBought' => $metrics['totalKgBought'],
                'stokBeras' => $inventory['inventoryKg'],
                'lastUpdate' => now(),
                'chartData' => $chartData,

                'adminStats' => $adminStats 
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

    public function data() {
        // Simple API for Admin Dashboard Debugging
        if (auth()->user()->peran !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = $this->dashboardService->getAdminStats();
        $stats['server_time'] = now()->toDateTimeString();

        return response()->json($stats);
    }

    /**
     * AJAX Endpoint for Chart Data
     */
    public function getChartDataResponse(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $range = $request->input('range', '30d');

        try {
            if ($user->peran === 'admin') {
                $data = $this->dashboardService->getAdminChartData($range);
            } else {
                $data = $this->dashboardService->getChartData($user, $range);
            }
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
