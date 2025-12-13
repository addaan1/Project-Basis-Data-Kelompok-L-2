<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengepulController;
use App\Http\Controllers\NegosiasiController;

Route::middleware(['auth', 'role:pengepul'])->group(function () {
    Route::resource('pengepul', PengepulController::class);
    
    // Negosiasi Actions
    Route::get('/pasar/{produk}/negosiasi', [NegosiasiController::class, 'create'])->name('negosiasi.create');
    Route::post('/pasar/{produk}/negosiasi', [NegosiasiController::class, 'store'])->name('negosiasi.store');
    Route::post('/negosiasi/{negosiasi}/counter', [NegosiasiController::class, 'counterOffer'])->name('negosiasi.counter');
});
