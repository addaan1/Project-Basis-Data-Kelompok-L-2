@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Transactions</h2>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Buyer</th>
                            <th>Seller</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>#{{ $transaction->id_transaksi }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d M Y') }}</td>
                            <td>{{ $transaction->pembeli->nama ?? 'Unknown' }}</td>
                            <td>{{ $transaction->penjual->nama ?? 'Unknown' }}</td>
                            <td>Rp {{ number_format($transaction->harga_akhir ?? 0, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $statusColor = 'secondary';
                                    if($transaction->status_transaksi == 'completed') $statusColor = 'success';
                                    elseif($transaction->status_transaksi == 'pending') $statusColor = 'warning';
                                    elseif($transaction->status_transaksi == 'processed') $statusColor = 'info';
                                    elseif($transaction->status_transaksi == 'cancelled' || $transaction->status_transaksi == 'failed') $statusColor = 'danger';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ ucfirst($transaction->status_transaksi) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.transactions.show', $transaction->id_transaksi) }}" class="btn btn-sm btn-outline-primary">Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No transactions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
