@extends('layouts.main')

@section('content')
<div class="container-fluid p-4" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); min-height: 100vh;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0" style="color: #1b5e20 !important;">
                <i class="bi bi-chat-dots-fill text-warning me-2"></i>Detail Negosiasi
            </h1>
            <p class="small" style="color: #2e7d32 !important;">Pantau dan kelola penawaran harga Anda secara real-time.</p>
        </div>
        <a href="{{ route('negosiasi.index') }}" class="btn btn-outline-success rounded-pill px-4 shadow-sm hover-scale">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Alert Messages (Added for feedback) -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- End Alert Messages -->

    <div class="row g-4">
        <!-- Product Details -->
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-lg rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #ffffff 0%, #f1f8e9 100%);">
                <div class="position-relative">
                    <img src="{{ $negosiasi->produk->foto ? asset('storage/' . $negosiasi->produk->foto) : 'https://via.placeholder.com/400x300?text=Produk+Beras' }}" 
                         class="card-img-top" 
                         alt="{{ $negosiasi->produk->nama_produk }}"
                         style="height: 250px; object-fit: contain; background: #ffffff;">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge shadow-sm px-3 py-2 rounded-pill fw-bold" style="background: #4CAF50; color: #ffffff !important;">
                            <i class="bi bi-box-seam me-1"></i>Stok: {{ $negosiasi->produk->stok }} kg
                        </span>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-1" style="color: #1b5e20 !important;">{{ $negosiasi->produk->nama_produk }}</h5>
                    <p class="small mb-3" style="color: #2e7d32 !important;"><i class="bi bi-tag me-1"></i>{{ $negosiasi->produk->jenis_beras }}</p>
                    
                    <!-- Price Comparison Card - Light Green Theme -->
                    <div class="card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #a5d6a7 0%, #81c784 100%);">
                        <div class="card-body p-3">
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-50">
                                <div class="mb-1">
                                    <span class="small fw-bold" style="color: #1b5e20 !important;">
                                        <i class="bi bi-cash-stack me-1"></i>Harga Awal
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-bold" style="color: #ffffff !important; font-size: 1.2rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
                                        Rp {{ number_format($negosiasi->harga_awal, 0, ',', '.') }} 
                                    </span>
                                    <small style="color: #ffffff !important; opacity: 0.9;">/kg</small>
                                </div>
                            </div>
                            
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-50">
                                <div class="mb-1">
                                    <span class="small fw-bold" style="color: #1b5e20 !important;">
                                        <i class="bi bi-box me-1"></i>Jumlah Diminta
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-bold" style="color: #ffffff !important; font-size: 1.2rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
                                        {{ $negosiasi->jumlah_kg }}
                                    </span>
                                    <small style="color: #ffffff !important; opacity: 0.9;">Kg</small>
                                </div>
                            </div>
                            
                            <div class="pt-2">
                                <div class="mb-2">
                                    <span class="fw-bold" style="color: #1b5e20 !important;">
                                        <i class="bi bi-calculator me-1"></i>Total Nilai Awal
                                    </span>
                                </div>
                                <div class="p-2 rounded" style="background: rgba(255, 255, 255, 0.3);">
                                    <span class="fw-bold" style="color: #1b5e20 !important; font-size: 1.3rem;">
                                        Rp {{ number_format($negosiasi->harga_awal * $negosiasi->jumlah_kg, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Partner Info Card - Green Theme -->
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #66bb6a 0%, #4caf50 100%);">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow" 
                                     style="width: 48px; height: 48px; border-radius: 50%; background: #ffffff;">
                                    <i class="bi bi-person-fill fs-4" style="color: #4CAF50;"></i>
                                </div>
                                <div>
                                    <small class="d-block text-uppercase fw-bold mb-1" style="color: rgba(255, 255, 255, 0.8) !important; font-size: 0.65rem; letter-spacing: 0.5px;">
                                        {{ Auth::user()->peran == 'petani' ? 'Pembeli (Pengepul)' : 'Penjual (Petani)' }}
                                    </small>
                                    <span class="fw-bold d-block" style="color: #ffffff !important; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
                                        {{ Auth::user()->peran == 'petani' ? ($negosiasi->pengepul->nama ?? 'Unknown') : ($negosiasi->petani->nama ?? 'Unknown') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Negotiation Conversation & Actions -->
        <div class="col-lg-8">
            <div class="card h-100 border-0 shadow-lg rounded-4 overflow-hidden" style="background: #ffffff;">
                <div class="card-header border-0 d-flex justify-content-between align-items-center p-4" style="background: linear-gradient(135deg, #66bb6a 0%, #4caf50 100%);">
                    <h5 class="mb-0 fw-bold" style="color: #ffffff !important;">
                        <i class="bi bi-megaphone me-2 fs-4"></i>Status Penawaran
                    </h5>
                    @php
                        $statusBadge = match($negosiasi->status) {
                            'diterima' => 'success',
                            'ditolak' => 'danger',
                            default => 'warning text-dark'
                        };
                        $statusText = match($negosiasi->status) {
                            'dalam_proses', 'menunggu' => 'Menunggu Respon',
                            default => ucfirst($negosiasi->status)
                        };
                    @endphp
                    <span class="badge bg-{{ $statusBadge }} rounded-pill px-4 py-2 text-uppercase shadow fw-bold" style="font-size: 0.85rem;">
                        <i class="bi {{ $negosiasi->status == 'diterima' ? 'bi-check-circle-fill' : ($negosiasi->status == 'ditolak' ? 'bi-x-circle-fill' : 'bi-hourglass-split') }} me-1"></i>
                        {{ $statusText }}
                    </span>
                </div>
                
                <div class="card-body p-4" style="background: linear-gradient(to bottom, #f1f8e9 0%, #e8f5e9 100%);">
                    <!-- Current Offer Highlights -->
                    <div class="offer-card p-4 rounded-4 mb-4 position-relative shadow-lg overflow-hidden" 
                         style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);">
                        
                        <div class="row align-items-center position-relative" style="z-index: 2;">
                            <div class="col-md-7 border-end border-white border-opacity-50">
                                <h6 class="text-uppercase letter-spacing-1 mb-2 fw-bold" style="color: #ffffff !important; font-size: 0.75rem;">
                                    <i class="bi bi-tag-fill me-1"></i>Harga Tawar Per Kg
                                </h6>
                                <h2 class="display-5 fw-bold mb-0" style="color: #ffffff !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                                    Rp {{ number_format($negosiasi->harga_penawaran, 0, ',', '.') }}
                                </h2>
                            </div>
                            <div class="col-md-5 ps-md-4 mt-3 mt-md-0">
                                <h6 class="text-uppercase letter-spacing-1 mb-2 fw-bold" style="color: #ffffff !important; font-size: 0.75rem;">
                                    <i class="bi bi-wallet2 me-1"></i>Total Deal
                                </h6>
                                <h3 class="fw-bold mb-3" style="color: #ffffff !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                                    Rp {{ number_format($negosiasi->harga_penawaran * $negosiasi->jumlah_kg, 0, ',', '.') }}
                                </h3>
                                <div class="d-flex align-items-center rounded-pill px-3 py-2" style="background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px);">
                                    <i class="bi bi-arrow-down-circle-fill me-2 fs-5" style="color: #ffffff !important;"></i>
                                    <div>
                                        <small class="d-block fw-bold" style="color: #ffffff !important; font-size: 0.7rem;">Hemat dari Harga Awal</small>
                                        <strong style="color: #ffffff !important; font-size: 0.95rem;">Rp {{ number_format(($negosiasi->harga_awal - $negosiasi->harga_penawaran) * $negosiasi->jumlah_kg, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="position-absolute" style="top: 50%; right: -5%; transform: translate(0, -50%) rotate(-15deg); z-index: 1; opacity: 0.08;">
                            <i class="bi bi-tags-fill" style="font-size: 18rem; color: white;"></i>
                        </div>
                    </div>

                    <!-- Message History - Green Theme -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase mb-3" style="color: #1b5e20 !important;">
                            <i class="bi bi-chat-left-quote-fill me-2" style="color: #4CAF50;"></i>Pesan dari Penawar
                        </label>
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-4" style="background: linear-gradient(135deg, #ffffff 0%, #e8f5e9 100%); border-left: 5px solid #4CAF50;">
                                <div class="position-relative">
                                    <i class="bi bi-quote position-absolute top-0 start-0 fs-1" style="color: #4CAF50; opacity: 0.15;"></i>
                                    <p class="mb-0 position-relative ps-4" style="color: #1b5e20 !important; font-size: 1.1rem; line-height: 1.7; font-style: italic; font-weight: 600;">
                                        "{{ $negosiasi->pesan ?: 'Saya ingin mengajukan penawaran harga untuk produk ini. Mohon dipertimbangkan.' }}"
                                    </p>
                                </div>
                                <div class="mt-3 pt-3 border-top border-success border-opacity-25">
                                    <small style="color: #2e7d32 !important; font-weight: 500;">
                                        <i class="bi bi-clock-fill me-1"></i>Dikirim pada {{ $negosiasi->created_at->format('d M Y, H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @php
                        $normalizedStatus = trim(strtolower($negosiasi->status));
                    @endphp
                    
                    @if(in_array($normalizedStatus, ['dalam_proses', 'menunggu']))
                        <div class="d-grid gap-3 d-md-flex justify-content-md-end mt-5 pt-4 border-top border-success border-opacity-25">
                            @if(Auth::user()->peran === 'petani')
                                <form action="{{ route('negosiasi.reject', $negosiasi) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-lg px-5 rounded-pill fw-bold hover-scale shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menolak tawaran ini?')">
                                        <i class="bi bi-x-circle me-2"></i>Tolak Tawaran
                                    </button>
                                </form>
                                <form action="{{ route('negosiasi.accept', $negosiasi) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-lg px-5 rounded-pill fw-bold shadow-lg hover-scale border-0" style="background: linear-gradient(135deg, #4CAF50, #2E7D32); color: #ffffff;">
                                        <i class="bi bi-check-circle-fill me-2"></i>Terima & Lanjutkan
                                    </button>
                                </form>
                            @else
                                <div class="alert border-0 shadow-sm rounded-4 d-flex align-items-center px-4 mb-0" role="alert" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                                    <i class="bi bi-hourglass-split fs-3 me-3" style="color: #1976d2;"></i>
                                    <div>
                                        <strong class="d-block" style="color: #0d47a1;">Menunggu Respon Petani</strong>
                                        <small style="color: #1565c0;">Penawaran terkirim, mohon tunggu petani merespon.</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="alert {{ $normalizedStatus == 'diterima' ? 'alert-success' : 'alert-danger' }} d-flex align-items-center mb-0 rounded-4 shadow-sm border-0 p-4" role="alert">
                            <i class="bi {{ $normalizedStatus == 'diterima' ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} fs-2 me-3"></i>
                            <div>
                                <h5 class="alert-heading fw-bold mb-2">Negosiasi {{ ucfirst($normalizedStatus) }}</h5>
                                <p class="mb-0">
                                    Tawaran ini telah diproses pada {{ $negosiasi->updated_at->format('d M Y, H:i') }}.
                                    @if($normalizedStatus == 'diterima') 
                                        <strong>Transaksi berhasil dibuat, Pembayaran Lunas (Auto-Deduct), dan Saldo diteruskan ke Petani.</strong>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale { 
        transition: transform 0.3s ease, box-shadow 0.3s ease; 
    }
    
    .hover-scale:hover { 
        transform: translateY(-3px); 
        box-shadow: 0 10px 25px rgba(46, 125, 50, 0.3) !important; 
    }
    
    .letter-spacing-1 {
        letter-spacing: 1px;
    }
</style>
@endsection
