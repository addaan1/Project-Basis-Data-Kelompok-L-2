<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProdukBeras;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = ProdukBeras::with('petani')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        // Admin needs to select a Petani
        $petanis = User::where('peran', 'petani')->get();
        return view('admin.products.create', compact('petanis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'varietas' => 'required|string|max:255',
            'kualitas' => 'required|string|max:255',
            'harga_per_kg' => 'required|numeric|min:0',
            'lokasi_gudang' => 'required|string|max:255',
            'stok_kg' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_petani' => 'required|exists:users,id_user',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('produk_beras', 'public');
        }

        ProdukBeras::create([
            'nama_produk' => $request->nama_produk,
            'jenis_beras' => $request->varietas,
            'kualitas' => $request->kualitas,
            'harga' => $request->harga_per_kg,
            'id_petani' => $request->id_petani,
            'nama_petani' => User::find($request->id_petani)->nama ?? 'Unknown',
            'lokasi_gudang' => $request->lokasi_gudang,
            'stok' => $request->stok_kg,
            'deskripsi' => $request->deskripsi,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = ProdukBeras::findOrFail($id);
        $petanis = User::where('peran', 'petani')->get();
        return view('admin.products.edit', compact('product', 'petanis'));
    }

    public function update(Request $request, $id)
    {
        $product = ProdukBeras::findOrFail($id);
        
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'varietas' => 'required|string|max:255',
            'kualitas' => 'required|string|max:255',
            'harga_per_kg' => 'required|numeric|min:0',
            'lokasi_gudang' => 'required|string|max:255',
            'stok_kg' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_petani' => 'required|exists:users,id_user',
        ]);

        if ($request->hasFile('foto')) {
            if ($product->foto) {
                Storage::disk('public')->delete($product->foto);
            }
            $product->foto = $request->file('foto')->store('produk_beras', 'public');
        }

        $product->update([
            'nama_produk' => $request->nama_produk,
            'jenis_beras' => $request->varietas,
            'kualitas' => $request->kualitas,
            'harga' => $request->harga_per_kg,
            'id_petani' => $request->id_petani,
            'nama_petani' => User::find($request->id_petani)->nama ?? 'Unknown',
            'lokasi_gudang' => $request->lokasi_gudang,
            'stok' => $request->stok_kg,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = ProdukBeras::findOrFail($id);
        if ($product->foto) {
            Storage::disk('public')->delete($product->foto);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
