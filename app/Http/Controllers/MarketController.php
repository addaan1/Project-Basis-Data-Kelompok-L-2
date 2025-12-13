<?php

namespace App\Http\Controllers;

use App\Models\ProdukBeras;
use App\Models\Transaksi;
use App\Models\TransaksiHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MarketController extends Controller
{
    public function index()
    {
        $products = ProdukBeras::paginate(12);
        return view('market.index', compact('products'));
    }

    public function show(ProdukBeras $market)
    {
        $product = $market;
        return view('market.show', compact('product'));
    }

    public function create()
    {
        return view('market.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jenis_beras' => 'required|string|max:255',
            'kualitas' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'lokasi_gudang' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('produk', 'public');
        }

        $inventory = \App\Models\Inventory::where('id_user', Auth::id())
            ->where('jenis_beras', $request->jenis_beras)
            ->where('kualitas', $request->kualitas)
            ->first();

        // Check if inventory exists and has enough stock
        if (!$inventory || $inventory->jumlah < $request->stok) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Stok gudang tidak mencukupi untuk jenis beras dan kualitas ini. Stok tersedia: ' . ($inventory ? $inventory->jumlah : 0) . ' kg.');
        }

        // Deduct from Inventory (Move to Market Etalase)
        $inventory->decrement('jumlah', $request->stok);

        ProdukBeras::create([
            'nama_produk' => $request->nama_produk,
            'jenis_beras' => $request->jenis_beras,
            'kualitas' => $request->kualitas,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'nama_petani' => Auth::user()->nama,
            'lokasi_gudang' => $request->lokasi_gudang,
            'id_petani' => Auth::id(),
            'foto' => $fotoPath,
        ]);

        return redirect()->route('market.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $product = ProdukBeras::findOrFail($id);
        return view('market.edit', compact('product'));
    }
    public function buy(Request $request, ProdukBeras $market)
    {
        try {
            $product = $market;
            $request->validate([
                'jumlah' => 'required|integer|min:1|max:'.$product->stok,
            ]);

            DB::beginTransaction();

            $buyer = Auth::user();
            $buyerId = $buyer->id_user;
            $sellerId = $product->id_petani;
            $jumlah = (int) $request->input('jumlah');
            $hargaSatuan = (float) $product->harga;
            $total = $hargaSatuan * $jumlah;

            if ((float) ($buyer->saldo ?? 0) < $total) {
                throw new \RuntimeException('Saldo tidak mencukupi untuk melakukan pembelian ini');
            }

            $trx = Transaksi::create([
                'id_penjual' => $sellerId,
                'id_pembeli' => $buyerId,
                'id_produk' => $product->id_produk,
                'jumlah' => $jumlah,
                'harga_awalan' => $hargaSatuan,
                'harga_akhir' => $hargaSatuan,
                'tanggal' => now()->toDateString(),
                'jenis_transaksi' => 'beli',
                'status_transaksi' => 'menunggu_pembayaran',
                'type' => 'purchase',
                'description' => 'Pembelian langsung produk beras: '.$product->nama_produk,
                'user_id' => $buyerId,
            ]);

            \App\Models\Expenditure::create([
                'user_id' => $buyerId,
                'amount' => $total,
                'description' => 'Hold transaksi #'.$trx->id_transaksi,
                'date' => now()->toDateString(),
                'status' => 'pending',
            ]);

            $buyer->saldo = (float) $buyer->saldo - $total; // hold saldo
            $buyer->save();

            // Potong Stok Langsung (Reserve Stock)
            $product->decrement('stok', $jumlah);

            TransaksiHistory::create([
                'id_transaksi' => $trx->id_transaksi,
                'status_before' => null,
                'status_after' => 'menunggu_pembayaran',
                'changed_by' => $buyerId,
                'note' => 'Pembelian dibuat, saldo buyer di-hold',
            ]);

            DB::commit();

            return redirect()->route('market.show', ['market' => $product->id_produk])
                ->with('status', 'Pembelian berhasil dibuat, menunggu konfirmasi penjual');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Market buy error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('market.show', ['market' => $market->id_produk])->with('error', 'Gagal membuat pembelian: '.$e->getMessage());
        }
    }

    public function negotiate(Request $request, ProdukBeras $market)
    {
        try {
            $product = $market;
            $request->validate([
                'tawaran_harga' => 'required|numeric|min:1',
                'jumlah' => 'nullable|integer|min:1|max:'.$product->stok,
                'pesan' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $buyerId = Auth::user()->id_user;
            $sellerId = $product->id_petani;
            $jumlah = (int) ($request->input('jumlah') ?? 0);
            $offer = (float) $request->input('tawaran_harga');
            $message = (string) $request->input('pesan', '');

            // Use the standard Negosiasi Model
            \App\Models\Negosiasi::create([
                'id_produk' => $product->id_produk,
                'id_pengepul' => $buyerId,
                'id_petani' => $sellerId,
                'harga_penawaran' => $offer,
                'harga_awal' => $product->harga, // Save initial price
                'jumlah_kg' => $jumlah,
                'pesan' => $message,
                'status' => 'dalam_proses',
            ]);

            DB::commit();

            return redirect()->route('market.show', ['market' => $product->id_produk])
                ->with('status', 'Tawaran negosiasi telah dikirim! Cek menu Negosiasi untuk memantau.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Market negotiate error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('market.index')->with('error', 'Gagal mengirim negosiasi: '.$e->getMessage());
        }
    }

    public function seller($id)
    {
        $seller = \App\Models\User::findOrFail($id);
        
        // Only allow viewing profiles of sellers (Petani/Pengepul/Pasar)
        if (!in_array($seller->peran, ['petani', 'pengepul', 'pasar', 'distributor', 'admin'])) {
             // In a real app we might restrict this, for now allow all involved in trade
        }

        $products = ProdukBeras::where('id_petani', $id)->latest()->get();
        return view('market.seller', compact('seller', 'products'));
    }
}