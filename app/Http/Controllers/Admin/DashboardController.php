<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pasar;
use App\Models\Transaksi;

use App\Models\FactUserDailyMetric;
use Illuminate\Support\Facades\DB;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $adminStats = $this->dashboardService->getAdminStats();
        $chartData = $this->dashboardService->getAdminChartData();

        // Get recent transactions for the dashboard widget
        $recentTransactions = Transaksi::with(['penjual', 'pembeli'])
            ->latest()
            ->take(5)
            ->get();

        // Get latest users for the widget
        $latestUsers = User::latest()->take(5)->get();

        // Pass variables to view
        $totalUsers = $adminStats['total_users'];
        $totalProducts = Pasar::count(); 
        $totalTransactions = Transaksi::count();

        return view('admin.dashboard', compact('totalUsers', 'totalProducts', 'totalTransactions', 'recentTransactions', 'latestUsers', 'adminStats', 'chartData'));
    }

    public function backup()
    {
        $data = [
            'users' => User::all(),
            'transactions' => Transaksi::all(),
            'generated_at' => now()->toDateTimeString(),
        ];
        
        $filename = 'backup_database_' . date('Y-m-d_H-i-s') . '.json';
        
        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, $filename);
    }

    public function logs()
    {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return response('No logs found.', 404);
        }

        $logs = file_get_contents($logFile);
        $lines = explode("\n", $logs);
        $lastLines = array_slice($lines, -50); // Get last 50 lines
        
        return response('<pre>' . implode("\n", $lastLines) . '</pre>');
    }
}
