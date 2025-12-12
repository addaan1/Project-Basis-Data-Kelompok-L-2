@extends('layouts.main')

@section('content')
    <div class="title-box mb-4">
        <h1 class="text-4xl font-bold text-white m-0 d-flex align-items-center justify-content-center">
            <i class="fas fa-tachometer-alt me-3"></i>
            Selamat Datang di Dashboard
        </h1>
    </div>
    
    <!-- Dashboard Features -->
    <div class="row mb-4">
        <!-- Saldo Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card dashboard-card h-100 animate-fade-in">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Saldo</h5>
                        <i class="fas fa-coins fs-3 text-success"></i>
                    </div>
                    <h2 id="saldoValue" class="mb-2 text-white">Rp {{ number_format($saldo ?? 0, 0, ',', '.') }}</h2>
                    <p id="saldoUpdatedAt" class="card-text text-light">Terakhir diperbarui: {{ isset($lastUpdate) ? $lastUpdate->diffForHumans() : 'Baru saja' }}</p>
                    <a href="{{ route('saldo') }}" class="btn btn-sm btn-outline-light mt-2">Lihat Detail</a>
                </div>
            </div>
        </div>
        
        <!-- Aktivitas Transaksi Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card dashboard-card h-100 animate-fade-in" style="animation-delay: 0.1s;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Aktivitas Transaksi</h5>
                        <i class="fas fa-exchange-alt fs-3 text-primary"></i>
                    </div>
                    <div id="activityList" class="transaction-list">
                        @forelse($activities ?? [] as $index => $activity)
                            <div class="transaction-item d-flex justify-content-between mb-2" style="animation-delay: {{ $index * 0.1 }}s;">
                                <span>{{ $activity->description }}</span>
                                @php
                                    $amount = (float) ($activity->amount ?? 0);
                                    $formatted = 'Rp ' . number_format(abs($amount), 0, ',', '.');
                                    $sign = in_array($activity->type, ['sale','topup']) ? '+' : '-';
                                    $cls = $activity->type === 'sale' ? 'text-success' : ($activity->type === 'purchase' ? 'text-danger' : ($activity->type === 'topup' ? 'text-info' : 'text-warning'));
                                @endphp
                                <span class="{{ $cls }}">{{ $sign }}{{ $formatted }}</span>
                            </div>
                        @empty
                            <div class="transaction-item d-flex justify-content-between mb-2">
                                <span>Belum ada aktivitas</span>
                                <span class="text-light">-</span>
                            </div>
                        @endforelse
                    </div>
                    <a href="{{ route('transaksi.index') }}" class="btn btn-sm btn-outline-light mt-3">Lihat Semua</a>
                </div>
            </div>
        </div>
        
        <!-- Status Negosiasi Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card dashboard-card h-100 animate-fade-in" style="animation-delay: 0.2s;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Status Negosiasi</h5>
                        <i class="fas fa-comments fs-3 text-warning"></i>
                    </div>
                    <div id="negotiationList" class="negotiation-list">
                        @forelse($negotiationsSummary ?? [] as $index => $n)
                            @php
                                $badgeClass = ($n->status === 'Menunggu') ? 'bg-warning text-dark' : (($n->status === 'Disetujui') ? 'bg-success' : 'bg-secondary');
                            @endphp
                            <div class="negotiation-item d-flex justify-content-between mb-2" style="animation-delay: {{ $index * 0.1 }}s;">
                                <span>{{ $n->label }} {{ $n->jumlah_kg }} kg</span>
                                <span class="badge {{ $badgeClass }}">{{ $n->status }}</span>
                            </div>
                        @empty
                            <div class="negotiation-item d-flex justify-content-between mb-2">
                                <span>Belum ada negosiasi terkini</span>
                                <span class="badge bg-secondary">-</span>
                            </div>
                        @endforelse
                    </div>
                    <a href="{{ route('negosiasi.index') }}" class="btn btn-sm btn-outline-light mt-3">Lihat Semua</a>
                </div>
            </div>
        </div>
        
        <!-- Inventaris Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card dashboard-card h-100 animate-fade-in" style="animation-delay: 0.3s;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Inventaris Padi</h5>
                        <i class="fas fa-boxes fs-3 text-info"></i>
                    </div>
                    <h2 id="inventoryValue" class="mb-2 text-white">{{ number_format(($inventoryTon ?? 0), 2, ',', '.') }} Ton</h2>
                    <div class="progress mb-2" style="height: 10px; background-color: rgba(255, 255, 255, 0.2);">
                        <div id="inventoryProgress" class="progress-bar bg-info" role="progressbar" style="width: {{ $capacityPercent ?? 0 }}%; background: linear-gradient(90deg, #17a2b8, #20c997);" aria-valuenow="{{ $capacityPercent ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p id="inventoryCapacityText" class="card-text text-light">{{ $capacityPercent ?? 0 }}% dari kapasitas gudang</p>
                    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-light mt-2">Kelola Inventaris</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Settings Section -->
    <div class="title-box mb-4 mt-5">
        <h2 class="text-2xl font-bold text-white m-0 d-flex align-items-center justify-content-center">
            <i class="fas fa-cog me-3"></i>
            Pengaturan
        </h2>
    </div>
    <div class="row">
        <!-- Pengaturan Akun Card -->
        <div class="col-md-6 mb-4">
            <div class="card dashboard-card h-100 animate-fade-in" style="animation-delay: 0.4s;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Pengaturan Akun</h5>
                        <i class="fas fa-user-cog fs-3 text-secondary"></i>
                    </div>
                    <p class="card-text">Kelola informasi akun Anda seperti nama, password, nomor telepon, dan informasi lainnya.</p>
                    <a href="{{ route('settings.index') }}" class="btn btn-outline-light mt-3">Kelola Akun</a>
                </div>
            </div>
        </div>
        
        <!-- Pengaturan E-Wallet Card -->
        <div class="col-md-6 mb-4">
            <div class="card dashboard-card h-100 animate-fade-in" style="animation-delay: 0.5s;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Pengaturan E-Wallet</h5>
                        <i class="fas fa-wallet fs-3 text-danger"></i>
                    </div>
                    <p class="card-text">Kelola metode pembayaran, rekening bank, dan pengaturan e-wallet lainnya.</p>
                    <a href="{{ route('ewallet') }}" class="btn btn-outline-light mt-3">Kelola E-Wallet</a>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .dashboard-card {
            background: linear-gradient(135deg, #4CAF50, #81C784);
            backdrop-filter: blur(10px);
            border: 1px solid #FF9800;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #FF9800, #4CAF50);
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }
        
        .title-box {
            background: linear-gradient(135deg, #FF9800, #FFB74D);
            border: 1px solid #2E7D32;
            border-radius: 16px;
            padding: 12px 24px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: inline-block;
            width: auto;
            margin: 0 auto;
        }
        
        .title-box:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }
        
        .card-title {
            font-weight: 600;
            color: #fff;
        }
        
        .card-text, .transaction-item, .negotiation-item {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .transaction-list, .negotiation-list {
            max-height: 150px;
            overflow-y: auto;
            padding-right: 5px;
        }
        
        .transaction-list::-webkit-scrollbar,
        .negotiation-list::-webkit-scrollbar {
            width: 4px;
        }
        
        .transaction-list::-webkit-scrollbar-track,
        .negotiation-list::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
        }
        
        .transaction-list::-webkit-scrollbar-thumb,
        .negotiation-list::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }
        
        .btn-outline-light {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }
        
        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.8);
            transform: translateY(-1px);
        }
        
        /* Animasi Fade-in */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        /* Responsiveness untuk mobile */
        @media (max-width: 768px) {
            .title-box {
                padding: 10px 20px;
                width: 100%;
                display: block;
            }
            
            .text-4xl {
                font-size: 2rem !important;
            }
            
            .text-2xl {
                font-size: 1.5rem !important;
            }
            
            .dashboard-card {
                margin-bottom: 1rem;
            }
        }
    </style>
    <script>
        async function fetchDashboardData() {
            try {
                const res = await fetch('{{ route('dashboard.data') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) return;
                const data = await res.json();
                const saldoEl = document.getElementById('saldoValue');
                const saldoUpdatedEl = document.getElementById('saldoUpdatedAt');
                const actList = document.getElementById('activityList');
                const negoList = document.getElementById('negotiationList');
                const invVal = document.getElementById('inventoryValue');
                const invProg = document.getElementById('inventoryProgress');
                const invText = document.getElementById('inventoryCapacityText');

                if (saldoEl) saldoEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(data.saldo || 0));
                if (saldoUpdatedEl) saldoUpdatedEl.textContent = 'Terakhir diperbarui: ' + new Date(data.lastUpdate).toLocaleString('id-ID');

                if (actList) {
                    if (Array.isArray(data.activities) && data.activities.length) {
                        actList.innerHTML = data.activities.map((a) => {
                            const amount = Math.abs(Number(a.amount || 0));
                            const sign = (a.type === 'sale' || a.type === 'topup') ? '+' : '-';
                            const cls = a.type === 'sale' ? 'text-success' : (a.type === 'purchase' ? 'text-danger' : (a.type === 'topup' ? 'text-info' : 'text-warning'));
                            return `<div class="transaction-item d-flex justify-content-between mb-2"><span>${a.description}</span><span class="${cls}">${sign}Rp ${new Intl.NumberFormat('id-ID').format(amount)}</span></div>`;
                        }).join('');
                    } else {
                        actList.innerHTML = `<div class="transaction-item d-flex justify-content-between mb-2"><span>Belum ada aktivitas</span><span class="text-light">-</span></div>`;
                    }
                }

                if (negoList) {
                    if (Array.isArray(data.negotiations) && data.negotiations.length) {
                        negoList.innerHTML = data.negotiations.map((n) => {
                            const badgeClass = (n.status === 'Menunggu') ? 'bg-warning text-dark' : ((n.status === 'Disetujui') ? 'bg-success' : 'bg-secondary');
                            return `<div class="negotiation-item d-flex justify-content-between mb-2"><span>${n.label} ${n.jumlah_kg} kg</span><span class="badge ${badgeClass}">${n.status}</span></div>`;
                        }).join('');
                    } else {
                        negoList.innerHTML = `<div class="negotiation-item d-flex justify-content-between mb-2"><span>Belum ada negosiasi terkini</span><span class="badge bg-secondary">-</span></div>`;
                    }
                }

                const ton = (Number(data.inventoryKg || 0) / 1000).toFixed(2);
                if (invVal) invVal.textContent = `${ton.replace('.', ',')} Ton`;
                if (invProg) {
                    invProg.style.width = `${Number(data.capacityPercent || 0)}%`;
                    invProg.setAttribute('aria-valuenow', String(Number(data.capacityPercent || 0)));
                }
                if (invText) invText.textContent = `${Number(data.capacityPercent || 0)}% dari kapasitas gudang`;
            } catch (e) {
                // silently ignore
            }
        }
        setInterval(fetchDashboardData, 15000);
    </script>
@endsection