@extends('layouts.main')

@section('content')
<div class="container-fluid p-4">
    <!-- Welcome Section -->
    <!-- Welcome Section -->
    <!-- Welcome Section (Hero Card) -->
    <div class="card border-0 mb-5 shadow-lg overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05)); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
        <div class="card-body p-4 p-lg-5 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-center text-md-start">
                <h1 class="display-6 fw-bold mb-2" style="color: #1B5E20; text-shadow: 0 1px 1px rgba(255,255,255,0.8);">
                    <i class="bi bi-speedometer2 me-2 text-warning"></i>Dashboard Petani
                </h1>
                <p class="lead mb-0 fw-medium" style="color: #33691E;">
                    Pantau aktivitas penjualan dan stok beras Anda dengan mudah.
                </p>
            </div>
            <div>
                <button class="btn btn-warning btn-lg text-dark fw-bold rounded-pill px-4 shadow-sm hover-scale" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise me-2"></i> Refresh Data
                </button>
            </div>
        </div>
    </div>

    <!-- Business Stats Grid (ETL Powered) -->
    <div class="row g-4 mb-4">
        <!-- Total Pemasukan -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-graph-up-arrow display-4 text-success"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Total Pemasukan</p>
                    <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</h3>
                    <small class="text-success fw-bold">
                        <i class="bi bi-arrow-up-circle me-1"></i>
                        Revenue
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-success" style="width: 70%"></div>
                </div>
            </div>
        </div>

        <!-- Total Pengeluaran -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-cart-check display-4 text-danger"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Total Pengeluaran</p>
                    <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}</h3>
                    <small class="text-danger fw-bold">
                        <i class="bi bi-arrow-down-circle me-1"></i>
                        Expense
                    </small>
                </div>
                 <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-danger" style="width: 40%"></div>
                </div>
            </div>
        </div>

        <!-- Volume Terjual -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-box-seam display-4 text-info"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Volume Terjual</p>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($totalKgSold ?? 0) }} <span class="fs-6 text-muted">Kg</span></h3>
                    <small class="text-info fw-bold">
                        <i class="bi bi-check2-all me-1"></i>
                        Produktivitas
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-info" style="width: 60%"></div>
                </div>
            </div>
        </div>

        <!-- Saldo Aktif -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card bg-gradient-green text-white">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-25">
                        <i class="bi bi-wallet2 display-4"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-white-50 mb-1">Saldo Aktif</p>
                    <h3 class="fw-bold mb-1">Rp {{ number_format($saldo ?? 0, 0, ',', '.') }}</h3>
                    <small class="text-white fw-bold">
                        <span class="badge bg-white text-success rounded-pill px-2">Aman</span>
                    </small>
                </div>
            </div>
        </div>
    </div>
     <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Recent Activity Feed -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Aktivitas Terakhir</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($activities as $activity)
                            <div class="list-group-item px-3 py-3 d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="activity-icon rounded-circle d-flex align-items-center justify-content-center bg-white"
                                        style="width: 40px; height: 40px;">
                                        @php
                                            $iconColorClass = match($activity->type) {
                                                'sale' => 'text-success',
                                                'topup' => 'text-info',
                                                default => 'text-danger'
                                            };
                                            $iconClass = match($activity->type) {
                                                'sale' => 'bi-arrow-up-right',
                                                'topup' => 'bi-wallet2',
                                                default => 'bi-arrow-down-left'
                                            };
                                        @endphp
                                        <i class="bi {{ $iconClass }} fs-5 {{ $iconColorClass }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <h6 class="mb-1 fw-bold text-white">{{ $activity->description }}</h6>
                                    <small class="text-white-50 d-block">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($activity->date)->format('d M Y, H:i') }}
                                    </small>
                                </div>
                                <div class="text-end ms-3">
                                    <span class="d-block fw-bold text-white">
                                        {{ ($activity->type == 'sale' || $activity->type == 'topup') ? '+' : '-' }} Rp {{ number_format($activity->amount, 0, ',', '.') }}
                                    </span>
                                    <span class="badge bg-white text-dark rounded-pill border mt-1">{{ ucfirst($activity->type) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-3 text-white-50 opacity-50">
                                    <i class="bi bi-clipboard-x display-4"></i>
                                </div>
                                <h6 class="text-white-50">Belum ada aktivitas tercatat.</h6>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Negotiation Status Table -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Status Negosiasi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="text-white-50 small text-uppercase" style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                                <tr>
                                    <th class="ps-0">Partner</th>
                                    <th>Jml (Kg)</th>
                                    <th class="text-end pe-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($negotiationsSummary as $neg)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                        <td class="ps-0 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-white rounded-circle p-2 me-2 text-primary">
                                                    <i class="bi bi-person-fill"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block fw-bold text-white">{{ $neg->label }}</span>
                                                    <small class="text-white-50" style="font-size: 0.75rem;">Negosiasi</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 fw-medium text-white">{{ number_format($neg->jumlah_kg) }}</td>
                                        <td class="text-end pe-0 py-3">
                                            @php
                                                $statusClass = match($neg->status) {
                                                    'Menunggu' => 'bg-warning text-dark',
                                                    'Disetujui' => 'bg-success text-white',
                                                    'Ditolak' => 'bg-danger text-white',
                                                    default => 'bg-secondary text-white'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">{{ $neg->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <div class="text-white-50 opacity-50 mb-2">
                                                <i class="bi bi-inbox fs-1"></i>
                                            </div>
                                            <p class="text-white-50 small mb-0">Tidak ada negosiasi aktif</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4 text-center">
                        <a href="{{ route('negosiasi.index') }}" class="btn btn-sm btn-light rounded-pill px-4 text-success fw-bold">
                            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
    }
    .icon-shape {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
</style>
@endsection
