<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetaniController;
use App\Http\Controllers\ProdukBerasController;
use App\Http\Controllers\PasarController;
use App\Http\Controllers\NegosiasiController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\InventoryController;

Route::middleware(['auth', 'role:petani'])->group(function () {
    Route::resource('petani', PetaniController::class);
    Route::resource('inventory', InventoryController::class);
    Route::resource('produk', ProdukBerasController::class);

    // Pasar Management (Petani manages their market items)
    Route::get('/pasar/create', [PasarController::class, 'create'])->name('pasar.create');
    Route::post('/pasar', [PasarController::class, 'store'])->name('pasar.store');
    Route::get('/pasar/{produk}/edit', [PasarController::class, 'edit'])->name('pasar.edit');
    Route::put('/pasar/{produk}', [PasarController::class, 'update'])->name('pasar.update');
    Route::delete('/pasar/{produk}', [PasarController::class, 'destroy'])->name('pasar.destroy');

    // Negosiasi Decisions
    Route::post('/negosiasi/{negosiasi}/accept', [NegosiasiController::class, 'accept'])->name('negosiasi.accept');
    Route::post('/negosiasi/{negosiasi}/reject', [NegosiasiController::class, 'reject'])->name('negosiasi.reject');
    
    // Transaction Validations
    Route::post('/transaksi/{transaksi}/approve', [TransaksiController::class, 'approve'])->name('transaksi.approve');
    Route::post('/transaksi/{transaksi}/reject', [TransaksiController::class, 'reject'])->name('transaksi.reject');
    Route::get('/transaksi/{transaksi}/history', [TransaksiController::class, 'history'])->name('transaksi.history');
});
