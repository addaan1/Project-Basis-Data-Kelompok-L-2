<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventories = Inventory::where('id_user', Auth::id())->latest()->get();
        return view('inventory.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_beras' => 'required|string|max:255',
            'kualitas' => 'required|string|in:Premium,Medium,Standard',
            'jumlah' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image', // Future proofing
        ]);

        // Check if existing inventory for this type/quality exists, if so check if we should merge or create new?
        // For simplicity now, we create new batch or merge if exact same batch logic needed.
        // Let's Merge for simpler UX -> "Total Inventory of Pandan Wangi"
        
        $existing = Inventory::where('id_user', Auth::id())
            ->where('jenis_beras', $request->jenis_beras)
            ->where('kualitas', $request->kualitas)
            ->first();

        if ($existing) {
            $existing->increment('jumlah', $request->jumlah);
            // Update timestamp maybe?
        } else {
            Inventory::create([
                'id_user' => Auth::id(),
                'jenis_beras' => $request->jenis_beras,
                'kualitas' => $request->kualitas,
                'jumlah' => $request->jumlah,
                'tanggal_masuk' => $request->tanggal_masuk,
                'keterangan' => $request->keterangan,
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Stok berhasil ditambahkan ke gudang.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $inventory = Inventory::where('id_user', Auth::id())->findOrFail($id);
        $inventory->delete();

        return redirect()->route('inventory.index')->with('success', 'Item inventaris dihapus.');
    }
}