<?php

use Illuminate\Support\Facades\Route;
// Controller dari Breeze
use App\Http\Controllers\ProfileController;
// Controller Kustom Kita
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PetaniController;
use App\Http\Controllers\PengepulController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\PasarController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\NegosiasiController;
use App\Http\Controllers\TopUpController;

/*
|--------------------------------------------------------------------------
| Rute Publik (Guest)
|--------------------------------------------------------------------------
| Rute yang bisa diakses siapa saja tanpa perlu login.
*/
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/about-us', [WelcomeController::class, 'about'])->name('about');
Route::middleware(['auth'])->group(function () {
    Route::post('/market/{market}/buy', [MarketController::class, 'buy'])->name('market.buy');
    Route::post('/market/{market}/negotiate', [MarketController::class, 'negotiate'])->name('market.negotiate');
    Route::get('/market/seller/{id}', [MarketController::class, 'seller'])->name('market.seller');
    Route::resource('market', MarketController::class);
});
Route::get('/how-it-works', [WelcomeController::class, 'howItWorks'])->name('how-it-works');
Route::get('/contact-us', function () {
    return view('contact-us');
})->name('contact-us');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::resource('transactions', \App\Http\Controllers\Admin\TransactionController::class)->only(['index', 'show', 'update']);
});


/*
|--------------------------------------------------------------------------
| Rute Aplikasi Internal (Butuh Login)
|--------------------------------------------------------------------------
| Semua rute di sini dilindungi oleh middleware 'auth', artinya
| pengguna harus login untuk bisa mengaksesnya.
*/
Route::prefix('app')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    // Rute untuk Fitur Dashboard
    Route::get('/saldo', [SettingsController::class, 'saldo'])->name('saldo');
    Route::post('/saldo/topup', [SettingsController::class, 'topupStore'])->name('saldo.topup.store');
    Route::post('/saldo/topup/{id}/confirm', [SettingsController::class, 'topupConfirm'])->name('saldo.topup.confirm');
    Route::get('/negosiasi', [NegosiasiController::class, 'index'])->name('negosiasi');
    Route::get('/ewallet', [SettingsController::class, 'ewallet'])->name('ewallet');

    // Rute Profil dari Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute untuk Pengaturan
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/background', [SettingsController::class, 'updateBackground'])->name('settings.updateBackground');

    // Rute Resource untuk semua modul CRUD kita
    Route::resource('petani', PetaniController::class);
    Route::resource('pengepul', PengepulController::class);
    Route::resource('distributor', DistributorController::class);
    Route::resource('pasar', PasarController::class);
    Route::resource('inventory', InventoryController::class);
    Route::resource('transaksi', TransaksiController::class);
    Route::middleware(['role:petani'])->group(function () {
        Route::post('/transaksi/{transaksi}/approve', [TransaksiController::class, 'approve'])->name('transaksi.approve');
        Route::post('/transaksi/{transaksi}/reject', [TransaksiController::class, 'reject'])->name('transaksi.reject');
        Route::get('/transaksi/{transaksi}/history', [TransaksiController::class, 'history'])->name('transaksi.history');
    });
    Route::get('/transaksi/notifications', [TransaksiController::class, 'notifications'])->name('transaksi.notifications');
});


/*
|--------------------------------------------------------------------------
| Rute Otentikasi (Login, Register, dll.)
|--------------------------------------------------------------------------
| File ini berisi semua rute yang dibuat oleh Breeze untuk
| menangani proses login, registrasi, lupa password, dll.
*/
require __DIR__.'/auth.php';


// Tambahkan route berikut di file routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::resource('produk', ProdukBerasController::class);
});

// Route untuk pasar digital
Route::middleware(['auth'])->group(function () {
    // Pasar Digital
    Route::get('/pasar', [PasarController::class, 'index'])->name('pasar.index');
    
    // Route khusus petani
    Route::middleware(['role:petani'])->group(function () {
        Route::get('/pasar/create', [PasarController::class, 'create'])->name('pasar.create');
        Route::post('/pasar', [PasarController::class, 'store'])->name('pasar.store');
        Route::get('/pasar/{produk}/edit', [PasarController::class, 'edit'])->name('pasar.edit');
        Route::put('/pasar/{produk}', [PasarController::class, 'update'])->name('pasar.update');
        Route::delete('/pasar/{produk}', [PasarController::class, 'destroy'])->name('pasar.destroy');
    });
    
    // Route untuk semua user
    Route::get('/pasar/{produk}', [PasarController::class, 'show'])->name('pasar.show');
    Route::post('/pasar/{produk}/rate', [PasarController::class, 'rateProduct'])->name('pasar.rate');
    
    // Negosiasi
    Route::get('/negosiasi', [NegosiasiController::class, 'index'])->name('negosiasi.index');
    Route::get('/negosiasi/{negosiasi}', [NegosiasiController::class, 'show'])->name('negosiasi.show');
    
    // Route khusus pengepul
    Route::middleware(['role:pengepul'])->group(function () {
        Route::get('/pasar/{produk}/negosiasi', [NegosiasiController::class, 'create'])->name('negosiasi.create');
        Route::post('/pasar/{produk}/negosiasi', [NegosiasiController::class, 'store'])->name('negosiasi.store');
        Route::post('/negosiasi/{negosiasi}/counter', [NegosiasiController::class, 'counterOffer'])->name('negosiasi.counter');
    });
    
    // Route khusus petani
    Route::middleware(['role:petani'])->group(function () {
        Route::post('/negosiasi/{negosiasi}/accept', [NegosiasiController::class, 'accept'])->name('negosiasi.accept');
        Route::post('/negosiasi/{negosiasi}/reject', [NegosiasiController::class, 'reject'])->name('negosiasi.reject');
    });
});
Route::middleware('auth')->group(function () {
    Route::get('/topup', [TopUpController::class, 'index'])->name('topup.index');
    Route::get('/topup/create', [TopUpController::class, 'create'])->name('topup.create');
    Route::post('/topup', [TopUpController::class, 'store'])->name('topup.store');
    Route::get('/topup/{topUp}', [TopUpController::class, 'show'])->name('topup.show');
    Route::post('/topup/{topUp}/confirm', [TopUpController::class, 'confirm'])->name('topup.confirm');
});

// ini faiz