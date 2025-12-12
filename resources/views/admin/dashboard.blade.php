@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold text-dark">Admin Dashboard</h2>
            <p class="text-secondary">Welcome back, Administrator. Here's what's happening today.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #0d6efd, #0a58ca);">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people-fill me-2"></i>Total Users</h5>
                    <h2 class="display-4 fw-bold mt-3">{{ $totalUsers }}</h2>
                    <p class="card-text small opacity-75">Registered users on platform</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #198754, #157347);">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-basket-fill me-2"></i>Total Products</h5>
                    <h2 class="display-4 fw-bold mt-3">{{ $totalProducts }}</h2>
                    <p class="card-text small opacity-75">Active listings in market</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-warning text-dark h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #ffc107, #ffca2c);">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-currency-exchange me-2"></i>Transactions</h5>
                    <h2 class="display-4 fw-bold mt-3">{{ $totalTransactions }}</h2>
                    <p class="card-text small opacity-75">Total transactions processed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Recent Transactions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Buyer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaction)
                                <tr>
                                    <td>#{{ $transaction->id_transaksi ?? $transaction->id }}</td>
                                    <td>{{ $transaction->user->nama ?? 'Unknown' }}</td>
                                    <td>Rp {{ number_format($transaction->total_harga ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->created_at->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No recent transactions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <a href="{{ route('admin.transactions.index') }}" class="text-decoration-none fw-semibold">View All Transactions <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Quick Actions</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action py-3 d-flex align-items-center">
                        <div class="bg-light rounded-circle p-2 me-3 text-primary"><i class="bi bi-person-plus-fill"></i></div>
                        <div>
                            <h6 class="mb-0">Manage Users</h6>
                            <small class="text-muted">View, edit, or delete users</small>
                        </div>
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action py-3 d-flex align-items-center">
                        <div class="bg-light rounded-circle p-2 me-3 text-success"><i class="bi bi-box-seam-fill"></i></div>
                        <div>
                            <h6 class="mb-0">Manage Products</h6>
                            <small class="text-muted">Review market listings</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
