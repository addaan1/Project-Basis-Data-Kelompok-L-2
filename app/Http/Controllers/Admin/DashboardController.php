<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pasar;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalProducts = Pasar::count(); // Assuming Pasar is the product listing
        $totalTransactions = Transaksi::count();

        // Get recent transactions for the dashboard widget
        $recentTransactions = Transaksi::with(['user', 'petani', 'pengepul']) // eager load simple relations
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('totalUsers', 'totalProducts', 'totalTransactions', 'recentTransactions'));
    }
}
