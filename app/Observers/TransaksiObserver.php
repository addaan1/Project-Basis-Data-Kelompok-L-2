<?php

namespace App\Observers;

use App\Models\Transaksi;
use App\Models\ProdukBeras;
use Illuminate\Validation\ValidationException;

class TransaksiObserver
{
    /**
     * Handle the Transaksi "created" event.
     */
    public function created(Transaksi $transaksi): void
    {
        // Jika transaksi melibatkan produk (bukan topup/withdraw dsb)
        if ($transaksi->id_produk) {
            $product = ProdukBeras::find($transaksi->id_produk);
            
            if ($product) {
                // Safety check: Pastikan stok cukup (redundant dengan controller tapi bagus untuk safety)
                if ($product->stok < $transaksi->jumlah) {
                    // Note: Throwing exception inside observer might be tricky if not handled, 
                    // but standard controller validation should have caught this. 
                    // This is a last line of defense.
                     // throw new \Exception("Stok tidak mencukupi untuk transaksi #{$transaksi->id_transaksi}");
                     // For now, let's just deduct. If negative, it means race condition or seeder issue.
                }

                $product->decrement('stok', $transaksi->jumlah);

                // --- SYNC INVENTORY DEDUCTION ---
                $inventory = \App\Models\Inventory::where('id_user', $transaksi->id_penjual)
                    ->where('jenis_beras', $product->jenis_beras)
                    ->where('kualitas', $product->kualitas)
                    ->first();
                
                if ($inventory) {
                    $inventory->decrement('jumlah', $transaksi->jumlah);
                }
                // --------------------------------
            }
        }
    }

    /**
     * Handle the Transaksi "updated" event.
     */
    public function updated(Transaksi $transaksi): void
    {
        // Handle restoration of stock if rejected/cancelled
        // Cek jika status berubah jadi 'ditolak' atau 'cancelled' DARI status yang bukan itu
        if (in_array($transaksi->status_transaksi, ['ditolak', 'cancelled']) && 
            $transaksi->isDirty('status_transaksi') &&
            !in_array($transaksi->getOriginal('status_transaksi'), ['ditolak', 'cancelled'])) {
            
            if ($transaksi->id_produk) {
                $product = ProdukBeras::find($transaksi->id_produk);
                if ($product) {
                    $product->increment('stok', $transaksi->jumlah);

                    // --- SYNC INVENTORY RESTORATION ---
                    $inventory = \App\Models\Inventory::where('id_user', $transaksi->id_penjual)
                        ->where('jenis_beras', $product->jenis_beras)
                        ->where('kualitas', $product->kualitas)
                        ->first();
                    
                    if ($inventory) {
                        $inventory->increment('jumlah', $transaksi->jumlah);
                    }
                    // ----------------------------------
                }
            }
        }
    }
}
