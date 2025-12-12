<?php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TopUp;
use App\Models\Expenditure;
use App\Models\Inventory;
use App\Models\Pengepul;
use App\Models\Petani;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class DashboardController extends Controller {
    public function index()
    {
        try {
            $user = auth()->user();
            $saldo = (float) ($user->saldo ?? 0);

            $marketTransactions = Transaksi::where(function ($q) use ($user) {
                    $q->where('id_penjual', $user->id_user)
                      ->orWhere('id_pembeli', $user->id_user);
                })
                ->select(['id_penjual', 'id_pembeli', 'jumlah', 'harga_akhir', 'tanggal'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($trx) use ($user) {
                    $isSale = $trx->id_penjual == $user->id_user;
                    return (object) [
                        'type' => $isSale ? 'sale' : 'purchase',
                        'description' => ($isSale ? 'Penjualan ' : 'Pembelian ') . $trx->jumlah . ' kg',
                        'amount' => (float) ($trx->harga_akhir ?? 0) * (float) ($trx->jumlah ?? 0),
                        'date' => $trx->tanggal,
                    ];
                });

            $topUps = TopUp::where('user_id', $user->id_user)
                ->select(['amount', 'created_at'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($topup) {
                    return (object) [
                        'type' => 'topup',
                        'description' => 'Topup saldo',
                        'amount' => (float) ($topup->amount ?? 0),
                        'date' => optional($topup->created_at)->toDateString(),
                    ];
                });

            $expenditures = Expenditure::where('user_id', $user->id_user)
                ->select(['amount', 'description', 'date'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($exp) {
                    return (object) [
                        'type' => 'expenditure',
                        'description' => $exp->description,
                        'amount' => (float) ($exp->amount ?? 0),
                        'date' => $exp->date,
                    ];
                });

            $activities = $marketTransactions->concat($topUps)->concat($expenditures)->sortByDesc('date')->take(5);

            $inventoryKg = (int) Inventory::where('id_user', $user->id_user)->sum('jumlah');
            $inventoryTon = $inventoryKg / 1000;

            $capacityKg = null;
            if (($user->peran ?? null) === 'pengepul') {
                $capacityKg = (int) (Pengepul::where('id_user', $user->id_user)->value('kapasitas_tampung') ?? 0);
            } elseif (($user->peran ?? null) === 'petani') {
                $capacityKg = (int) (Petani::where('id_user', $user->id_user)->value('kapasitas_panen') ?? 0);
            }
            if (!$capacityKg || $capacityKg <= 0) {
                $capacityKg = 10000; // fallback kapasitas
            }
            $capacityPercent = min(100, (int) round(($inventoryKg / $capacityKg) * 100));

            $negotiationsSummary = Transaksi::where(function ($q) use ($user) {
                    $q->where('id_penjual', $user->id_user)
                      ->orWhere('id_pembeli', $user->id_user);
                })
                ->select(['id_penjual', 'id_pembeli', 'jumlah', 'status_transaksi'])
                ->latest()
                ->take(2)
                ->get()
                ->map(function ($trx) use ($user) {
                    $isSale = $trx->id_penjual == $user->id_user;
                    $label = $isSale ? 'Penjualan' : 'Pembelian';
                    $status = $trx->status_transaksi;
                    $statusText = match ($status) {
                        'menunggu_pembayaran' => 'Menunggu',
                        'completed', 'disetujui' => 'Disetujui',
                        default => ucfirst(str_replace('_', ' ', (string) $status)),
                    };
                    return (object) [
                        'label' => $label,
                        'jumlah_kg' => (int) ($trx->jumlah ?? 0),
                        'status' => $statusText,
                    ];
                });

            $lastUpdate = now();

            return view('dashboard', compact(
                'saldo',
                'activities',
                'inventoryKg',
                'inventoryTon',
                'capacityKg',
                'capacityPercent',
                'negotiationsSummary',
                'lastUpdate'
            ));
        } catch (\Throwable $e) {
            Log::error('Dashboard index error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $saldo = 0;
            $activities = collect();
            $inventoryKg = 0;
            $inventoryTon = 0;
            $capacityKg = 10000;
            $capacityPercent = 0;
            $negotiationsSummary = collect();
            $lastUpdate = now();
            return view('dashboard', compact('saldo','activities','inventoryKg','inventoryTon','capacityKg','capacityPercent','negotiationsSummary','lastUpdate'));
        }
    }

    public function data()
    {
        try {
            $user = auth()->user();
            $saldo = (float) ($user->saldo ?? 0);

            $inventoryKg = (int) Inventory::where('id_user', $user->id_user)->sum('jumlah');
            $capacityKg = null;
            if (($user->peran ?? null) === 'pengepul') {
                $capacityKg = (int) (Pengepul::where('id_user', $user->id_user)->value('kapasitas_tampung') ?? 0);
            } elseif (($user->peran ?? null) === 'petani') {
                $capacityKg = (int) (Petani::where('id_user', $user->id_user)->value('kapasitas_panen') ?? 0);
            }
            if (!$capacityKg || $capacityKg <= 0) {
                $capacityKg = 10000;
            }
            $capacityPercent = min(100, (int) round(($inventoryKg / $capacityKg) * 100));

            $activities = Transaksi::where(function ($q) use ($user) {
                    $q->where('id_penjual', $user->id_user)
                      ->orWhere('id_pembeli', $user->id_user);
                })
                ->select(['id_penjual', 'id_pembeli', 'jumlah', 'harga_akhir', 'tanggal'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($trx) use ($user) {
                    $isSale = $trx->id_penjual == $user->id_user;
                    return [
                        'type' => $isSale ? 'sale' : 'purchase',
                        'description' => ($isSale ? 'Penjualan ' : 'Pembelian ') . $trx->jumlah . ' kg',
                        'amount' => (float) ($trx->harga_akhir ?? 0) * (float) ($trx->jumlah ?? 0),
                        'date' => $trx->tanggal,
                    ];
                });

            $negotiations = Transaksi::where(function ($q) use ($user) {
                    $q->where('id_penjual', $user->id_user)
                      ->orWhere('id_pembeli', $user->id_user);
                })
                ->select(['id_penjual','id_pembeli','jumlah','status_transaksi'])
                ->latest()
                ->take(2)
                ->get()
                ->map(function ($trx) use ($user) {
                    $isSale = $trx->id_penjual == $user->id_user;
                    $status = $trx->status_transaksi;
                    $statusText = match ($status) {
                        'menunggu_pembayaran' => 'Menunggu',
                        'completed', 'disetujui' => 'Disetujui',
                        default => ucfirst(str_replace('_', ' ', (string) $status)),
                    };
                    return [
                        'label' => $isSale ? 'Penjualan' : 'Pembelian',
                        'jumlah_kg' => (int) ($trx->jumlah ?? 0),
                        'status' => $statusText,
                    ];
                });

            return response()->json([
                'saldo' => $saldo,
                'inventoryKg' => $inventoryKg,
                'capacityKg' => $capacityKg,
                'capacityPercent' => $capacityPercent,
                'activities' => $activities,
                'negotiations' => $negotiations,
                'lastUpdate' => now()->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Dashboard data error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan'], 500);
        }
    }
}
