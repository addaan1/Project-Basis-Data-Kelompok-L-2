@extends('layouts.main')

@section('content')
<div class="container-fluid p-4">
    <!-- Hero Section (Pengepul) -->
    <div class="card border-0 mb-5 shadow-lg overflow-hidden" 
         style="border-radius: 20px; background: linear-gradient(135deg, #FF6F00, #FF8F00); color: white;">
        <div class="card-body p-4 p-lg-5 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-center text-md-start">
                <h1 class="display-6 fw-bold mb-2">
                    <i class="bi bi-shop-window me-2 text-white-50"></i>Dashboard Pengepul
                </h1>
                <p class="lead mb-0 fw-medium opacity-75">
                    Moda Trading: Pantau arus kas, stok masuk, dan negosiasi pasar.
                </p>
            </div>
            <div>
                <button class="btn btn-light btn-lg text-warning fw-bold rounded-pill px-4 shadow-sm hover-scale" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise me-2"></i> Refresh Pasar
                </button>
            </div>
        </div>
    </div>

    <!-- Trading Stats Grid (ETL Powered) -->
    <div class="row g-4 mb-4">
        <!-- Net Cashflow (Income - Expense) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-cash-stack display-4 text-warning"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Net Cashflow</p>
                    @php $netFlow = ($totalIncome ?? 0) - ($totalExpense ?? 0); @endphp
                    <h3 class="fw-bold {{ $netFlow >= 0 ? 'text-success' : 'text-danger' }} mb-1">
                        {{ $netFlow >= 0 ? '+' : '' }} Rp {{ number_format($netFlow, 0, ',', '.') }}
                    </h3>
                    <small class="text-muted fw-bold">
                        In: <span class="text-success">{{ number_format(($totalIncome ?? 0)/1000000, 1) }}M</span> | 
                        Out: <span class="text-danger">{{ number_format(($totalExpense ?? 0)/1000000, 1) }}M</span>
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-warning" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Volume Pembelian (Stock In) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-box-arrow-in-down display-4 text-primary"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Volume Pembelian (Harian)</p>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($totalKgBought ?? 0) }} <span class="fs-6 text-muted">Kg</span></h3>
                    <small class="text-primary fw-bold">
                        <i class="bi bi-arrow-down-circle me-1"></i>
                        Stok Masuk
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-primary" style="width: {{ min(($totalKgBought / 1000) * 100, 100) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Kapasitas Gudang -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-building display-4 text-info"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Kapasitas Gudang</p>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($inventoryKg ?? 0) }} <span class="fs-6 text-muted">/ {{ number_format($capacityKg ?? 10000) }} Kg</span></h3>
                    <small class="{{ $capacityPercent > 80 ? 'text-danger' : 'text-info' }} fw-bold">
                        <i class="bi bi-hdd-network me-1"></i>
                        Terisi {{ $capacityPercent }}%
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar {{ $capacityPercent > 80 ? 'bg-danger' : 'bg-info' }}" style="width: {{ $capacityPercent }}%"></div>
                </div>
            </div>
        </div>

        <!-- Negosiasi Pending -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card bg-white">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-chat-quote align-middle display-4 text-secondary"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Tawaran Saya</p>
                    <h3 class="fw-bold text-dark mb-1">{{ $negotiationsSummary->where('status', 'Menunggu')->count() }} <span class="fs-6 text-muted">Pending</span></h3>
                    <small class="text-warning fw-bold">
                        <i class="bi bi-clock-history me-1"></i>
                        Menunggu Respon Petani
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-warning" style="width: 50%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Operations Area -->
    <div class="row g-4">
        <!-- Market & Activity Feed -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-activity me-2 text-warning"></i>Aktivitas Pasca Panen</h5>
                    <a href="{{ route('pasar.index') }}" class="btn btn-outline-warning btn-sm rounded-pill px-3">
                        <i class="bi bi-cart-plus me-1"></i> Cari Beras
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        @forelse($activities as $activity)
                            <div class="list-group-item border-0 px-0 py-3 d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="activity-icon rounded-circle d-flex align-items-center justify-content-center bg-light"
                                        style="width: 45px; height: 45px;">
                                        @php
                                            $iconColor = match($activity->type) {
                                                'sale' => 'text-success',
                                                'purchase' => 'text-primary', // Purchase is Primary (Blue) for Pengepul context usually means Stock In
                                                'topup' => 'text-info',
                                                default => 'text-secondary'
                                            };
                                            $icon = match($activity->type) {
                                                'sale' => 'bi-arrow-up-right',
                                                'purchase' => 'bi-arrow-down-left',
                                                'topup' => 'bi-wallet2',
                                                default => 'bi-circle'
                                            };
                                        @endphp
                                        <i class="bi {{ $icon }} fs-5 {{ $iconColor }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <h6 class="mb-1 fw-bold text-dark">{{ $activity->description }}</h6>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ \Carbon\Carbon::parse($activity->date)->translatedFormat('d F Y, H:i') }}
                                    </small>
                                </div>
                                <div class="text-end ms-3">
                                    @php
                                        // Logic Colors for Money: Green (In), Red (Out)
                                        // Sale = Money In (Green). Purchase = Money Out (Red). Topup = Money In (Blue/Green).
                                        $isMoneyIn = in_array($activity->type, ['sale', 'topup']);
                                        $color = $isMoneyIn ? 'text-success' : 'text-danger';
                                        $sign = $isMoneyIn ? '+' : '-';
                                    @endphp
                                    <span class="d-block fw-bold {{ $color }} fs-6">
                                        {{ $sign }} Rp {{ number_format($activity->amount, 0, ',', '.') }}
                                    </span>
                                    <span class="badge bg-light text-secondary rounded-pill border mt-1">{{ ucfirst($activity->type) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 opacity-50">
                                <i class="bi bi-inbox-fill display-4 text-muted"></i>
                                <p class="mt-3 text-muted">Belum ada transaksi jual-beli.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Status -->
        <div class="col-lg-4">
            <!-- Saldo Card Mini -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-dark text-white overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute top-0 end-0 mt-n3 me-n3 opacity-10">
                        <i class="bi bi-wallet2 display-1 text-white"></i>
                    </div>
                    <div class="mb-4">
                        <small class="text-white-50 text-uppercase fw-bold">Saldo Aktif</small>
                        <h2 class="fw-bold mb-0">Rp {{ number_format($saldo, 0, ',', '.') }}</h2>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('topup.index') }}" class="btn btn-warning fw-bold text-dark">
                            <i class="bi bi-plus-lg me-1"></i> Isi Saldo
                        </a>
                        <a href="{{ route('ewallet') }}" class="btn btn-outline-light">
                            <i class="bi bi-clock-history me-1"></i> Riwayat
                        </a>
                    </div>
                </div>
            </div>

            <!-- Negotiation Summary List -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 p-4 pb-0">
                    <h5 class="fw-bold mb-0">Status Nego Terakhir</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        @forelse($negotiationsSummary->take(3) as $neg)
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $neg->product_name }}</h6>
                                <small class="text-muted">{{ number_format($neg->jumlah_kg) }} Kg &bull; Rp {{ number_format($neg->harga_tawar) }}</small>
                            </div>
                            @php
                                $badge = match($neg->status) {
                                    'Menunggu' => 'bg-warning text-dark',
                                    'Disetujui' => 'bg-success',
                                    'Ditolak' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badge }} rounded-pill">{{ $neg->status }}</span>
                        </div>
                        @empty
                            <p class="text-muted small mb-0">Tidak ada negosiasi.</p>
                        @endforelse
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('negosiasi.index') }}" class="text-decoration-none small fw-bold text-warning">Lihat Semua ></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .hover-scale:hover {
        transform: scale(1.02);
        transition: transform 0.2s;
    }
</style>
@endsection
