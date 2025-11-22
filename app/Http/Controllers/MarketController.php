<?php

namespace App\Http\Controllers;

use App\Models\ProdukBeras;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MarketController extends Controller
{
    public function index()
    {
        $products = ProdukBeras::all();
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
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'nama_petani' => 'required|string|max:255',
            'lokasi_gudang' => 'required|string|max:255',
        ]);

        ProdukBeras::create([
            'nama_produk' => $request->nama_produk,
            'jenis_beras' => $request->jenis_beras,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'nama_petani' => $request->nama_petani,
            'lokasi_gudang' => $request->lokasi_gudang,
            'id_user' => auth()->id(), // Asumsi produk dibuat oleh user yang sedang login
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

            $buyerId = Auth::user()->id_user;
            $sellerId = $product->id_user;
            $jumlah = (int) $request->input('jumlah');
            $hargaSatuan = (float) $product->harga;

            $trx = Transaksi::create([
                'id_penjual' => $sellerId,
                'id_pembeli' => $buyerId,
                'jumlah' => $jumlah,
                'harga_awalan' => $hargaSatuan,
                'harga_akhir' => $hargaSatuan,
                'tanggal' => now()->toDateString(),
                'jenis_transaksi' => 'market_purchase',
                'status_transaksi' => 'menunggu_pembayaran',
                'type' => 'purchase',
                'description' => 'Pembelian langsung produk beras',
                'user_id' => $buyerId,
            ]);

            $product->decrement('stok', $jumlah);

            DB::commit();

            return redirect()->route('market.show', $product->id_produk)
                ->with('status', 'Pembelian berhasil dibuat, menunggu konfirmasi!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Market buy error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('market.index')->with('error', 'Gagal membuat pembelian: '.$e->getMessage());
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
            $sellerId = $product->id_user;
            $jumlah = (int) ($request->input('jumlah') ?? 0);
            $offer = (float) $request->input('tawaran_harga');
            $message = (string) $request->input('pesan', '');

            $trx = Transaksi::create([
                'id_penjual' => $sellerId,
                'id_pembeli' => $buyerId,
                'jumlah' => $jumlah,
                'harga_awalan' => (float) $product->harga,
                'harga_akhir' => $offer,
                'tanggal' => now()->toDateString(),
                'jenis_transaksi' => 'market_negotiation',
                'status_transaksi' => 'dalam_proses',
                'type' => 'purchase',
                'description' => 'Negosiasi harga: '.($message ?: '-'),
                'user_id' => $buyerId,
            ]);

            DB::table('negosiasi_hargas')->insert([
                'id_transaksi' => $trx->id_transaksi,
                'id_user_penawar' => $buyerId,
                'harga_tawaran' => $offer,
                'catatan' => $message,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('market.show', $product->id_produk)
                ->with('status', 'Tawaran negosiasi telah dikirim dan sedang diproses');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Market negotiate error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('market.index')->with('error', 'Gagal mengirim negosiasi: '.$e->getMessage());
        }
    }
}