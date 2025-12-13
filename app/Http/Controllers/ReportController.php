<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private function getTransactions()
    {
        $user = Auth::user();
        $query = Transaksi::with(['user', 'produk']);

        if ($user->peran == 'pengepul') {
            $query->where('id_pembeli', $user->id_user);
        } elseif ($user->peran == 'petani') {
            $query->where('id_penjual', $user->id_user);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function exportPdf()
    {
        $transactions = $this->getTransactions();
        $user = Auth::user();

        $pdf = Pdf::loadView('reports.transaction', compact('transactions', 'user'));
        return $pdf->download('laporan-transaksi-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportCsv()
    {
        $transactions = $this->getTransactions();
        $filename = "laporan-transaksi-" . now()->format('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['ID', 'Tanggal', 'Tipe', 'Produk', 'Jumlah (Kg)', 'Harga Total', 'Status']);

            // Data
            foreach ($transactions as $trx) {
                fputcsv($file, [
                    $trx->id,
                    $trx->created_at->format('Y-m-d H:i'),
                    $trx->jenis_transaksi,
                    $trx->produk->nama_produk ?? '-',
                    $trx->jumlah,
                    "Rp " . number_format($trx->harga_akhir * $trx->jumlah, 0, ',', '.'),
                    $trx->status_transaksi
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
