<?php

namespace App\Http\Controllers;

use App\Models\ProdukBeras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProdukBerasController extends Controller
{
    /**
     * Menampilkan daftar produk beras
     */
    public function index()
    {
        $produkBeras = ProdukBeras::all();
        return view('produk.index', compact('produkBeras'));
    }

    /**
     * Menampilkan form untuk membuat produk baru
     */
    public function create()
    {
        return view('produk.create');
    }

    /**
     * Menyimpan produk beras baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jenis_beras' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'nama_petani' => 'required|string|max:255',
            'lokasi_gudang' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();
        $data['id_petani'] = Auth::id();

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoName = time() . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/produk', $fotoName);
            $data['foto'] = 'produk/' . $fotoName;
        }

        ProdukBeras::create($data);

        return redirect()->route('produk.index')
            ->with('success', 'Produk beras berhasil ditambahkan');
    }

    /**
     * Menampilkan detail produk beras
     */
    public function show(ProdukBeras $produk)
    {
        return view('produk.show', compact('produk'));
    }

    /**
     * Menampilkan form untuk mengedit produk
     */
    public function edit(ProdukBeras $produk)
    {
        return view('produk.edit', compact('produk'));
    }

    /**
     * Update produk beras
     */
    public function update(Request $request, ProdukBeras $produk)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jenis_beras' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'nama_petani' => 'required|string|max:255',
            'lokasi_gudang' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($produk->foto) {
                Storage::delete('public/' . $produk->foto);
            }
            
            $foto = $request->file('foto');
            $fotoName = time() . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/produk', $fotoName);
            $data['foto'] = 'produk/' . $fotoName;
        }

        $produk->update($data);

        return redirect()->route('produk.index')
            ->with('success', 'Produk beras berhasil diperbarui');
    }

    /**
     * Hapus produk beras
     */
    public function destroy(ProdukBeras $produk)
    {
        // Hapus foto jika ada
        if ($produk->foto) {
            Storage::delete('public/' . $produk->foto);
        }
        
        $produk->delete();

        return redirect()->route('produk.index')
            ->with('success', 'Produk beras berhasil dihapus');
    }
}