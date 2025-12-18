@extends('layouts.main')

@section('content')
<div class="container-fluid p-4">
    <!-- Welcome Section (Premium Hero) -->
    <div class="card border-0 mb-4 shadow-lg overflow-hidden position-relative hero-card" style="border-radius: 24px; background: linear-gradient(135deg, #FF9800 0%, #F57C00 50%, #E65100 100%); min-height: 180px;">
        
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
                                    <i class="bi bi-sun-fill me-2 text-warning" style="color: #FFD54F !important;"></i>
                                    <span class="fw-medium opacity-90">Selamat datang kembali,</span>
                                </p>
                                <h3 class="text-white fw-bold mb-0 h4" style="text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    {{ auth()->user()->nama ?? 'Mitra Pengepul' }}
                                </h3>
                            </div>
                            
                            <!-- Dashboard Title -->
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill">
                                    <i class="bi bi-bar-chart-fill me-1"></i>
                                    Dashboard
                                </div>
                                <div class="badge bg-white text-orange px-3 py-1 rounded-pill fw-bold" style="color: #E65100;">
                                    Pengepul
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <p class="text-white mb-0 small opacity-90 fw-normal">
                                <i class="bi bi-check-circle-fill me-1" style="color: #FFD54F;"></i>
                                Pantau arus kas, stok gudang, dan negosiasi pasar
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Actions & Quick Stats -->
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="d-flex flex-column align-items-md-end gap-3">
                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button class="btn btn-light fw-bold rounded-pill px-4 py-2 shadow-sm hover-lift d-flex align-items-center text-orange" style="color: #E65100;" onclick="window.location.reload()">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                <span>Refresh Data</span>
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
        .hero-card { position: relative; overflow: hidden; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
        @keyframes slideInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
        
        /* Icon Container */
        .icon-container { transition: all 0.3s ease; animation: iconFadeIn 0.8s ease-out; }
        .icon-container:hover { transform: scale(1.05) rotate(5deg); }
        @keyframes iconFadeIn { from { opacity: 0; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }
        
        /* Pulse Ring */
        .pulse-ring {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 100%; height: 100%;
            border: 3px solid rgba(255, 152, 0, 0.4);
            border-radius: 16px;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse { 0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 1; } 50% { transform: translate(-50%, -50%) scale(1.1); opacity: 0.5; } }
        
        /* Glass Card */
        .glass-card { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); transition: all 0.3s ease; }
        .glass-card:hover { background: rgba(255, 255, 255, 0.2); transform: translateY(-2px); }
        
        /* Hover Lift */
        .hover-lift { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important; }
        
        /* Stat Card */
        .stat-card { transition: transform 0.3s ease, box-shadow 0.3s ease; background: #fff; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important; }
        
        /* ApexCharts */
        .apexcharts-text { fill: #333333 !important; }
        .apexcharts-gridline { stroke: #e0e0e0 !important; }
    </style>

    <!-- Business Stats Grid -->
    <div class="row g-4 mb-4">
        <!-- Net Cashflow -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-wallet2 display-4 text-success"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Net Cashflow</p>
                    <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($saldo ?? 0, 0, ',', '.') }}</h3>
                    <small class="text-success fw-bold">
                        <i class="bi bi-arrow-up-circle me-1"></i> Active Balance
                    </small>
                </div>
                 <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-success" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Volume Pembelian -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-cart-plus display-4 text-primary"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Volume Pembelian (Harian)</p>
                    <h3 class="fw-bold text-dark mb-1">
                        {{ number_format($activities->where('type', 'purchase')->where('date', '>=', \Carbon\Carbon::today())->sum('amount')/1000 ?? 0, 0, ',', '.') }} Kg
                    </h3>
                    <small class="text-primary fw-bold">
                        <span class="badge bg-primary-subtle text-primary rounded-pill">Stok Masuk</span>
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-primary" style="width: 70%"></div>
                </div>
            </div>
        </div>

        <!-- Kapasitas Gudang -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-building display-4 text-orange" style="color: #FD7E14;"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Kapasitas Gudang</p>
                    <h3 class="fw-bold text-dark mb-1">
                         {{ number_format($inventoryTon ?? 0, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">/ 10k Kg</span>
                    </h3>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <div class="progress flex-grow-1" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: {{ $capacityPercent ?? 0 }}%"></div>
                        </div>
                        <span class="small fw-bold text-muted">{{ $capacityPercent ?? 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tawaran Saya -->
        <div class="col-12 col-sm-6 col-xl-3">
             <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-chat-quote display-4 text-info"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Tawaran Saya</p>
                    <h3 class="fw-bold text-dark mb-1">{{ $negotiationsSummary->where('status', 'Menunggu')->count() }}</h3>
                    <small class="text-info fw-bold">
                        <span class="badge bg-info-subtle text-info rounded-pill">Status Pending</span>
                    </small>
                </div>
                 <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-info" style="width: 50%"></div>
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
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn active" data-range="30d" onclick="updateChartFilter(this, '30d')">30 Hari</button>
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

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Recent Activity Feed -->
        <div class="col-lg-8">
            <div class="card h-100 border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-activity me-2"></i>Aktivitas Terakhir</h5>
                </div>
                <div class="card-body p-0">
                     <div class="table-responsive p-3">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="ps-4 border-0 text-secondary x-small fw-bold text-uppercase">Deskripsi</th>
                                    <th class="border-0 text-secondary x-small fw-bold text-uppercase">Tanggal</th>
                                    <th class="border-0 text-secondary x-small fw-bold text-uppercase text-end">Jumlah</th>
                                    <th class="pe-4 border-0 text-secondary x-small fw-bold text-uppercase text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities->take(5) ?? [] as $activity)
                                <tr>
                                    <td class="ps-4 border-bottom-0">
                                        <div class="d-flex align-items-center">
                                             <div class="rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center bg-light" 
                                                style="width: 40px; height: 40px;">
                                                @php
                                                    $iconColor = match($activity->type) {
                                                        'sale' => 'text-success', 'purchase' => 'text-primary', 'topup' => 'text-info', default => 'text-danger'
                                                    };
                                                    $icon = match($activity->type) {
                                                        'sale' => 'bi-arrow-up-right', 'purchase' => 'bi-arrow-down-left', 'topup' => 'bi-wallet2', default => 'bi-circle'
                                                    };
                                                @endphp
                                                <i class="bi {{ $icon }} fs-5 {{ $iconColor }}"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-bold small">{{ Str::limit($activity->description, 30) }}</h6>
                                                <small class="text-secondary x-small">{{ ucfirst($activity->type) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="border-bottom-0 text-secondary small">
                                        {{ isset($activity->date) ? \Carbon\Carbon::parse($activity->date)->format('d M, H:i') : '-' }}
                                    </td>
                                    <td class="border-bottom-0 text-end fw-bold text-dark small">
                                        {{ in_array($activity->type, ['sale', 'topup']) ? '+' : '-' }} Rp {{ number_format(abs($activity->amount ?? 0), 0, ',', '.') }}
                                    </td>
                                    <td class="pe-4 border-bottom-0 text-center">
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2">Selesai</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted small">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Negotiation Status -->
        <div class="col-lg-4">
             <div class="card h-100 border-0 shadow-sm rounded-4 text-white" style="background: linear-gradient(135deg, #FF9800, #F57C00);">
                <div class="card-header border-0 bg-transparent p-4 pb-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-chat-dots me-2"></i>Status Negosiasi</h5>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush bg-transparent">
                         @forelse($negotiationsSummary->take(4) ?? [] as $nego)
                        <div class="list-group-item bg-transparent border-bottom border-white border-opacity-25 text-white py-3 px-0">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="badge bg-white text-orange fw-bold" style="color: #E65100;">{{ $nego->label ?? 'Negosiasi' }}</span>
                                <small class="text-white text-opacity-75">{{ \Carbon\Carbon::parse($nego->date)->diffForHumans() }}</small>
                            </div>
                            <h6 class="mb-1 fw-bold">{{ $nego->product_name ?? 'Beras' }}</h6>
                             <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fs-5 fw-bold">Rp {{ number_format($nego->amount ?? 0, 0, ',', '.') }}</span>
                                <div class="px-2 py-1 bg-white bg-opacity-20 rounded text-center" style="min-width: 60px;">
                                    <small>{{ $nego->jumlah_kg ?? 0 }} Kg</small>
                                </div>
                            </div>
                            <div class="mt-2 text-white text-opacity-75 small">
                                <i class="bi bi-person me-1"></i> {{ $nego->partner ?? 'Mitra' }}
                            </div>
                        </div>
                        @empty
                        <div class="p-4 text-center text-white">Tidak ada negosiasi aktif.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
</style>

<script>
    let financeChart, volumeChart;

    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData ?? []);
        
        if (chartData.labels && chartData.labels.length > 0) {
            // 1. Finance Chart
            var financeOptions = {
                series: [{ name: 'Pemasukan', data: chartData.income }, { name: 'Pengeluaran', data: chartData.expense }],
                chart: { 
                    type: 'area', height: 350, toolbar: { show: false }, 
                    fontFamily: 'Instrument Sans, sans-serif',
                    dropShadow: { enabled: true, top: 3, left: 0, blur: 4, opacity: 0.1 }
                },
                colors: ['#4caf50', '#f44336'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.6, opacityTo: 0.1 } },
                xaxis: { 
                    categories: chartData.labels, 
                    axisBorder: { show: true, color: '#e0e0e0' }, axisTicks: { show: true, color: '#e0e0e0' }
                },
                yaxis: { labels: { formatter: val => new Intl.NumberFormat('id-ID', { notation: "compact" }).format(val) } },
                grid: { borderColor: '#e0e0e0', strokeDashArray: 3 },
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: { theme: 'dark', y: { formatter: val => "Rp " + new Intl.NumberFormat('id-ID').format(val) } }
            };
            financeChart = new ApexCharts(document.querySelector("#financeChart"), financeOptions);
            financeChart.render();

            // 2. Volume Chart
            var volumeOptions = {
                series: [{ name: 'Stok Masuk', data: chartData.kg_bought }, { name: 'Stok Keluar', data: chartData.kg_sold }],
                chart: { 
                    type: 'bar', height: 350, toolbar: { show: false },
                    fontFamily: 'Instrument Sans, sans-serif',
                    dropShadow: { enabled: true, top: 3, left: 0, blur: 4, opacity: 0.1 }
                },
                colors: ['#29B6F6', '#FFA726'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
                dataLabels: { enabled: false },
                xaxis: { 
                    categories: chartData.labels, 
                    axisBorder: { show: true, color: '#e0e0e0' }
                },
                grid: { borderColor: '#e0e0e0', strokeDashArray: 3 },
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: { theme: 'dark', y: { formatter: val => val + " Kg" } }
            };
            volumeChart = new ApexCharts(document.querySelector("#volumeChart"), volumeOptions);
            volumeChart.render();
        }
    });

    async function updateChartFilter(btn, range) {
        document.querySelectorAll('.filter-btn').forEach(b => { b.classList.remove('active', 'btn-secondary'); b.classList.add('btn-outline-secondary'); });
        btn.classList.add('active', 'btn-secondary'); btn.classList.remove('btn-outline-secondary');

        try {
            const response = await fetch(`{{ route('dashboard.chart-data') }}?range=${range}`);
            const data = await response.json();
            if (financeChart) {
                financeChart.updateOptions({ xaxis: { categories: data.labels } });
                financeChart.updateSeries([{ data: data.income }, { data: data.expense }]);
            }
        } catch (error) { console.error('Error:', error); }
    }
</script>
@endsection