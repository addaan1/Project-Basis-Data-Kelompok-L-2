@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Transaction Details #{{ $transaction->id_transaksi }}</h5>
                    <span class="badge bg-{{ $transaction->status_transaksi == 'completed' ? 'success' : ($transaction->status_transaksi == 'pending' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($transaction->status_transaksi) }}
                    </span>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold">Transaction Info</h6>
                            <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($transaction->tanggal)->format('d F Y H:i') }}</p>
                            <p class="mb-1"><strong>Type:</strong> {{ ucfirst($transaction->jenis_transaksi) }}</p>
                            <p class="mb-1"><strong>Total Amount:</strong> <span class="text-primary fw-bold">Rp {{ number_format($transaction->harga_akhir, 0, ',', '.') }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold">Payment Method</h6>
                            <p class="mb-1">{{ $transaction->payment_method ?? '-' }}</p>
                            <p class="mb-1 small text-muted">{{ $transaction->reference_code ?? '' }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3"><i class="bi bi-person-circle me-2"></i>Buyer</h6>
                                <p class="mb-1 fw-bold">{{ $transaction->pembeli->nama ?? 'Unknown' }}</p>
                                <p class="mb-0 small text-muted">{{ $transaction->pembeli->email ?? '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3"><i class="bi bi-shop me-2"></i>Seller</h6>
                                <p class="mb-1 fw-bold">{{ $transaction->penjual->nama ?? 'Unknown' }}</p>
                                <p class="mb-0 small text-muted">{{ $transaction->penjual->email ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small fw-bold">Product Details</h6>
                        <div class="bg-light p-3 rounded" style="color: #212529 !important;">
                            <p class="mb-1" style="color: #212529 !important;"><strong>Product ID:</strong> {{ $transaction->id_produk ?? '-' }}</p>
                            <p class="mb-1" style="color: #212529 !important;"><strong>Market:</strong> {{ $transaction->pasar->nama_pasar ?? '-' }}</p>
                            <p class="mb-1" style="color: #212529 !important;"><strong>Quantity:</strong> {{ $transaction->jumlah ?? 0 }}</p>
                            <p class="mb-0" style="color: #212529 !important;"><strong>Description:</strong> {{ $transaction->description ?? '-' }}</p>
                        </div>
                    </div>

                    <hr>

                    <form action="{{ route('admin.transactions.update', $transaction->id_transaksi) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-bold">Update Status</label>
                            <div class="input-group">
                                <select name="status_transaksi" class="form-select">
                                    <option value="pending" {{ $transaction->status_transaksi == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processed" {{ $transaction->status_transaksi == 'processed' ? 'selected' : '' }}>Processed</option>
                                    <option value="completed" {{ $transaction->status_transaksi == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $transaction->status_transaksi == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="failed" {{ $transaction->status_transaksi == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-4">
                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back to Transactions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
