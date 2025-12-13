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
        $seller = Auth::user();
        if ((int)$transaksi->id_penjual !== (int)$seller->id_user) { abort(403); }

        DB::beginTransaction();
        try {
            $before = $transaksi->status_transaksi;
            $total = (float) ($transaksi->harga_akhir ?? $transaksi->harga_awalan ?? 0) * (int) ($transaksi->jumlah ?? 0);

            $seller->saldo = (float) ($seller->saldo ?? 0) + $total;
            $seller->save();

            // Note: Stok sudah dipotong saat transaksi dibuat (Reserve Stock).
            // Jadi tidak perlu dipotong lagi disini.

            $transaksi->status_transaksi = 'disetujui';
            $transaksi->save();

            \App\Models\Expenditure::where('user_id', $transaksi->id_pembeli)
                ->where('status', 'pending')
                ->where('description', 'like', 'Hold transaksi #'.$transaksi->id_transaksi.'%')
                ->update(['status' => 'completed']);

            TransaksiHistory::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'status_before' => $before,
                'status_after' => 'disetujui',
                'changed_by' => $seller->id_user,
                'note' => 'Transaksi disetujui, saldo seller bertambah dan stok berkurang',
            ]);

            DB::commit();
            return back()->with('success', 'Transaksi disetujui');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Approve transaksi error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Gagal menyetujui transaksi: '.$e->getMessage());
        }
    }

    public function reject(Transaksi $transaksi)
    {
        $seller = Auth::user();
        if ((int)$transaksi->id_penjual !== (int)$seller->id_user) { abort(403); }

        DB::beginTransaction();
        try {
            $before = $transaksi->status_transaksi;
            $total = (float) ($transaksi->harga_akhir ?? $transaksi->harga_awalan ?? 0) * (int) ($transaksi->jumlah ?? 0);

            $buyer = \App\Models\User::find($transaksi->id_pembeli);
            if ($buyer) {
                $buyer->saldo = (float) ($buyer->saldo ?? 0) + $total; // release hold
                $buyer->save();
            }

            // Kembalikan Stok (Refund Stock)
            if ($transaksi->id_produk) {
               $product = \App\Models\ProdukBeras::find($transaksi->id_produk);
               if ($product) {
                   $product->increment('stok', (int) $transaksi->jumlah);
               }
            }

            \App\Models\Expenditure::where('user_id', $transaksi->id_pembeli)
                ->where('status', 'pending')
                ->where('description', 'like', 'Hold transaksi #'.$transaksi->id_transaksi.'%')
                ->update(['status' => 'completed', 'description' => 'Hold dirilis transaksi #'.$transaksi->id_transaksi]);

            $transaksi->status_transaksi = 'ditolak';
            $transaksi->save();

            TransaksiHistory::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'status_before' => $before,
                'status_after' => 'ditolak',
                'changed_by' => $seller->id_user,
                'note' => 'Transaksi ditolak, saldo buyer dikembalikan',
            ]);

            DB::commit();
            return back()->with('success', 'Transaksi ditolak');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Reject transaksi error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Gagal menolak transaksi: '.$e->getMessage());
        }
    }

    public function history(Transaksi $transaksi)
    {
        $user = Auth::user();
        if ((int)$transaksi->id_penjual !== (int)$user->id_user && (int)$transaksi->id_pembeli !== (int)$user->id_user) {
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
            $recentPurchases = Transaksi::where('id_penjual', $user->id_user)
                ->where('status_transaksi', 'menunggu_pembayaran')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(['jumlah','harga_akhir','harga_awalan','created_at'])
                ->map(function ($t) {
                    $price = (float) ($t->harga_akhir ?? $t->harga_awalan ?? 0);
                    return [
                        'type' => 'pembelian',
                        'message' => 'Pembeli baru: '.(int)$t->jumlah.' kg @ Rp '.number_format($price,0,',','.'),
                        'created_at' => $t->created_at,
                    ];
                });

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

            $items = $recentPurchases->concat($recentNegotiations)->concat($statusUpdates)->sortByDesc('created_at')->values();
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
