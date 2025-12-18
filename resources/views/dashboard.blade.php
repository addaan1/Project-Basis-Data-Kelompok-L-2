@extends('layouts.main')

@section('content')
<div class="dashboard-wrapper min-vh-100 p-4">
    
    <!-- 1. TOP WELCOME BANNER (Orange) -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(90deg, #FF9800 0%, #F57C00 100%); border-radius: 16px;">
        <div class="card-body p-4 text-white d-flex align-items-center justify-content-between relative">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3">
                    <i class="bi bi-shop-window fs-2 text-white"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">{{ Auth::user()->nama ?? 'Mitra WarungPadi' }} ({{ ucfirst(Auth::user()->peran ?? 'User') }})</h4>
                    <div class="d-flex gap-2">
                        <span class="badge bg-white text-warning"><i class="bi bi-bar-chart-fill me-1"></i> Dashboard</span>
                        <span class="badge bg-white bg-opacity-25">{{ ucfirst(Auth::user()->peran ?? 'User') }}</span>
                    </div>
                     <p class="mb-0 mt-2 opacity-75 small"><i class="bi bi-info-circle me-1"></i> Pantau arus kas, stok masuk, dan negosiasi pasar</p>
                </div>
            </div>
             <div class="text-end d-none d-md-block">
                <small class="opacity-75">Last Update</small>
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::now()->format('H:i') }}
                    <span class="mx-2">|</span>
                    {{ \Carbon\Carbon::now()->format('d M') }}
                </h5>
            </div>
            <!-- Decorative circle -->
            <div style="position: absolute; right: -20px; bottom: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
        </div>
    </div>

    <!-- 2. STATS GRID (4 Green Cards) -->
    <div class="row g-4 mb-4">
        <!-- Net Cashflow -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 text-white" style="background-color: #4CAF50; border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold text-uppercase opacity-75">Net Cashflow</span>
                        <i class="bi bi-wallet2 fs-4 opacity-50"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Rp {{ number_format($saldo ?? 0, 0, ',', '.') }}</h4>
                    <small class="opacity-75" style="font-size: 0.75rem;">
                        Inc: <span class="text-white">{{ number_format(($chartData['income'][11] ?? 0)/1000000, 1) }}M</span> | 
                        Out: <span class="text-danger bg-white px-1 rounded">{{ number_format(($chartData['expense'][11] ?? 0)/1000000, 1) }}M</span>
                    </small>
                </div>
            </div>
        </div>

        <!-- Volume Pembelian -->
        <div class="col-xl-3 col-md-6">
             <div class="card border-0 shadow-sm h-100 text-white" style="background-color: #4CAF50; border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                         <span class="small fw-bold text-uppercase opacity-75">Volume Pembelian (Harian)</span>
                         <i class="bi bi-download fs-4 opacity-50"></i>
                    </div>
                    <h4 class="fw-bold mb-1">
                        {{ number_format($activities->where('type', 'purchase')->where('date', '>=', \Carbon\Carbon::today())->sum('amount')/1000 ?? 0, 0, ',', '.') }} Kg
                    </h4>
                     <small class="opacity-75" style="font-size: 0.75rem;"><i class="bi bi-arrow-down-circle me-1"></i> Stok Masuk</small>
                </div>
            </div>
        </div>

        <!-- Kapasitas Gudang -->
        <div class="col-xl-3 col-md-6">
             <div class="card border-0 shadow-sm h-100 text-white" style="background-color: #4CAF50; border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                         <span class="small fw-bold text-uppercase opacity-75">Kapasitas Gudang</span>
                         <i class="bi bi-building fs-4 opacity-50"></i>
                    </div>
                    <h4 class="fw-bold mb-1">
                         {{ number_format($inventoryTon ?? 0, 0, ',', '.') }} <span class="fs-6 fw-normal text-white-50">/ 10.000 Kg</span>
                    </h4>
                    <div class="progress bg-white bg-opacity-25 mt-2" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ $capacityPercent ?? 0 }}%"></div>
                    </div>
                     <small class="opacity-75 mt-1 d-block" style="font-size: 0.7rem;">Terisi {{ $capacityPercent ?? 0 }}%</small>
                </div>
            </div>
        </div>

        <!-- Tawaran Saya -->
        <div class="col-xl-3 col-md-6">
             <div class="card border-0 shadow-sm h-100 text-white" style="background-color: #4CAF50; border-radius: 12px;">
                <div class="card-body p-3">
                     <div class="d-flex justify-content-between mb-2">
                         <span class="small fw-bold text-uppercase opacity-75">Tawaran Saya</span>
                         <i class="bi bi-chat-quote fs-4 opacity-50"></i>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $negotiationsSummary->where('status', 'Menunggu')->count() }} <span class="fs-6 fw-normal">Pending</span></h4>
                     <small class="opacity-75" style="font-size: 0.75rem;"><i class="bi bi-clock-history me-1"></i> Menunggu Respon Petani</small>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. CHARTS ROW (Orange Header / Green Body) -->
    <div class="row g-4">
        <!-- Tren Arus Kas -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-warning text-white py-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Tren Arus Kas</h6>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-white text-warning py-0 px-2 fw-bold" style="font-size: 0.7rem;">30 Hari</button>
                        <button class="btn btn-sm btn-outline-light py-0 px-2 opacity-50" style="font-size: 0.7rem;">24 Jam</button>
                        <button class="btn btn-sm btn-outline-light py-0 px-2 opacity-50" style="font-size: 0.7rem;">4 Minggu</button>
                        <button class="btn btn-sm btn-dark py-0 px-2" style="font-size: 0.7rem;">12 Bulan</button>
                    </div>
                </div>
                <div class="card-body bg-success text-white">
                     <!-- Legend hack manually placed if needed, or use Chart legend -->
                     <div class="d-flex justify-content-end mb-2">
                         <span class="badge bg-transparent border border-white me-2"><i class="bi bi-circle-fill text-white"></i> Pemasukan</span>
                         <span class="badge bg-transparent border border-danger text-danger bg-white"><i class="bi bi-circle-fill text-danger"></i> Pengeluaran</span>
                     </div>
                    <div id="financeChart" style="min-height: 280px;"></div>
                </div>
            </div>
        </div>

        <!-- Volume Transaksi -->
        <div class="col-xl-4 col-lg-5">
             <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-warning text-white py-2">
                    <h6 class="fw-bold mb-0">Volume Transaksi</h6>
                </div>
                <div class="card-body bg-success text-white">
                      <div class="d-flex justify-content-end mb-2">
                         <small class="me-2"><i class="bi bi-square-fill text-info"></i> Stok Masuk</small>
                         <small><i class="bi bi-square-fill text-warning"></i> Stok Keluar</small>
                     </div>
                    <div id="volumeChart" style="min-height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-wrapper { background: #f8f9fa; }
    /* ApexCharts White Theme Override */
    .apexcharts-text { fill: #ffffff !important; }
    .apexcharts-gridline { stroke: rgba(255,255,255,0.2) !important; stroke-dasharray: 4; }
    .apexcharts-legend-text { color: #ffffff !important; }
</style>

<script>
    let financeChart, volumeChart;

    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData ?? []);
        
        if (chartData.labels && chartData.labels.length > 0) {
            // 1. Tren Arus Kas (Line/Area with dashed grid)
            const financeOptions = {
                series: [{ name: 'Pemasukan', data: chartData.income }, { name: 'Pengeluaran', data: chartData.expense }],
                chart: { type: 'line', height: 300, toolbar: { show: false }, background: 'transparent' },
                colors: ['#ffffff', '#ef5350'], 
                stroke: { curve: 'smooth', width: 2, dashArray: [0, 0] },
                markers: { size: 4, hover: { size: 6 } },
                xaxis: { 
                    categories: chartData.labels, 
                    labels: { style: { colors: '#ffffff', fontSize: '10px' } },
                    axisBorder: { show: false }, axisTicks: { show: false }
                },
                yaxis: { 
                    labels: { 
                        formatter: val => (val/1000000).toFixed(1) + "jt", 
                        style: { colors: '#ffffff', fontSize: '10px' } 
                    } 
                },
                grid: { borderColor: 'rgba(255,255,255,0.2)', strokeDashArray: 3 },
                legend: { show: false }, // Custom legend used
                tooltip: { theme: 'light', y: { formatter: val => "Rp " + new Intl.NumberFormat('id-ID').format(val) } }
            };
            financeChart = new ApexCharts(document.querySelector("#financeChart"), financeOptions);
            financeChart.render();

            // 2. Volume Transaksi (Bar)
            const volumeOptions = {
                series: [{ name: 'Stok Masuk (Beli)', data: chartData.kg_bought }, { name: 'Stok Keluar (Jual)', data: chartData.kg_sold }],
                chart: { type: 'bar', height: 280, stacked: false, toolbar: { show: false }, background: 'transparent' },
                colors: ['#29B6F6', '#FFA726'], // Blue and Orange bars
                plotOptions: { bar: { borderRadius: 2, columnWidth: '60%' } },
                dataLabels: { enabled: false },
                xaxis: { 
                    categories: chartData.labels, 
                    labels: { style: { colors: '#ffffff', fontSize: '10px' }, rotate: -45 },
                    axisBorder: { show: false }
                },
                yaxis: { 
                    labels: { style: { colors: '#ffffff', fontSize: '10px' } }
                },
                grid: { borderColor: 'rgba(255,255,255,0.2)', strokeDashArray: 3 },
                legend: { show: false },
                tooltip: { theme: 'light' }
            };
            volumeChart = new ApexCharts(document.querySelector("#volumeChart"), volumeOptions);
            volumeChart.render();
        }
    });
</script>
@endsection