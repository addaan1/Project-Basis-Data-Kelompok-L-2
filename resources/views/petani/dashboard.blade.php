@extends('layouts.main')

@section('content')
<div class="container-fluid p-4">
    <!-- Welcome Section (Premium Hero) -->
    <div class="card border-0 mb-4 shadow-lg overflow-hidden position-relative hero-card" style="border-radius: 24px; background: linear-gradient(135deg, #66bb6a 0%, #43a047 50%, #2e7d32 100%); min-height: 180px;">
        
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
                            <i class="bi bi-speedometer2 fs-3 text-success position-absolute top-50 start-50 translate-middle"></i>
                            <div class="pulse-ring"></div>
                        </div>
                        
                        <div class="flex-grow-1">
                            <!-- Greeting with Animation -->
                            <div class="greeting-text mb-2" style="animation: slideInLeft 0.6s ease-out;">
                                <p class="text-white mb-1 d-flex align-items-center" style="font-size: 0.9rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-sun-fill me-2 text-warning"></i>
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
                                <div class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                                    Petani
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <p class="text-white mb-0 small opacity-90 fw-normal">
                                <i class="bi bi-check-circle-fill me-1 text-warning"></i>
                                Pantau aktivitas penjualan dan stok beras Anda secara real-time
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Actions & Quick Stats -->
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="d-flex flex-column align-items-md-end gap-3">
                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button class="btn btn-light fw-bold rounded-pill px-4 py-2 shadow-sm hover-lift d-flex align-items-center" onclick="window.location.reload()">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                <span>Refresh</span>
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
            border: 3px solid rgba(76, 175, 80, 0.4);
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
        
        /* Badge Animations */
        .badge {
            animation: badgeFadeIn 1s ease-out;
        }
        
        @keyframes badgeFadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>


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
     <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Finance Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Tren Pendapatan</h5>
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
                    <h5 class="fw-bold mb-0">Volume Panen Terjual</h5>
                </div>
                <div class="card-body p-4">
                    <div id="volumeChart" style="min-height: 350px;"></div>
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
                                    <div class="activity-icon rounded-circle d-flex align-items-center justify-content-center bg-light"
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
                                    <h6 class="mb-1 fw-bold text-dark">{{ $activity->description }}</h6>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($activity->date)->format('d M Y, H:i') }}
                                    </small>
                                </div>
                                <div class="text-end ms-3">
                                    <span class="d-block fw-bold text-dark">
                                        {{ ($activity->type == 'sale' || $activity->type == 'topup') ? '+' : '-' }} Rp {{ number_format($activity->amount, 0, ',', '.') }}
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

        <!-- Negotiation Status Table -->
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 text-white" style="background: linear-gradient(135deg, #66bb6a, #43a047);">
                <div class="card-header border-0 bg-transparent p-4 pb-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-chat-dots me-2"></i>Status Negosiasi</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="small text-uppercase" style="border-bottom: 2px solid rgba(255,255,255,0.3);">
                                <tr>
                                    <th class="ps-0 text-white fw-bold">Partner</th>
                                    <th class="text-white fw-bold">Jml (Kg)</th>
                                    <th class="text-end pe-0 text-white fw-bold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($negotiationsSummary as $neg)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                        <td class="ps-0 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-white rounded-circle p-2 me-3 text-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person-fill fs-5"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block fw-bold">{{ $neg->label }}</span>
                                                    <small class="text-white-50" style="font-size: 0.75rem;">{{ $neg->product_name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 fw-medium">{{ number_format($neg->jumlah_kg) }}</td>
                                        <td class="text-end pe-0 py-3">
                                            @php
                                                $statusClass = match($neg->status) {
                                                    'Menunggu' => 'bg-warning text-dark',
                                                    'Disetujui' => 'bg-white text-success',
                                                    'Ditolak' => 'bg-danger text-white',
                                                    default => 'bg-secondary text-white'
                                                };
                                                $iconStatus = match($neg->status) {
                                                    'Menunggu' => 'bi-hourglass-split',
                                                    'Disetujui' => 'bi-check-circle-fill',
                                                    'Ditolak' => 'bi-x-circle-fill',
                                                    default => 'bi-question-circle'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 shadow-sm">
                                                <i class="bi {{ $iconStatus }} me-1"></i> {{ $neg->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <div class="mb-3" style="opacity: 0.7;">
                                                <i class="bi bi-inbox fs-1 text-white"></i>
                                            </div>
                                            <p class="text-white fw-medium mb-0">Tidak ada negosiasi aktif</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4 text-center">
                        <a href="{{ route('negosiasi.index') }}" class="btn btn-sm btn-light rounded-pill px-4 text-success fw-bold shadow-sm hover-scale">
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
    
    /* Negotiation Table Transparency */
    .table-responsive .table {
        background-color: transparent !important;
    }
    .table-responsive .table thead,
    .table-responsive .table tbody,
    .table-responsive .table tr,
    .table-responsive .table td,
    .table-responsive .table th {
        background-color: transparent !important;
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
                    name: 'Pendapatan',
                    data: chartData.income
                }, {
                    name: 'Pengeluaran',
                    data: chartData.expense
                }],
                chart: {
                    type: 'area',
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
                colors: ['#4caf50', '#f44336'],
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
                    hover: {
                        size: 7
                    }
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
                    axisBorder: { 
                        show: true,
                        color: '#e0e0e0'
                    },
                    axisTicks: { 
                        show: true,
                        color: '#e0e0e0'
                    },
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
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: {
                        colors: '#333333'
                    },
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 3
                    }
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 3,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                tooltip: { 
                    theme: 'dark',
                    shared: false,
                    intersect: true,
                    followCursor: false,
                    style: {
                        fontSize: '14px',
                        fontFamily: 'Instrument Sans, sans-serif'
                    },
                    x: {
                        show: true
                    },
                    y: { 
                        formatter: function (val) { 
                            return "Rp " + new Intl.NumberFormat('id-ID').format(val) 
                        },
                        title: {
                            formatter: function (seriesName) {
                                return seriesName + ': '
                            }
                        }
                    },
                    marker: {
                        show: true
                    }
                }
            };
            financeChart = new ApexCharts(document.querySelector("#financeChart"), financeOptions);
            financeChart.render();

            // 2. Volume Chart
            var volumeOptions = {
                series: [{
                    name: 'Terjual (Kg)',
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
                colors: ['#8bc34a'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '60%',
                        borderRadius: 6,
                        borderRadiusApplication: 'end',
                        dataLabels: {
                            position: 'top'
                        }
                    },
                },
                dataLabels: { 
                    enabled: false 
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.5,
                        gradientToColors: ['#aed581'],
                        opacityFrom: 0.9,
                        opacityTo: 0.7,
                    }
                },
                xaxis: {
                    categories: chartData.labels,
                    axisBorder: {
                        show: true,
                        color: '#e0e0e0'
                    },
                    axisTicks: {
                        show: true,
                        color: '#e0e0e0'
                    },
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
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                legend: { 
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: {
                        colors: '#333333'
                    }
                },
                tooltip: { 
                    theme: 'dark',
                    shared: false,
                    intersect: true,
                    style: {
                        fontSize: '14px',
                        fontFamily: 'Instrument Sans, sans-serif'
                    },
                    x: {
                        show: true
                    },
                    y: { 
                        formatter: function (val) { 
                            return val + " Kg" 
                        },
                        title: {
                            formatter: function (seriesName) {
                                return seriesName + ': '
                            }
                        }
                    },
                    marker: {
                        show: true
                    }
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
                    name: 'Pendapatan',
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
                     name: 'Terjual (Kg)',
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
