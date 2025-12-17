<?php

namespace App\Http\Controllers;

use App\Models\TopUp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TopUpController extends Controller
{
    public function index()
    {
        if (auth()->user()->peran === 'admin') {
            $topUps = TopUp::orderBy('created_at', 'desc')->get();
        } else {
            $topUps = TopUp::where('user_id', auth()->user()->id_user)
                            ->orderBy('created_at', 'desc')
                            ->get();
        }
        
        return view('topup.index', compact('topUps'));
    }

    public function create()
    {
        return view('topup.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'payment_method' => 'required|in:bank,mini_market',
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $buktiPath = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
        }

        // Generate random reference code
        $referenceCode = strtoupper(Str::random(8));

        // Create top-up request
        $topUp = TopUp::create([
            'user_id' => auth()->user()->id_user,
            'amount' => $request->amount,
            'reference_code' => $referenceCode,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'bukti_transfer' => $buktiPath,
        ]);

        return redirect()->route('topup.show', $topUp->id)
                         ->with('success', 'Permintaan top-up berhasil dibuat.');
    }

    public function show($id)
    {
        // Explicitly fetch by ID to debug
        $topUp = TopUp::find($id);
        
        // Comprehensive debug logging
        \Log::info('=== TopUp Show Debug ===');
        \Log::info('Requested ID: ' . $id);
        \Log::info('TopUp Found: ' . ($topUp ? 'Yes' : 'No'));
        
        if ($topUp) {
            \Log::info('TopUp Data: ' . json_encode($topUp->toArray()));
            \Log::info('TopUp ID: ' . $topUp->id);
            \Log::info('TopUp Amount: ' . $topUp->amount);
            \Log::info('TopUp Reference: ' . $topUp->reference_code);
            \Log::info('TopUp Status: ' . $topUp->status);
            \Log::info('TopUp User ID: ' . $topUp->user_id);
        } else {
            \Log::error('TopUp record not found for ID: ' . $id);
            abort(404, 'Top-up record not found');
        }
        
        return view('topup.show', compact('topUp'));
    }

    public function confirm(TopUp $topUp)
    {
        // Only Admin can confirm
        if (auth()->user()->peran !== 'admin') {
            abort(403, 'Hanya Admin yang dapat memverifikasi Top Up.');
        }

        // Only pending top-ups can be confirmed
        if ($topUp->status !== 'pending') {
            return back()->with('error', 'Top-up ini tidak dapat dikonfirmasi.');
        }

        DB::beginTransaction();
        try {
            // Update top-up status
            $topUp->status = 'completed';
            $topUp->save();

            // Add amount to user's balance
            $user = User::find($topUp->user_id);
            $user->saldo += $topUp->amount;
            $user->save();

            DB::commit();

            return redirect()->route('topup.index')
                             ->with('success', 'Top-up berhasil dikonfirmasi dan saldo telah ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TopUp Confirm Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memverifikasi Top Up: ' . $e->getMessage());
        }
    }
}