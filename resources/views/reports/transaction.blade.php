<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; color: #333; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 10px; color: white; }
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-primary { background-color: #0d6efd; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Riwayat Transaksi</h2>
        <p>Dicetak Tanggal: {{ now()->format('d M Y H:i') }}</p>
        <p>Pengguna: {{ $user->nama }} ({{ ucfirst($user->peran) }})</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Keterangan</th>
                <th class="text-end">Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $trx)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($trx->created_at)->format('d/m/Y') }}</td>
                <td>{{ ucfirst($trx->jenis_transaksi) }}</td>
                <td>
                    {{ $trx->produk->nama_produk ?? '-' }} ({{ $trx->jumlah }} Kg)
                    <br>
                    <small style="color: #666;">{{ $trx->description }}</small>
                </td>
                <td class="text-end">
                    Rp {{ number_format($trx->harga_akhir * $trx->jumlah, 0, ',', '.') }}
                </td>
                <td>
                    {{ ucfirst($trx->status_transaksi) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
