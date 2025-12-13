<?php

namespace App\Http\Controllers;

use App\Models\TopUp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('settings', compact('user'));
    }

    public function saldo()
    {
        $user = auth()->user();
        $saldo = $user->saldo;
        $pendingTopUp = TopUp::where('user_id', $user->id_user)->where('status', 'pending')->first();
        return view('saldo', compact('saldo', 'pendingTopUp'));
    }

    public function topupStore(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'payment_method' => 'required|in:bank,mini_market',
        ]);

        // Check if user has pending topup
        $pending = TopUp::where('user_id', auth()->user()->id_user)->where('status', 'pending')->first();
        if ($pending) {
            return back()->with('error', 'Anda memiliki topup yang belum selesai.');
        }

        // Generate random reference code
        $referenceCode = strtoupper(Str::random(8));

        // Create top-up request
        TopUp::create([
            'user_id' => auth()->user()->id_user,
            'amount' => $request->amount,
            'reference_code' => $referenceCode,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
        ]);

        return back()->with('success', 'Permintaan top-up berhasil dibuat.');
    }

    public function topupConfirm($id)
    {
        $topUp = TopUp::findOrFail($id);
        if ($topUp->user_id !== auth()->user()->id_user) {
            abort(403);
        }

        if ($topUp->status !== 'pending') {
            return back()->with('error', 'Top-up ini tidak dapat dikonfirmasi.');
        }

        $topUp->status = 'completed';
        $topUp->save();

        $user = User::find($topUp->user_id);
        $user->saldo += $topUp->amount;
        $user->save();

        return back()->with('success', 'Top-up berhasil dikonfirmasi dan saldo telah ditambahkan.');
    }

    public function ewallet()
    {
        $user = auth()->user();
        return view('ewallet', compact('user'));
    }

    public function updateEwallet(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|numeric',
            'account_name' => 'required|string',
        ]);

        $user = auth()->user();
        $user->update([
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
        ]);

        return redirect()->back()->with('success', 'Pengaturan E-Wallet berhasil disimpan.');
    }

    // Anda bisa menambahkan metode updateBackground di sini jika diperlukan
    public function updateBackground(Request $request)
    {
        // Logika untuk memperbarui background
        return redirect()->back()->with('success', 'Background berhasil diperbarui!');
    }
}
