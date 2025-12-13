<?php

namespace App\Http\Controllers;

use App\Models\TopUp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TopUpController extends Controller
{
    public function index()
    {
        $topUps = TopUp::where('user_id', auth()->user()->id_user)
                        ->orderBy('created_at', 'desc')
                        ->get();
        
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
        ]);

        // Generate random reference code
        $referenceCode = strtoupper(Str::random(8));

        // Create top-up request
        $topUp = TopUp::create([
            'user_id' => auth()->user()->id_user,
            'amount' => $request->amount,
            'reference_code' => $referenceCode,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('topup.show', $topUp->id)
                         ->with('success', 'Permintaan top-up berhasil dibuat.');
    }

    public function show(TopUp $topUp)
    {
        
        // DEBUG V2
        $tId = $topUp->user_id;
        $aId = auth()->user()->id_user;
        \Illuminate\Support\Facades\Log::info("TopUp 9 Debug: TopUpOwner [{$tId}] (".gettype($tId).") vs AuthUser [{$aId}] (".gettype($aId).")");
        
        // Ensure users can only view their own top-ups
        // if ((int)$topUp->user_id !== (int)auth()->user()->id_user) {
        //     abort(403);
        // }

        return view('topup.show', compact('topUp'));
    }

    public function confirm(TopUp $topUp)
    {
        // Ensure users can only confirm their own top-ups
        if ((int)$topUp->user_id !== (int)auth()->user()->id_user) {
            abort(403);
        }

        // Only pending top-ups can be confirmed
        if ($topUp->status !== 'pending') {
            return back()->with('error', 'Top-up ini tidak dapat dikonfirmasi.');
        }

        // Update top-up status
        $topUp->status = 'completed';
        $topUp->save();

        // Add amount to user's balance
        $user = User::find($topUp->user_id);
        $user->saldo += $topUp->amount;
        $user->save();

        return redirect()->route('topup.index')
                         ->with('success', 'Top-up berhasil dikonfirmasi dan saldo telah ditambahkan.');
    }
}