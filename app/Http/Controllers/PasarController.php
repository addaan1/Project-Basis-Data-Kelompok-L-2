<?php

namespace App\Http\Controllers;

use App\Models\ProdukBeras;
use App\Models\User;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PasarController extends Controller
{
    public function index(Request $request)
    {
        $query = ProdukBeras::with('petani');

        // Filter berdasarkan kriteria
        if ($request->has('varietas')) {
            $query->where('varietas', 'like', '%' . $request->varietas . '%');
        }

        if ($request->has('kualitas')) {
            $query->where('kualitas', 'like', '%' . $request->kualitas . '%');
        }

        if ($request->has('lokasi')) {
            $query->where('lokasi_gudang', 'like', '%' . $request->lokasi . '%');
        }

        if ($request->has('harga_min')) {
            $query->where('harga_per_kg', '>=', $request->harga_min);
        }

        if ($request->has('harga_max')) {
            $query->where('harga_per_kg', '<=', $request->harga_max);
        }

        // Pencarian produk
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_produk', 'like', '%' . $request->search . '%')
                  ->orWhere('varietas', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        $produk = $query->paginate(10);

        return view('pasar.index', compact('produk'));
    }

    public function create()
    {
        // Hanya petani yang bisa membuat produk
        if (Auth::user()->peran !== 'petani') {
            return redirect()->route('pasar.index')
                ->with('error', 'Hanya petani yang dapat menambahkan produk');
        }

        return view('pasar.create');
    }

    public function store(Request $request)
    {
        // Validasi request
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'varietas' => 'required|string|max:255',
            'kualitas' => 'required|string|max:255',
            'harga_per_kg' => 'required|numeric|min:0',
            'lokasi_gudang' => 'required|string|max:255',
            'stok_kg' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload foto jika ada
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('produk_beras', 'public');
        }

        // Buat produk baru
        ProdukBeras::create([
            'nama_produk' => $request->nama_produk,
            'varietas' => $request->varietas,
            'kualitas' => $request->kualitas,
            'harga_per_kg' => $request->harga_per_kg,
            'id_petani' => Auth::id(),
            'lokasi_gudang' => $request->lokasi_gudang,
            'stok_kg' => $request->stok_kg,
            'deskripsi' => $request->deskripsi,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('pasar.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function show(ProdukBeras $produk)
    {
        $produk->load('petani', 'rating');
        $avgRating = $produk->rating()->avg('nilai_rating') ?? 0;
        
        return view('pasar.show', compact('produk', 'avgRating'));
    }

    public function edit(ProdukBeras $produk)
    {
        // Hanya pemilik produk yang bisa mengedit
        if (Auth::id() !== $produk->id_petani) {
            return redirect()->route('pasar.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit produk ini');
        }

        return view('pasar.edit', compact('produk'));
    }

    public function update(Request $request, ProdukBeras $produk)
    {
        // Validasi request
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'varietas' => 'required|string|max:255',
            'kualitas' => 'required|string|max:255',
            'harga_per_kg' => 'required|numeric|min:0',
            'lokasi_gudang' => 'required|string|max:255',
            'stok_kg' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($produk->foto) {
                Storage::disk('public')->delete($produk->foto);
            }
            $fotoPath = $request->file('foto')->store('produk_beras', 'public');
            $produk->foto = $fotoPath;
        }

        // Update produk
        $produk->update([
            'nama_produk' => $request->nama_produk,
            'varietas' => $request->varietas,
            'kualitas' => $request->kualitas,
            'harga_per_kg' => $request->harga_per_kg,
            'lokasi_gudang' => $request->lokasi_gudang,
            'stok_kg' => $request->stok_kg,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('pasar.show', $produk)
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(ProdukBeras $produk)
    {
        // Hanya pemilik produk yang bisa menghapus
        if (Auth::id() !== $produk->id_petani) {
            return redirect()->route('pasar.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus produk ini');
        }

        // Hapus foto jika ada
        if ($produk->foto) {
            Storage::disk('public')->delete($produk->foto);
        }

        // Kembalikan Stok ke Gudang (Inventory)
        if ($produk->stok_kg > 0) {
            $inventory = \App\Models\Inventory::where('id_user', Auth::id())
                ->where('jenis_beras', $produk->varietas) // Mapping varietas -> jenis_beras
                ->where('kualitas', $produk->kualitas)
                ->first();

            if ($inventory) {
                $inventory->increment('jumlah', $produk->stok_kg);
            } else {
                \App\Models\Inventory::create([
                    'id_user' => Auth::id(),
                    'jenis_beras' => $produk->varietas,
                    'kualitas' => $produk->kualitas,
                    'jumlah' => $produk->stok_kg,
                    'tanggal_masuk' => now(),
                    'keterangan' => 'Pengembalian dari Etalase: ' . $produk->nama_produk,
                ]);
            }
        }

        $produk->delete();

        return redirect()->route('pasar.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    public function rateProduct(Request $request, ProdukBeras $produk)
    {
        $request->validate([
            'nilai_rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
        ]);

        // Cek apakah user sudah pernah memberi rating
        $existingRating = Rating::where('id_user', Auth::id())
            ->where('id_produk', $produk->id)
            ->first();

        if ($existingRating) {
            // Update rating yang sudah ada
            $existingRating->update([
                'nilai_rating' => $request->nilai_rating,
                'komentar' => $request->komentar,
            ]);
        } else {
            // Buat rating baru
            Rating::create([
                'id_user' => Auth::id(),
                'id_produk' => $produk->id,
                'id_penjual' => $produk->id_petani,
                'nilai_rating' => $request->nilai_rating,
                'komentar' => $request->komentar,
            ]);
        }

        return redirect()->back()->with('success', 'Rating berhasil diberikan');
    }
}