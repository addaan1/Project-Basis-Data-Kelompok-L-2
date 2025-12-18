@extends('layouts.main')

@section('content')
<div class="container-fluid p-4">
    <!-- Welcome Section (Premium Hero) -->
    <div class="card border-0 mb-4 shadow-lg overflow-hidden position-relative hero-card" style="border-radius: 24px; background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 50%, #43a047 100%); min-height: 180px;">
        
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
                            <i class="bi bi-shield-lock-fill fs-3 text-success position-absolute top-50 start-50 translate-middle"></i>
                            <div class="pulse-ring" style="border-color: rgba(76, 175, 80, 0.4);"></div>
                        </div>
                        
                        <div class="flex-grow-1">
                            <!-- Greeting with Animation -->
                            <div class="greeting-text mb-2" style="animation: slideInLeft 0.6s ease-out;">
                                <p class="text-white mb-1 d-flex align-items-center" style="font-size: 0.9rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-stars me-2 text-warning"></i>
                                    <span class="fw-medium opacity-90">Selamat datang kembali,</span>
                                </p>
                                <h3 class="text-white fw-bold mb-0 h4" style="text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    {{ auth()->user()->nama }}
                                </h3>
                            </div>
                            
                            <!-- Dashboard Title -->
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill">
                                    <i class="bi bi-grid-fill me-1"></i>
                                    Console
                                </div>
                                <div class="badge bg-light text-success px-3 py-1 rounded-pill fw-bold">
                                    Administrator
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <p class="text-white mb-0 small opacity-90 fw-normal">
                                <i class="bi bi-check-circle-fill me-1 text-success-subtle"></i>
                                Monitoring Kesehatan Sistem & Aktivitas User
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Actions & Quick Stats -->
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="d-flex flex-column align-items-md-end gap-3">
                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button class="btn btn-light fw-bold rounded-pill px-4 py-2 shadow-sm hover-lift d-flex align-items-center text-success" onclick="window.location.reload()">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                <span>Live Refresh</span>
                            </button>
                        </div>
                        
                        <!-- Quick Info Card -->
                        <div class="glass-card rounded-4 px-4 py-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between gap-4">
                                <div class="text-start">
                                    <p class="text-white-50 mb-1 small">System Time</p>
                                    <p class="text-white mb-0 fw-bold">
                                        <i class="bi bi-clock-fill me-1"></i>
                                        {{ now()->format('H:i') }}
                                    </p>
                                </div>
                                <div class="text-start">
                                    <p class="text-white-50 mb-1 small">Date</p>
                                    <p class="text-white mb-0 fw-bold">
                                        <i class="bi bi-calendar-check-fill me-1"></i>
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

    <!-- System Health Stats (ETL + Realtime) -->
    <div class="row g-4 mb-4">
        <!-- GMV (Gross Merchandise Value) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-currency-dollar display-4 text-success"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Total GMV (System)</p>
                    <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($adminStats['gmv'] ?? 0, 0, ',', '.') }}</h3>
                    <small class="text-success fw-bold">
                        <i class="bi bi-graph-up-arrow me-1"></i>
                        Perputaran Uang
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-success" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Total User Base -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-people display-4 text-primary"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Total Pengguna</p>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($adminStats['total_users'] ?? 0) }}</h3>
                    <small class="text-primary fw-bold">
                        <span class="badge bg-primary-subtle text-primary rounded-pill">+{{ $adminStats['new_users_today'] ?? 0 }} Hari Ini</span>
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Daily Transactions -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-receipt display-4 text-info"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Transaksi Hari Ini</p>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($adminStats['total_tx_today'] ?? 0) }}</h3>
                    <small class="text-info fw-bold">
                        Activity Feed
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-info" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Pending Disputes/Nego -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-exclamation-triangle display-4 text-warning"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Negosiasi Menunggu</p>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($adminStats['pending_nego'] ?? 0) }}</h3>
                    <small class="text-warning fw-bold">
                        Potential bottlenecks
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-warning" style="width: 50%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Row -->
    <div class="row g-4 mb-4">
        <!-- GMV Trend Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Tren Gross Merchandise Value (GMV)</h5>
                    <div class="btn-group" role="group" aria-label="Time Filter">
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn active" data-range="30d" onclick="updateChartFilter(this, '30d')">30 Hari</button>
                         <button type="button" class="btn btn-outline-secondary btn-sm filter-btn" data-range="24h" onclick="updateChartFilter(this, '24h')">24 Jam</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn" data-range="4w" onclick="updateChartFilter(this, '4w')">4 Minggu</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn" data-range="12m" onclick="updateChartFilter(this, '12m')">12 Bulan</button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="gmvChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Transaction Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h5 class="fw-bold mb-0">Status Negosiasi (Data Warehouse)</h5>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div id="statusChart" class="w-100"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- User Management (Quick View) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Manajemen Pengguna Terbaru</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                        <i class="bi bi-people-fill me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 0.85rem; border-bottom: 2px solid #e9ecef;">
                                <tr>
                                    <th class="ps-4 py-3 border-0 rounded-start">User Info</th>
                                    <th class="py-3 border-0">Role</th>
                                    <th class="py-3 border-0">Joined Date</th>
                                    <th class="pe-4 py-3 border-0 text-end rounded-end">Action</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 1rem;">
                                @forelse($latestUsers ?? [] as $user)
                                    <tr class="border-bottom hover-shadow-sm transition-all">
                                        <td class="ps-4 py-3 border-0">
                                            <div class="d-flex align-items-center">
                                                <div class="position-relative">
                                                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                                                        <i class="bi bi-person-fill fs-4"></i>
                                                    </div>
                                                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-light rounded-circle">
                                                        <span class="visually-hidden">Active</span>
                                                    </span>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="mb-1 fw-bold text-dark">{{ $user->nama ?? $user->name ?? 'No Name' }}</h6>
                                                    <span class="d-block text-secondary small">{{ $user->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 border-0">
                                            @php
                                                $roleClasses = match($user->peran) {
                                                    'admin' => 'bg-dark text-white',
                                                    'petani' => 'bg-success text-white',
                                                    'pengepul' => 'bg-warning text-dark',
                                                    default => 'bg-secondary text-white'
                                                };
                                            @endphp
                                            <span class="badge {{ $roleClasses }} rounded-pill px-3 py-2 fw-normal shadow-sm">
                                                {{ ucfirst($user->peran) }}
                                            </span>
                                        </td>
                                        <td class="py-3 border-0 text-secondary">
                                            {{ $user->created_at->translatedFormat('d M Y') }}
                                            <small class="d-block text-muted" style="font-size: 0.75rem;">{{ $user->created_at->format('H:i') }}</small>
                                        </td>
                                        <td class="pe-4 py-3 border-0 text-end">
                                            <button class="btn btn-light text-success btn-sm rounded-circle shadow-sm" data-bs-toggle="tooltip" title="Edit details">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted bg-light rounded-3">
                                            <i class="bi bi-people display-4 opacity-50 mb-3 d-block"></i>
                                            Belum ada data pengguna.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="text-center mt-3 p-3 bg-light rounded-3">
                             <small class="text-muted">Fitur manajemen user lengkap tersedia di menu "Users"</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links (Control Panel) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                 <div class="card-header bg-white border-bottom-0 p-4 pb-0">
                    <h5 class="fw-bold mb-0">Control Panel</h5>
                </div>
                <div class="card-body p-4">
                     <div class="d-grid gap-3">
                        <a href="{{ route('admin.backup') }}" class="btn btn-light text-start p-3 border-0 shadow-sm rounded-4 d-flex align-items-center hover-scale">
                            <div class="bg-success-subtle text-success rounded-circle p-3 me-3">
                                <i class="bi bi-database-check fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark h6 mb-0">Database Backup</div>
                                <small class="text-secondary">Download JSON Dump</small>
                            </div>
                        </a>

                        <a href="{{ route('dashboard.data') }}" target="_blank" class="btn btn-light text-start p-3 border-0 shadow-sm rounded-4 d-flex align-items-center hover-scale">
                            <div class="bg-primary-subtle text-primary rounded-circle p-3 me-3">
                                <i class="bi bi-braces fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark h6 mb-0">Raw JSON API</div>
                                <small class="text-secondary">Debug dashboard data</small>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.logs') }}" target="_blank" class="btn btn-light text-start p-3 border-0 shadow-sm rounded-4 d-flex align-items-center hover-scale">
                            <div class="bg-warning-subtle text-warning rounded-circle p-3 me-3">
                                <i class="bi bi-terminal fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark h6 mb-0">System Logs</div>
                                <small class="text-secondary">View laravel.log</small>
                            </div>
                        </a>
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

    .stat-card { transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-5px); }
    .hover-shadow:hover { background-color: #f8f9fa; border-color: transparent; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .hover-bg-light:hover { background-color: #f8f9fa; }
    
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
    let gmvChart; 

    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData ?? []);

        // 1. GMV Line Chart
        if (chartData.trend_labels) {
            const gmvOptions = {
                series: [{
                    name: 'GMV (Rp)',
                    data: chartData.trend_gmv
                }],
                chart: {
                    type: 'area', // Premium Area
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
                colors: ['#2E7D32'], // Official Green
                stroke: { 
                    curve: 'smooth', 
                    width: 3 
                },
                markers: {
                    size: 5,
                    colors: ['#2E7D32'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: { size: 7 }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.6,
                        opacityTo: 0.1,
                    }
                },
                xaxis: {
                    categories: chartData.trend_labels,
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
                        formatter: function (value) {
                            return new Intl.NumberFormat('id-ID', { notation: "compact" }).format(value);
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
                   labels: { colors: '#333333' } 
                },
                tooltip: { 
                    theme: 'dark',
                    style: { fontSize: '14px', fontFamily: 'Instrument Sans, sans-serif' },
                    y: { formatter: function (val) { return "Rp " + new Intl.NumberFormat('id-ID').format(val) } } 
                }
            };

            gmvChart = new ApexCharts(document.querySelector("#gmvChart"), gmvOptions);
            gmvChart.render();
        }

        // 2. Status Donut Chart
        if (chartData.status_distribution) {
            // Transform associative array to arrays
            const labels = Object.keys(chartData.status_distribution);
            const series = Object.values(chartData.status_distribution);

            const statusOptions = {
                series: series,
                labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)), 
                chart: {
                    type: 'donut',
                    height: 350,
                    fontFamily: 'Instrument Sans, sans-serif',
                    dropShadow: {
                        enabled: true,
                        top: 2,
                        left: 0,
                        blur: 3,
                        opacity: 0.1
                    }
                },
                colors: ['#FFC107', '#4CAF50', '#2196F3', '#FF5722', '#9E9E9E'], 
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                name: {
                                   fontSize: '14px',
                                   fontFamily: 'Instrument Sans, sans-serif',
                                },
                                value: {
                                   fontSize: '24px',
                                   fontFamily: 'Instrument Sans, sans-serif',
                                   fontWeight: 700,
                                   formatter: function(val) {
                                       return val
                                   }
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '14px',
                                    fontWeight: 500,
                                    color: '#666',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                    }
                                }
                            }
                        }
                    }
                },
                legend: { 
                    position: 'bottom',
                    fontFamily: 'Instrument Sans, sans-serif',
                    markers: { width: 10, height: 10, radius: 12 },
                    itemMargin: { horizontal: 10, vertical: 5 }
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['#fff']
                },
                tooltip: {
                    theme: 'dark',
                    style: { fontSize: '14px', fontFamily: 'Instrument Sans, sans-serif' }
                }
            };

            const statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
            statusChart.render();
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

            if (gmvChart && data.trend_labels) {
                gmvChart.updateOptions({
                    xaxis: { categories: data.trend_labels }
                });
                gmvChart.updateSeries([{
                    name: 'GMV (Rp)',
                    data: data.trend_gmv
                }]);
            }
        } catch (error) {
            console.error('Error fetching admin chart data:', error);
            alert('Gagal memuat data grafik admin.');
        }
    }
</script>
@endsection
