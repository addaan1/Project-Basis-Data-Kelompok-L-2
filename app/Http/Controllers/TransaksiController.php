<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Transaksi::query();
        if (($user->peran ?? null) === 'petani') {
            $query->where('id_penjual', $user->id_user);
        } else {
            $query->where('id_pembeli', $user->id_user);
        }

        if ($request->filled('status')) {
            $query->where('status_transaksi', $request->string('status'));
        }
        if ($request->filled('produk')) {
            $query->where('description', 'like', '%'.$request->string('produk').'%');
        }
        if ($request->filled('from')) {
            $query->whereDate('tanggal', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal', '<=', $request->date('to'));
        }

        $transactions = $query->with(['pembeli'])
            ->orderByDesc('tanggal')
            ->paginate(10)
            ->through(function ($trx) {
                return (object) [
                    'id' => $trx->id_transaksi,
                    'produk' => $trx->description,
                    'harga' => (float) ($trx->harga_akhir ?? $trx->harga_awalan ?? 0),
                    'jumlah' => (int) $trx->jumlah,
                    'pembeli' => $trx->pembeli?->nama,
                    'pembeli_kontak' => $trx->pembeli?->email,
                    'tanggal' => $trx->tanggal,
                    'status' => $trx->status_transaksi,
                ];
            });

        return view('transaksi.petani', compact('transactions'));
    }

    public function approve(Transaksi $transaksi)
    {
        $user = Auth::user();
        if ($transaksi->id_penjual !== $user->id_user) {
            abort(403);
        }

        $before = $transaksi->status_transaksi;
        $transaksi->status_transaksi = 'disetujui';
        $transaksi->save();

        TransaksiHistory::create([
            'id_transaksi' => $transaksi->id_transaksi,
            'status_before' => $before,
            'status_after' => 'disetujui',
            'changed_by' => $user->id_user,
            'note' => 'Transaksi disetujui oleh petani',
        ]);

        return back()->with('success', 'Transaksi disetujui');
    }

    public function reject(Transaksi $transaksi)
    {
        $user = Auth::user();
        if ($transaksi->id_penjual !== $user->id_user) {
            abort(403);
        }

        $before = $transaksi->status_transaksi;
        $transaksi->status_transaksi = 'ditolak';
        $transaksi->save();

        TransaksiHistory::create([
            'id_transaksi' => $transaksi->id_transaksi,
            'status_before' => $before,
            'status_after' => 'ditolak',
            'changed_by' => $user->id_user,
            'note' => 'Transaksi ditolak oleh petani',
        ]);

        return back()->with('success', 'Transaksi ditolak');
    }

    public function history(Transaksi $transaksi)
    {
        $user = Auth::user();
        if ($transaksi->id_penjual !== $user->id_user && $transaksi->id_pembeli !== $user->id_user) {
            abort(403);
        }

        $status = TransaksiHistory::where('id_transaksi', $transaksi->id_transaksi)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($h) {
                return [
                    'kind' => 'status',
                    'status_before' => $h->status_before,
                    'status_after' => $h->status_after,
                    'note' => $h->note,
                    'created_at' => $h->created_at,
                ];
            });

        $offers = DB::table('negosiasi_hargas')
            ->where('id_transaksi', $transaksi->id_transaksi)
            ->orderByDesc('created_at')
            ->get(['harga_tawaran as offer', 'catatan as note', 'created_at'])
            ->map(function ($o) {
                return [
                    'kind' => 'offer',
                    'offer' => (float) $o->offer,
                    'note' => $o->note,
                    'created_at' => $o->created_at,
                ];
            });

        $items = $status->concat($offers)->sortByDesc('created_at')->values();
        return response()->json($items);
    }

    public function notifications(Request $request)
    {
        $user = Auth::user();

        $items = collect();
        if (($user->peran ?? null) === 'petani') {
            $recentNegotiations = DB::table('negosiasi_hargas')
                ->join('transaksis', 'negosiasi_hargas.id_transaksi', '=', 'transaksis.id_transaksi')
                ->where('transaksis.id_penjual', $user->id_user)
                ->orderByDesc('negosiasi_hargas.created_at')
                ->limit(10)
                ->get([
                    'negosiasi_hargas.harga_tawaran as offer',
                    'negosiasi_hargas.catatan as note',
                    'negosiasi_hargas.created_at as created_at',
                    'transaksis.jumlah as jumlah',
                ])
                ->map(function ($n) {
                    return [
                        'type' => 'negosiasi',
                        'message' => 'Penawaran baru: Rp '.number_format((float) $n->offer, 0, ',', '.') . ' / kg untuk '.((int)$n->jumlah).' kg',
                        'created_at' => $n->created_at,
                    ];
                });

            $statusUpdates = TransaksiHistory::whereHas('transaksi', function ($q) use ($user) {
                    $q->where('id_penjual', $user->id_user);
                })
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(function ($h) {
                    return [
                        'type' => 'status',
                        'message' => 'Status berubah: '.$h->status_before.' → '.$h->status_after,
                        'created_at' => $h->created_at,
                    ];
                });

            $items = $recentNegotiations->concat($statusUpdates)->sortByDesc('created_at')->values();
        } else {
            $statusUpdates = TransaksiHistory::whereHas('transaksi', function ($q) use ($user) {
                    $q->where('id_pembeli', $user->id_user);
                })
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(function ($h) {
                    return [
                        'type' => 'status',
                        'message' => 'Status transaksi: '.$h->status_after,
                        'created_at' => $h->created_at,
                    ];
                });
            $items = $statusUpdates->values();
        }

        return response()->json($items);
    }
}
