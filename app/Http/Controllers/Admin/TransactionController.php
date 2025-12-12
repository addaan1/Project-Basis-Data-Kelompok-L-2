<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaksi::with(['pembeli', 'penjual', 'pasar'])->latest()->paginate(10);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = Transaksi::with(['pembeli', 'penjual', 'pasar'])->findOrFail($id);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_transaksi' => 'required|in:pending,processed,completed,cancelled,failed',
        ]);

        $transaction = Transaksi::findOrFail($id);
        $transaction->update([
            'status_transaksi' => $request->status_transaksi,
        ]);

        return redirect()->route('admin.transactions.show', $id)->with('success', 'Transaction status updated successfully.');
    }
}
