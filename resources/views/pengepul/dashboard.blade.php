@extends('layouts.main')

@section('content')
<div class="container-fluid p-4">
    <!-- Welcome Section (Premium Hero) -->
    <div class="card border-0 mb-4 shadow-lg overflow-hidden position-relative hero-card" style="border-radius: 24px; background: linear-gradient(135deg, #FF6F00 0%, #F57C00 50%, #EF6C00 100%); min-height: 180px;">
        
        <!-- Floating Decorative Elements -->
        <div class="position-absolute" style="top: 20px; right: 40px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; filter: blur(40px); animation: float 6s ease-in-out infinite;"></div>
        <div class="position-absolute" style="bottom: 30px; left: 60px; width: 80px; height: 80px; background: rgba(255,255,255,0.08); border-radius: 50%; filter: blur(30px); animation: float 8s ease-in-out infinite reverse;"></div>
        
        <div class="card-body p-4 py-md-4 px-md-5 position-relative">
            <div class="row align-items-center">
                <!-- Left: Title & Info -->
                <div class="col-md-7">
                    <div class="d-flex align-items-start mb-3">
                        <!-- Animated Icon -->
                        <div class="icon-container bg-white rounded-4 p-3 shadow-lg me-3 position-relative" style="width: 64px; height: 64px;">
                            <i class="bi bi-shop-window fs-3 text-warning position-absolute top-50 start-50 translate-middle"></i>
                            <div class="pulse-ring" style="border-color: rgba(255, 152, 0, 0.4);"></div>
                        </div>
                        
                        <div class="flex-grow-1">
                            <!-- Greeting with Animation -->
                            <div class="greeting-text mb-2" style="animation: slideInLeft 0.6s ease-out;">
                                <p class="text-white mb-1 d-flex align-items-center" style="font-size: 0.9rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-sun-fill me-2 text-warning-subtle"></i>
                                    <span class="fw-medium opacity-90">Selamat datang kembali,</span>
                                </p>
                                <h3 class="text-white fw-bold mb-0 h4" style="text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    {{ auth()->user()->nama }}
                                </h3>
                            </div>
                            
                            <!-- Dashboard Title -->
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill">
                                    <i class="bi bi-bar-chart-fill me-1"></i>
                                    Dashboard
                                </div>
                                <div class="badge bg-light text-warning px-3 py-1 rounded-pill fw-bold">
                                    Pengepul
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <p class="text-white mb-0 small opacity-90 fw-normal">
                                <i class="bi bi-check-circle-fill me-1 text-warning-subtle"></i>
                                Pantau arus kas, stok masuk, dan negosiasi pasar
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Actions & Quick Stats -->
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="d-flex flex-column align-items-md-end gap-3">
                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button class="btn btn-light fw-bold rounded-pill px-4 py-2 shadow-sm hover-lift d-flex align-items-center text-warning" onclick="window.location.reload()">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                <span>Refresh Pasar</span>
                            </button>
                        </div>
                        
                        <!-- Quick Info Card -->
                        <div class="glass-card rounded-4 px-4 py-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between gap-4">
                                <div class="text-start">
                                    <p class="text-white-50 mb-1 small">Last Update</p>
                                    <p class="text-white mb-0 fw-bold">
                                        <i class="bi bi-clock-fill me-1"></i>
                                        {{ now()->format('H:i') }}
                                    </p>
                                </div>
                                <div class="text-start">
                                    <p class="text-white-50 mb-1 small">Tanggal</p>
                                    <p class="text-white mb-0 fw-bold">
                                        <i class="bi bi-calendar-fill me-1"></i>
                                        {{ now()->format('d M') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Finance Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Tren Arus Kas</h5>
                    <div class="btn-group" role="group" aria-label="Time Filter">
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn active" data-range="30d" onclick="updateChartFilter(this, '30d')">30 Hari</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn" data-range="24h" onclick="updateChartFilter(this, '24h')">24 Jam</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn" data-range="4w" onclick="updateChartFilter(this, '4w')">4 Minggu</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn" data-range="12m" onclick="updateChartFilter(this, '12m')">12 Bulan</button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="financeChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Volume Chart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 p-4 pb-0">
                    <h5 class="fw-bold mb-0">Volume Transaksi</h5>
                </div>
                <div class="card-body p-4">
                    <div id="volumeChart" style="min-height: 350px;"></div>
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
                            <div class="list-group-item px-3 py-3 d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="activity-icon rounded-circle d-flex align-items-center justify-content-center bg-light"
                                        style="width: 40px; height: 40px;">
                                        @php
                                            $iconColor = match($activity->type) {
                                                'sale' => 'text-success',
                                                'purchase' => 'text-primary',
                                                'topup' => 'text-info',
                                                default => 'text-danger'
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
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($activity->date)->format('d M Y, H:i') }}
                                    </small>
                                </div>
                                <div class="text-end ms-3">
                                    @php
                                        // "Sale" in Petani is +
                                        // For Pengepul: Sale (+), Topup (+), Purchase (-)
                                        $sign = in_array($activity->type, ['sale', 'topup']) ? '+' : '-';
                                    @endphp
                                    <span class="d-block fw-bold text-dark">
                                        {{ $sign }} Rp {{ number_format($activity->amount, 0, ',', '.') }}
                                    </span>
                                    <span class="badge bg-success text-white rounded-pill mt-1">{{ ucfirst($activity->type) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-3 text-muted opacity-50">
                                    <i class="bi bi-clipboard-x display-4"></i>
                                </div>
                                <h6 class="text-muted">Belum ada aktivitas tercatat.</h6>
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
    /* Hero Card Effects */
    .hero-card {
        position: relative;
        overflow: hidden;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    /* Icon Container Effects */
    .icon-container {
        transition: all 0.3s ease;
        animation: iconFadeIn 0.8s ease-out;
    }
    
    .icon-container:hover {
        transform: scale(1.05) rotate(5deg);
    }
    
    @keyframes iconFadeIn {
        from {
            opacity: 0;
            transform: scale(0.8);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    /* Pulse Ring Animation */
    .pulse-ring {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        border: 3px solid rgba(255, 152, 0, 0.4);
        border-radius: 16px;
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
        50% {
            transform: translate(-50%, -50%) scale(1.1);
            opacity: 0.5;
        }
    }
    
    /* Glass Card Effect */
    .glass-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .glass-card:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }
    
    /* Hover Lift Effect */
    .hover-lift {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .hover-lift:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
    }
    
    /* General Stats */
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
    
    /* ApexCharts Text Color Override */
    .apexcharts-text,
    .apexcharts-xaxis-label,
    .apexcharts-yaxis-label,
    .apexcharts-legend-text {
        fill: #333333 !important;
    }
    
    .apexcharts-gridline {
        stroke: #e0e0e0 !important;
    }
</style>

<script>
    let financeChart, volumeChart;

    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData ?? []);

        if (chartData.labels && chartData.labels.length > 0) {
            // 1. Finance Chart
            var financeOptions = {
                series: [{
                    name: 'Pemasukan',
                    data: chartData.income
                }, {
                    name: 'Pengeluaran',
                    data: chartData.expense
                }],
                chart: {
                    type: 'area', // Premium Area Chart
                    height: 350,
                    toolbar: { show: false },
                    fontFamily: 'Instrument Sans, sans-serif',
                    dropShadow: {
                        enabled: true,
                        top: 3,
                        left: 0,
                        blur: 4,
                        opacity: 0.1
                    }
                },
                colors: ['#4caf50', '#f44336'], // Consistent Green/Red
                dataLabels: { enabled: false },
                stroke: { 
                    curve: 'smooth', 
                    width: 3,
                    lineCap: 'round'
                },
                markers: {
                    size: 5,
                    colors: ['#4caf50', '#f44336'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: { size: 7 }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.6,
                        opacityTo: 0.2,
                    }
                },
                xaxis: {
                    categories: chartData.labels,
                    axisBorder: { show: true, color: '#e0e0e0' },
                    axisTicks: { show: true, color: '#e0e0e0' },
                    labels: {
                        style: {
                            colors: '#333333',
                            fontSize: '12px',
                            fontWeight: 500
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#333333',
                            fontSize: '12px',
                            fontWeight: 500
                        },
                        formatter: function (val) {
                            return new Intl.NumberFormat('id-ID', { notation: "compact" }).format(val);
                        }
                    }
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 3,
                    xaxis: { lines: { show: true } },
                    yaxis: { lines: { show: true } }
                }, 
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: '#333333' },
                    markers: { width: 12, height: 12, radius: 3 }
                },
                tooltip: { 
                    theme: 'dark',
                    shared: false,
                    intersect: true,
                    style: { fontSize: '14px', fontFamily: 'Instrument Sans, sans-serif' },
                    y: { 
                        formatter: function (val) { return "Rp " + new Intl.NumberFormat('id-ID').format(val) },
                        title: { formatter: (seriesName) => seriesName + ': ' } 
                    },
                    marker: { show: true }
                }
            };
            financeChart = new ApexCharts(document.querySelector("#financeChart"), financeOptions);
            financeChart.render();

            // 2. Volume Chart (Buy vs Sell)
            var volumeOptions = {
                series: [{
                    name: 'Stok Masuk (Beli)',
                    data: chartData.kg_bought
                }, {
                    name: 'Stok Keluar (Jual)',
                    data: chartData.kg_sold
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: false },
                    fontFamily: 'Instrument Sans, sans-serif',
                    dropShadow: {
                        enabled: true,
                        top: 3,
                        left: 0,
                        blur: 4,
                        opacity: 0.1
                    }
                },
                colors: ['#2196F3', '#FF9800'], // Blue (Buy), Orange (Sell) - Distinction from Income/Expense
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 6,
                        borderRadiusApplication: 'end',
                        dataLabels: { position: 'top' }
                    },
                },
                dataLabels: { enabled: false },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.5,
                        gradientToColors: ['#64b5f6', '#ffb74d'], // Lighter variants
                        opacityFrom: 0.9,
                        opacityTo: 0.7,
                    }
                },
                xaxis: {
                    categories: chartData.labels,
                    axisBorder: { show: true, color: '#e0e0e0' },
                    axisTicks: { show: true, color: '#e0e0e0' },
                    labels: {
                        style: {
                            colors: '#333333',
                            fontSize: '12px',
                            fontWeight: 500
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#333333',
                            fontSize: '12px',
                            fontWeight: 500
                        }
                    }
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 3,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } }
                },
                legend: { 
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: '#333333' }
                },
                tooltip: { 
                    theme: 'dark',
                    shared: false,
                    intersect: true,
                    style: { fontSize: '14px', fontFamily: 'Instrument Sans, sans-serif' },
                    y: { formatter: function (val) { return val + " Kg" } }
                }
            };
            volumeChart = new ApexCharts(document.querySelector("#volumeChart"), volumeOptions);
            volumeChart.render();
        }
    });

    async function updateChartFilter(btn, range) {
        // UI Update
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active', 'btn-secondary'));
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.add('btn-outline-secondary'));
        
        document.querySelectorAll(`button[data-range="${range}"]`).forEach(b => {
             b.classList.add('active', 'btn-secondary');
             b.classList.remove('btn-outline-secondary');
        });

        // Fetch Data
        try {
            const response = await fetch(`{{ route('dashboard.chart-data') }}?range=${range}`);
            const data = await response.json();

            if (financeChart) {
                financeChart.updateOptions({
                    xaxis: { categories: data.labels }
                });
                financeChart.updateSeries([{
                    name: 'Pemasukan',
                    data: data.income
                }, {
                    name: 'Pengeluaran',
                    data: data.expense
                }]);
            }

            if (volumeChart) {
                volumeChart.updateOptions({
                    xaxis: { categories: data.labels }
                });
                volumeChart.updateSeries([{
                     name: 'Stok Masuk (Beli)',
                     data: data.kg_bought
                }, {
                     name: 'Stok Keluar (Jual)',
                     data: data.kg_sold
                }]);
            }

        } catch (error) {
            console.error('Error fetching chart data:', error);
            alert('Gagal memuat data grafik.');
        }
    }
</script>
@endsection
