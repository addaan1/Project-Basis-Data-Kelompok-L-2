@extends('layouts.main')

@section('content')
<div class="dashboard-wrapper min-vh-100 p-4">
    
    <!-- POWER PIXEL LAYOUT -->
    
    <!-- Top Section: Stats (Left 4) & Finance Chart (Right 8) -->
    <div class="row g-4 mb-4 animate-slide-down">
        <!-- LEFT COLUMN: 2x2 Stats Grid -->
        <div class="col-xl-4 col-lg-5">
            <div class="row g-3 h-100 align-content-between">
                <!-- 1. Net Cashflow -->
                <div class="col-6">
                    <div class="modern-stat-card bg-green-gradient text-white h-100 position-relative overflow-hidden rounded-4 shadow-sm border-bottom-yellow p-3 d-flex flex-column justify-content-between">
                         <div class="d-flex justify-content-between align-items-start mb-1">
                            <i class="bi bi-wallet2 fs-2 opacity-50"></i>
                            <span class="badge bg-white bg-opacity-20 rounded-pill x-small">Saldo</span>
                         </div>
                         <div class="mt-2">
                            <h5 class="fw-bold mb-0 text-truncate" title="Rp {{ number_format($saldo ?? 0, 0, ',', '.') }}">
                                {{ number_format(($saldo ?? 0)/1000000, 1, ',', '.') }}M
                            </h5>
                            <small class="opacity-75 x-small">Total Saldo</small>
                        </div>
                    </div>
                </div>

                <!-- 2. Transaksi -->
                <div class="col-6">
                     <div class="modern-stat-card bg-green-gradient text-white h-100 position-relative overflow-hidden rounded-4 shadow-sm border-bottom-blue p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <i class="bi bi-cart-check fs-2 opacity-50"></i>
                             <span class="badge bg-white bg-opacity-20 rounded-pill x-small">Trx</span>
                        </div>
                        <div class="mt-2">
                             <h5 class="fw-bold mb-0">{{ $activities->where('type', 'purchase')->count() ?? 0 }}</h5>
                             <small class="opacity-75 x-small">Pembelian</small>
                        </div>
                    </div>
                </div>

                <!-- 3. Gudang -->
                <div class="col-6">
                     <div class="modern-stat-card bg-green-gradient text-white h-100 position-relative overflow-hidden rounded-4 shadow-sm border-bottom-cyan p-3 d-flex flex-column justify-content-between">
                         <div class="d-flex justify-content-between align-items-start mb-1">
                            <i class="bi bi-building fs-2 opacity-50"></i>
                             <span class="badge bg-white bg-opacity-20 rounded-pill x-small">Stok</span>
                        </div>
                        <div class="mt-2">
                             <h5 class="fw-bold mb-0">
                                {{ number_format($inventoryTon ?? 0, 0, ',', '.') }}<small class="fs-6">t</small>
                            </h5>
                             <div class="progress rounded-pill bg-white bg-opacity-25 mt-1" style="height: 4px;">
                                <div class="progress-bar bg-white rounded-pill" style="width: {{ $capacityPercent ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- 4. Tawaran -->
                <div class="col-6">
                     <div class="modern-stat-card bg-green-gradient text-white h-100 position-relative overflow-hidden rounded-4 shadow-sm border-bottom-orange p-3 d-flex flex-column justify-content-between">
                         <div class="d-flex justify-content-between align-items-start mb-1">
                            <i class="bi bi-chat-quote fs-2 opacity-50"></i>
                             <span class="badge bg-white bg-opacity-20 rounded-pill x-small">Nego</span>
                        </div>
                        <div class="mt-2">
                             <h5 class="fw-bold mb-0">{{ $negotiationsSummary->where('status', 'Menunggu')->count() }}</h5>
                             <small class="opacity-75 x-small">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Finance Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="glass-card p-4 h-100 shadow-sm border-0 d-flex flex-column">
                 <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Analisis Keuangan</h5>
                        <small class="text-secondary">Ringkasan Pemasukan & Pengeluaran</small>
                    </div>
                    <div class="d-flex gap-2">
                         <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold text-success active filter-btn" data-range="30d" onclick="updateChartFilter(this, '30d')">30D</button>
                         <button class="btn btn-sm btn-light rounded-pill px-3 text-secondary filter-btn" data-range="4w" onclick="updateChartFilter(this, '4w')">4W</button>
                    </div>
                </div>
                <div class="flex-grow-1" id="financeChart" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Table (Left 8) & Volume Donut (Right 4) -->
    <div class="row g-4 animate-slide-up" style="animation-delay: 0.1s;">
        <!-- Left: Recent Transactions Table -->
        <div class="col-xl-8 col-lg-7">
            <div class="glass-card p-0 h-100 shadow-sm border-0 overflow-hidden">
                <div class="p-4 border-bottom border-light">
                     <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">Transaksi Terakhir</h5>
                        <a href="{{ route('transaksi.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Lihat Semua</a>
                    </div>
                </div>
                <div class="table-responsive">
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
                                         <div class="icon-box-sm rounded-circle me-3 flex-shrink-0 {{ match($activity->type) {
                                            'sale' => 'bg-success-subtle text-success',
                                            'purchase' => 'bg-danger-subtle text-danger',
                                            'topup' => 'bg-info-subtle text-info',
                                            default => 'bg-light text-secondary'
                                        } }}">
                                            <i class="bi {{ match($activity->type) {
                                                'sale' => 'bi-arrow-up-right',
                                                'purchase' => 'bi-arrow-down-left',
                                                'topup' => 'bi-wallet2',
                                                default => 'bi-circle'
                                            } }}"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark fw-bold small">{{ Str::limit($activity->description, 30) }}</h6>
                                            <small class="text-secondary x-small">{{ $activity->type }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="border-bottom-0 text-secondary small">
                                    {{ isset($activity->date) ? \Carbon\Carbon::parse($activity->date)->format('d M, H:i') : '-' }}
                                </td>
                                <td class="border-bottom-0 text-end fw-bold {{ in_array($activity->type, ['sale', 'topup']) ? 'text-success' : 'text-danger' }} small">
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

        <!-- Right: Volume Chart (Bar) -->
        <div class="col-xl-4 col-lg-5">
            <div class="glass-card p-4 h-100 shadow-sm border-0 d-flex flex-column">
                <div class="mb-3 text-center">
                    <h5 class="fw-bold text-dark mb-0">Volume Transaksi (kg)</h5>
                    <small class="text-secondary">Rasio Jual Beli</small>
                </div>
                <div class="flex-grow-1 position-relative">
                     <div id="volumeChart" style="width: 100%; min-height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* GLOBAL THEME VARS */
    :root {
        --rice-green: #4CAF50;
        --rice-dark: #2E7D32;
        --rice-orange: #FF9800;
        --rice-blue: #2196F3;
    }

    .dashboard-wrapper {
        background-image: url('{{ asset('images/Background.png') }}');
        background-size: cover;
        background-position: center top;
        background-attachment: fixed;
    }

    /* UTILITIES */
    .x-small { font-size: 0.7rem; letter-spacing: 0.5px; }
    
    /* MODERN SOLID CARD TWEAKS */
    .bg-green-gradient { background: linear-gradient(135deg, #43A047 0%, #2E7D32 100%); }
    .modern-stat-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .modern-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(46, 125, 50, 0.25) !important; }
    
    /* GLASS & TABLE */
    .glass-card {
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(24px);
        border: 1px solid rgba(255,255,255,0.8);
        border-radius: 16px;
    }
    .table-hover tbody tr:hover { background-color: rgba(76, 175, 80, 0.05); }
    
    /* BORDERS */
    .border-bottom-yellow { border-bottom: 4px solid #FFD54F; }
    .border-bottom-blue { border-bottom: 4px solid #42A5F5; }
    .border-bottom-cyan { border-bottom: 4px solid #26C6DA; }
    .border-bottom-orange { border-bottom: 4px solid #FFA726; }
    
    /* CUSTOM ICON BOX */
    .icon-box-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    
    @media (max-width: 991px) {
        .dashboard-wrapper { padding: 1rem !important; }
    }

    /* NUCLEAR FIX for ApexCharts Tooltip Visibility */
    html body .apexcharts-tooltip.apexcharts-theme-light, 
    html body .apexcharts-tooltip.apexcharts-theme-light .apexcharts-tooltip-title,
    html body .apexcharts-tooltip.apexcharts-theme-light .apexcharts-text-y-value,
    html body .apexcharts-tooltip.apexcharts-theme-light .apexcharts-tooltip-text-y-label,
    html body .apexcharts-tooltip.apexcharts-theme-light .apexcharts-tooltip-text-value,
    html body .apexcharts-tooltip.apexcharts-theme-light .apexcharts-tooltip-text-label {
        color: #0F172A !important;
        background: #FFFFFF !important;
        border-color: #E2E8F0 !important;
    }
    html body .apexcharts-tooltip.apexcharts-theme-light .apexcharts-tooltip-title {
        background: #F1F5F9 !important;
        border-bottom: 1px solid #E2E8F0 !important;
        font-weight: 700 !important;
        color: #1E293B !important;
    }
    .apexcharts-tooltip *, .apexcharts-tooltip-text * { color: inherit !important; }

    /* --- MODERN CUTOUT CARD DESIGN --- */
    .modern-cutout-card {
        position: relative;
        border-radius: 24px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        transition: transform 0.2s;
        /* create the scoop at top right */
        -webkit-mask-image: radial-gradient(circle at top right, transparent 32px, black 33px);
        mask-image: radial-gradient(circle at top right, transparent 32px, black 33px);
    }
    .modern-cutout-card:hover { text-decoration: none; transform: translateY(-3px); }

    /* Variant: Primary (Green) */
    .card-theme-green {
        background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
        color: #FFFFFF;
        box-shadow: 0 10px 15px -3px rgba(46, 125, 50, 0.3);
        border: none;
    }
    /* Variant: White/Glass */
    .card-theme-white {
        background: #FFFFFF;
        color: #1E293B;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    /* The Floating Icon Button */
    .cutout-icon-btn {
        position: absolute;
        top: 0;
        right: 0;
        width: 44px; /* Slightly larger than the 32px mask radius */
        height: 44px;
        background: #FFFFFF;
        border-radius: 50%; /* Make it rounded or squround */
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: -2px 2px 5px rgba(0,0,0,0.05);
        z-index: 10;
        /* Visual trick: The button actually sits BEHIND the mask if we want strict cutout, 
           but here we place it neatly in the void. */
        border: 4px solid transparent; /* spacing */
    }
    /* Icon positioning adjustment because masking makes the container lose that corner */
    .cutout-wrapper {
        position: relative;
        height: 100%;
    }
    /* We need to place the button OUTSIDE the masked container */
    .card-container-wrapper {
        position: relative;
        height: 100%;
        border-radius: 24px; /* Matches card */
        /* background: transparent; we rely on child for background */
    }
    /* REVISION: Masking cuts the content too. 
       Better approach: CSS border-radius manipulation without mask if possible? 
       No, mask is cleanest for the inverted curve. 
       We will place the button absolute to the wrapper, not the card. */
    
    .stats-value { font-size: 24px; font-weight: 700; letter-spacing: -0.5px; line-height: 1.2; margin-bottom: 4px; }
    .stats-label { font-size: 13px; font-weight: 500; opacity: 0.9; margin-bottom: 12px; }
    .stats-badge { 
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 20px; 
        font-size: 11px; font-weight: 600;
    }
    .badge-white-glass { background: rgba(255, 255, 255, 0.2); color: #FFFFFF; backdrop-filter: blur(4px); }
    .badge-green-subtle { background: #DCFCE7; color: #166534; }

</style>

<script>
    let financeChart, volumeChart;

    document.addEventListener('DOMContentLoaded', function() {
        // Init Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) })

        // Data from Controller
        const chartData = @json($chartData ?? []);
        const totalSold = chartData.kg_sold ? chartData.kg_sold.reduce((a, b) => a + b, 0) : 0;
        const totalBought = chartData.kg_bought ? chartData.kg_bought.reduce((a, b) => a + b, 0) : 0;

        // 1. Finance Chart (Spline Area)
        if (chartData.labels && chartData.labels.length > 0) {
            const financeOptions = {
                series: [{ name: 'Pemasukan', data: chartData.income }, { name: 'Pengeluaran', data: chartData.expense }],
                chart: { type: 'area', height: 320, toolbar: { show: false }, fontFamily: 'Inter, sans-serif', background: 'transparent' },
                colors: ['#4CAF50', '#ef5350'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
                xaxis: { 
                    categories: chartData.labels, 
                    axisBorder: { show: false }, 
                    axisTicks: { show: false }, 
                    labels: { 
                        style: { colors: '#64748B', fontSize: '11px', fontFamily: 'Inter, sans-serif' },
                        rotate: -45,
                        rotateAlways: false,
                        hideOverlappingLabels: true,
                        trim: true,
                        maxHeight: 60
                    },
                    tickAmount: 12,
                    tooltip: { enabled: false }
                },
                yaxis: { 
                    labels: { 
                        formatter: val => new Intl.NumberFormat('id-ID', { notation: "compact" }).format(val), 
                        style: { colors: '#64748B', fontSize: '11px', fontFamily: 'Inter, sans-serif' } 
                    } 
                },
                grid: { borderColor: '#F1F5F9', strokeDashArray: 4, padding: { top: 0, right: 0, bottom: 0, left: 10 } },
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: { 
                    theme: 'light',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif'
                    },
                    y: {
                        formatter: function(val) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                        }
                    }
                }
            };
            financeChart = new ApexCharts(document.querySelector("#financeChart"), financeOptions);
            financeChart.render();

            // 2. Volume Chart (Bar with fixes)
            const volumeOptions = {
                series: [{ name: 'Terjual', data: chartData.kg_sold }, { name: 'Dibeli', data: chartData.kg_bought }],
                chart: { 
                    type: 'bar', 
                    height: 280, 
                    stacked: true,
                    toolbar: { show: false }, 
                    fontFamily: 'Inter, sans-serif', 
                    background: 'transparent' 
                },
                colors: ['#FFA726', '#4FC3F7'],
                plotOptions: { 
                    bar: { 
                        horizontal: false, 
                        columnWidth: '50%', 
                        borderRadius: 2 
                    } 
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: { 
                    categories: chartData.labels, 
                    axisBorder: { show: false }, 
                    axisTicks: { show: false },
                    labels: { 
                        style: { colors: '#64748B', fontSize: '10px', fontFamily: 'Inter, sans-serif' },
                        rotate: -45,
                        hideOverlappingLabels: true,
                        tickAmount: 6
                    },
                    tooltip: { enabled: false }
                },
                yaxis: { 
                    labels: { 
                        style: { colors: '#64748B', fontSize: '10px', fontFamily: 'Inter, sans-serif' },
                        formatter: val => new Intl.NumberFormat('id-ID', { notation: "compact" }).format(val)
                    } 
                },
                grid: { borderColor: '#F1F5F9', strokeDashArray: 4, padding: { left: 10, right: 0 } },
                legend: { position: 'bottom', horizontalAlign: 'center', fontSize: '11px' },
                tooltip: { 
                    theme: 'light',
                    y: { formatter: val => val + " Kg" }
                }
            };
            volumeChart = new ApexCharts(document.querySelector("#volumeChart"), volumeOptions);
            volumeChart.render();
        }
    });

    async function updateChartFilter(btn, range) {
        document.querySelectorAll('.filter-btn').forEach(b => { b.classList.remove('active', 'text-success'); b.classList.add('text-secondary'); });
        btn.classList.add('active', 'text-success'); btn.classList.remove('text-secondary');
        
        try {
            const response = await fetch(`{{ route('dashboard.chart-data') }}?range=${range}`);
            const data = await response.json();
            if (financeChart) { financeChart.updateOptions({ xaxis: { categories: data.labels } }); financeChart.updateSeries([{ data: data.income }, { data: data.expense }]); }
            // For Volume, since it's cumulative donut, we might need different logic if the API returns arrays.
            // Assuming API returns arrays for the period. We sum them up.
            if (volumeChart) {
                const newSold = data.kg_sold.reduce((a, b) => a + b, 0);
                const newBought = data.kg_bought.reduce((a, b) => a + b, 0);
                 volumeChart.updateSeries([newSold, newBought]);
            }
        } catch (error) { console.error('Error fetching chart data:', error); }
    }
    
    async function fetchDashboardData() {
         location.reload(); 
    }
</script>
@endsection