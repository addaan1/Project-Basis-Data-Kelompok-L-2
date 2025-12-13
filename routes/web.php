<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PasarController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\NegosiasiController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/about-us', [WelcomeController::class, 'about'])->name('about');
Route::get('/how-it-works', [WelcomeController::class, 'howItWorks'])->name('how-it-works');
Route::get('/contact-us', function () { return view('contact-us'); })->name('contact-us');

// Market (Public/Auth)
Route::middleware(['auth'])->group(function () {
    Route::post('/market/{market}/buy', [MarketController::class, 'buy'])->name('market.buy');
    Route::post('/market/{market}/negotiate', [MarketController::class, 'negotiate'])->name('market.negotiate');
    Route::get('/market/seller/{id}', [MarketController::class, 'seller'])->name('market.seller');
    Route::resource('market', MarketController::class);
});

/*
|--------------------------------------------------------------------------
| Authenticated Application Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Common Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings & E-Wallet
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/background', [SettingsController::class, 'updateBackground'])->name('settings.updateBackground');
    Route::get('/saldo', [SettingsController::class, 'saldo'])->name('saldo');
    Route::post('/saldo/topup', [SettingsController::class, 'topupStore'])->name('saldo.topup.store');
    Route::post('/saldo/topup/{id}/confirm', [SettingsController::class, 'topupConfirm'])->name('saldo.topup.confirm');
    Route::get('/ewallet', [SettingsController::class, 'ewallet'])->name('ewallet');
    Route::post('/ewallet', [SettingsController::class, 'updateEwallet'])->name('ewallet.update');

    // Common Features
    Route::resource('distributor', DistributorController::class); // Assuming common or specific role needed
    Route::resource('inventory', InventoryController::class);
    
    // Transaksi (General)
    Route::resource('transaksi', TransaksiController::class);
    Route::get('/transaksi/notifications', [TransaksiController::class, 'notifications'])->name('transaksi.notifications');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Reporting
    Route::get('/report/export/pdf', [App\Http\Controllers\ReportController::class, 'exportPdf'])->name('report.export.pdf');
    Route::get('/report/export/csv', [App\Http\Controllers\ReportController::class, 'exportCsv'])->name('report.export.csv');

    // Pasar (General View)
    Route::get('/pasar', [PasarController::class, 'index'])->name('pasar.index');
    Route::get('/pasar/{produk}', [PasarController::class, 'show'])->name('pasar.show');
    Route::post('/pasar/{produk}/rate', [PasarController::class, 'rateProduct'])->name('pasar.rate');

    // Negosiasi (General List)
    Route::get('/negosiasi', [NegosiasiController::class, 'index'])->name('negosiasi.index');
    Route::get('/negosiasi/{negosiasi}', [NegosiasiController::class, 'show'])->name('negosiasi.show');

    // TopUp
    Route::resource('topup', TopUpController::class);
    Route::post('/topup/{topUp}/confirm', [TopUpController::class, 'confirm'])->name('topup.confirm');
});

/*
|--------------------------------------------------------------------------
| Role Specific Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/admin.php';
require __DIR__.'/petani.php';
require __DIR__.'/pengepul.php';

require __DIR__.'/auth.php';