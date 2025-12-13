<?php

namespace App\Http\Controllers;

use App\Models\Negosiasi;
use App\Models\ProdukBeras;
use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OfferCreated;

class NegosiasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->peran === 'petani') {
            // Petani melihat negosiasi yang masuk
            $negosiasi = Negosiasi::with(['produk', 'pengepul'])
                ->where('id_petani', $user->id_user)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Pengepul melihat negosiasi yang diajukan
            $negosiasi = Negosiasi::with(['produk', 'petani'])
                ->where('id_pengepul', $user->id_user)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        // kirim ke view dengan nama $negotiations
        return view('negosiasi.index', [
            'negotiations' => $negosiasi
        ]);
    }
    
    public function create(ProdukBeras $produk)
    {
        // Hanya pengepul yang bisa melakukan negosiasi
        if (Auth::user()->peran !== 'pengepul') {
            return redirect()->route('pasar.show', $produk)
                ->with('error', 'Hanya pengepul yang dapat melakukan negosiasi');
        }
        
        return view('negosiasi.create', compact('produk'));
    }
    
    public function store(Request $request, ProdukBeras $produk)
    {
        // Validasi request
        $request->validate([
            'harga_penawaran' => 'required|numeric|min:1',
            'jumlah_kg' => 'required|integer|min:1|max:' . $produk->stok, // Fix: stok_kg -> stok
            'pesan' => 'nullable|string',
        ]);
        
        // Buat negosiasi baru
        $negosiasi = Negosiasi::create([
            'id_produk' => $produk->id_produk, // Fix: id -> id_produk if model uses id_produk
            'id_pengepul' => Auth::id(),
            'id_petani' => $produk->id_petani,
            'harga_penawaran' => $request->harga_penawaran,
            'harga_awal' => $produk->harga, // Fix: harga_per_kg -> harga
            'jumlah_kg' => $request->jumlah_kg,
            'pesan' => $request->pesan,
            'status' => 'dalam_proses',
        ]);

        // Kirim Notifikasi ke Petani
        $petani = User::find($produk->id_petani);
        if ($petani) {
            Notification::send($petani, new OfferCreated($negosiasi));
        }
        
        return redirect()->route('negosiasi.index')
            ->with('success', 'Penawaran berhasil diajukan');
    }
    
    public function show(Negosiasi $negosiasi)
    {
        $negosiasi->load(['produk', 'petani', 'pengepul']);
        
        // Cek apakah user adalah pihak dalam negosiasi
        $user = Auth::user();
        if ((int)$user->id_user !== (int)$negosiasi->id_petani && (int)$user->id_user !== (int)$negosiasi->id_pengepul) {
            return redirect()->route('negosiasi.index')
                ->with('error', 'Anda tidak memiliki akses untuk melihat negosiasi ini');
        }
        
        return view('negosiasi.show', compact('negosiasi'));
    }
    
    public function accept(Negosiasi $negosiasi)
    {
        // Hanya petani yang bisa menerima negosiasi
        if ((int)Auth::id() !== (int)$negosiasi->id_petani) {
            return redirect()->route('negosiasi.index')
                ->with('error', 'Hanya petani yang dapat menerima negosiasi');
        }
        
        // Cek status negosiasi
        if ($negosiasi->status !== 'dalam_proses') {
            return redirect()->route('negosiasi.show', $negosiasi)
                ->with('error', 'Negosiasi ini sudah tidak dalam proses');
        }
        
        // Cek stok produk
        $produk = $negosiasi->produk;
        if ($produk->stok < $negosiasi->jumlah_kg) {
            return redirect()->route('negosiasi.show', $negosiasi)
                ->with('error', 'Stok produk tidak mencukupi untuk memenuhi negosiasi ini.');
        }

        // Cek saldo pembeli (pengepul)
        $buyer = User::find($negosiasi->id_pengepul);
        $totalHarga = $negosiasi->harga_penawaran * $negosiasi->jumlah_kg;

        if (!$buyer || $buyer->saldo < $totalHarga) {
            return redirect()->route('negosiasi.show', $negosiasi)
                ->with('error', 'Saldo pembeli sudah tidak mencukupi untuk transaksi ini.');
        }
        
        DB::beginTransaction();
        try {
            // Update status negosiasi
            $negosiasi->update(['status' => 'diterima']);
            
            // Kurangi stok produk
            $produk->decrement('stok', $negosiasi->jumlah_kg);

            // Proses Keuangan
            // 1. Kurangi saldo pembeli
            $buyer->decrement('saldo', $totalHarga);

            // 2. Tambah saldo penjual (petani)
            $seller = Auth::user();
            $seller->increment('saldo', $totalHarga);
            
            // 3. Catat Pengeluaran Pembeli
            \App\Models\Expenditure::create([
                'user_id' => $buyer->id_user,
                'amount' => $totalHarga,
                'description' => 'Pembelian via Negosiasi #' . $negosiasi->id,
                'date' => now(),
                'status' => 'completed',
            ]);

            // Buat transaksi (Langsung Disetujui/Selesai karena sudah dibayar)
            $trx = Transaksi::create([
                'id_pembeli' => $negosiasi->id_pengepul,
                'id_penjual' => $negosiasi->id_petani,
                'id_produk' => $negosiasi->id_produk,
                'jumlah' => $negosiasi->jumlah_kg,
                'harga_awalan' => $negosiasi->harga_awal,
                'harga_akhir' => $negosiasi->harga_penawaran, // Harga deal
                'tanggal' => now(),
                'jenis_transaksi' => 'jual', 
                'status_transaksi' => 'disetujui', 
                'type' => 'purchase',
                'description' => 'Pembelian produk melalui negosiasi (Diterima)',
                'user_id' => $buyer->id_user,
            ]);
            
            DB::commit();
            
            return redirect()->route('negosiasi.show', $negosiasi)
                ->with('success', 'Negosiasi diterima. Transaksi berhasil dibuat dan saldo diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('negosiasi.show', $negosiasi)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function reject(Negosiasi $negosiasi)
    {
        // Hanya petani yang bisa menolak negosiasi
        if ((int)Auth::id() !== (int)$negosiasi->id_petani) {
            return redirect()->route('negosiasi.index')
                ->with('error', 'Hanya petani yang dapat menolak negosiasi');
        }
        
        // Cek status negosiasi
        if ($negosiasi->status !== 'dalam_proses') {
            return redirect()->route('negosiasi.show', $negosiasi)
                ->with('error', 'Negosiasi ini sudah tidak dalam proses');
        }
        
        // Update status negosiasi
        $negosiasi->update(['status' => 'ditolak']);
        
        return redirect()->route('negosiasi.show', $negosiasi)
            ->with('success', 'Negosiasi ditolak');
    }
    
    public function counterOffer(Request $request, Negosiasi $negosiasi)
    {
        // Validasi request
        $request->validate([
            'harga_penawaran' => 'required|numeric|min:1',
            'pesan' => 'nullable|string',
        ]);
        
        // Cek status negosiasi
        if ($negosiasi->status !== 'dalam_proses') {
            return redirect()->route('negosiasi.show', $negosiasi)
                ->with('error', 'Negosiasi ini sudah tidak dalam proses');
        }
        
        // Update negosiasi dengan penawaran baru
        $negosiasi->update([
            'harga_penawaran' => $request->harga_penawaran,
            'pesan' => $request->pesan,
        ]);
        
        return redirect()->route('negosiasi.show', $negosiasi)
            ->with('success', 'Penawaran balik berhasil diajukan');
    }
}