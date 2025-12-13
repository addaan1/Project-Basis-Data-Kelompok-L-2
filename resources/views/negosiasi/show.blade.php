@extends('layouts.main')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark font-poppins mb-0">
                <i class="bi bi-chat-dots-fill text-warning me-2"></i>Detail Negosiasi
            </h1>
            <p class="text-muted small">Pantau dan kelola penawaran harga Anda secara real-time.</p>
        </div>
        <a href="{{ route('negosiasi.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm hover-scale">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row g-4">
        <!-- Product Details -->
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm glass-panel" style="background: white; border-radius: 16px; overflow: hidden;">
                <div class="position-relative">
                    <img src="{{ $negosiasi->produk->foto ? asset('storage/' . $negosiasi->produk->foto) : 'https://via.placeholder.com/400x300?text=Produk+Beras' }}" 
                         class="card-img-top" 
                         alt="{{ $negosiasi->produk->nama_produk }}"
                         style="height: 250px; object-fit: contain; background: #f8f9fa;">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-white text-success shadow-sm px-3 py-2 rounded-pill font-poppins">
                            Стоk: {{ $negosiasi->produk->stok }} kg
                        </span>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-success font-poppins mb-1">{{ $negosiasi->produk->nama_produk }}</h5>
                    <p class="text-muted small mb-3">{{ $negosiasi->produk->jenis_beras }}</p>
                    
                    <div class="p-3 rounded-3 bg-light mb-3 border border-light">
                        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                            <span class="text-muted small">Harga Awal</span>
                            <span class="fw-bold text-dark">Rp {{ number_format($negosiasi->harga_awal, 0, ',', '.') }} <small>/kg</small></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                            <span class="text-muted small">Jumlah Diminta</span>
                            <span class="fw-bold text-dark">{{ $negosiasi->jumlah_kg }} Kg</span>
                        </div>
                        <div class="d-flex justify-content-between pt-1">
                            <span class="text-dark fw-bold small">Total Nilai Awal</span>
                            <span class="text-secondary fw-bold">Rp {{ number_format($negosiasi->harga_awal * $negosiasi->jumlah_kg, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center p-3 rounded-3" style="background: rgba(76, 175, 80, 0.1);">
                        <div class="avatar-circle bg-success text-white me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px; border-radius: 50%;">
                            <i class="bi bi-person-fill fs-5"></i>
                        </div>
                        <div>
                            <small class="d-block text-uppercase text-success fw-bold" style="font-size: 0.65rem;">
                                {{ Auth::user()->peran == 'petani' ? 'Pembeli (Pengepul)' : 'Penjual (Petani)' }}
                            </small>
                            <span class="fw-bold text-dark">
                                {{ Auth::user()->peran == 'petani' ? ($negosiasi->pengepul->nama ?? 'Unknown') : ($negosiasi->petani->nama ?? 'Unknown') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Negotiation Conversation & Actions -->
        <div class="col-lg-8">
            <div class="card h-100 border-0 shadow-lg glass-card" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 d-flex justify-content-between align-items-center p-4 bg-white">
                    <h5 class="mb-0 fw-bold font-poppins">
                        <i class="bi bi-quote me-2 text-warning fs-4"></i>Status Penawaran
                    </h5>
                    @php
                        $statusBadge = match($negosiasi->status) {
                            'diterima' => 'success',
                            'ditolak' => 'danger',
                            default => 'warning text-dark'
                        };
                        $statusText = match($negosiasi->status) {
                            'dalam_proses' => 'Menunggu Respon',
                            default => ucfirst($negosiasi->status)
                        };
                    @endphp
                    <span class="badge bg-{{ $statusBadge }} rounded-pill px-4 py-2 text-uppercase shadow-sm">
                        {{ $statusText }}
                    </span>
                </div>
                
                <div class="card-body p-4 bg-light bg-opacity-50">
                    <!-- Current Offer Highlights -->
                    <div class="offer-card p-4 rounded-4 mb-4 text-white position-relative shadow-lg overflow-hidden" 
                         style="background: linear-gradient(135deg, #FF9800, #F57C00);">
                        
                        <div class="row align-items-center position-relative z-1">
                            <div class="col-md-7 border-end border-white border-opacity-25">
                                <h6 class="text-white-50 text-uppercase letter-spacing-1 mb-2 font-poppins" style="font-size: 0.75rem;">Harga Tawar Per Kg</h6>
                                <h2 class="display-5 fw-bold mb-0">
                                    Rp {{ number_format($negosiasi->harga_penawaran, 0, ',', '.') }}
                                </h2>
                            </div>
                            <div class="col-md-5 ps-md-4 mt-3 mt-md-0">
                                <h6 class="text-white-50 text-uppercase letter-spacing-1 mb-1 font-poppins" style="font-size: 0.75rem;">Total Deal</h6>
                                <h3 class="fw-bold text-white mb-0">
                                    Rp {{ number_format($negosiasi->harga_penawaran * $negosiasi->jumlah_kg, 0, ',', '.') }}
                                </h3>
                                <div class="mt-2 text-white-50 small">
                                    <i class="bi bi-arrow-down-circle me-1"></i>
                                    Hemat: Rp {{ number_format(($negosiasi->harga_awal - $negosiasi->harga_penawaran) * $negosiasi->jumlah_kg, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Decor -->
                        <div class="position-absolute top-0 end-0 opacity-10" style="transform: translate(20%, -20%) rotate(-15deg);">
                            <i class="bi bi-tags-fill" style="font-size: 12rem; color: white;"></i>
                        </div>
                    </div>

                    <!-- Message History / Note -->
                    <div class="mb-4">
                        <label class="form-label text-muted fw-bold small text-uppercase mb-3">Pesan dari Penawar</label>
                        <div class="chat-bubble p-4 rounded-4 bg-white shadow-sm border-start border-4 border-success position-relative">
                            <i class="bi bi-quote position-absolute top-0 start-0 ms-3 mt-2 fs-1 text-success opacity-25"></i>
                            <p class="mb-0 text-dark position-relative z-1" style="font-style: italic;">
                                "{{ $negosiasi->pesan ?: 'Saya ingin mengajukan penawaran harga untuk produk ini. Mohon dipertimbangkan.' }}"
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if($negosiasi->status === 'dalam_proses')
                        <div class="d-grid gap-3 d-md-flex justify-content-md-end mt-5 pt-4 border-top border-light">
                            @if(Auth::user()->peran === 'petani')
                                <!-- Actions for Petani (Seller) -->
                                <form action="{{ route('negosiasi.reject', $negosiasi) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-lg px-4 rounded-pill fw-bold hover-scale" onclick="return confirm('Apakah Anda yakin ingin menolak tawaran ini?')">
                                        <i class="bi bi-x-circle me-2"></i>Tolak
                                    </button>
                                </form>
                                <form action="{{ route('negosiasi.accept', $negosiasi) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg px-5 rounded-pill fw-bold shadow-lg hover-scale bg-gradient-green border-0">
                                        <i class="bi bi-check-circle-fill me-2"></i>Terima Tawaran
                                    </button>
                                </form>
                            @else
                                <!-- Actions for Pengepul (Buyer) -->
                                <button class="btn btn-secondary btn-lg px-4 rounded-pill disabled" disabled style="opacity: 0.7;">
                                    <i class="bi bi-hourglass-split me-2"></i>Menunggu Respon Petani
                                </button>
                                <div class="text-muted small align-self-center fst-italic ms-2">
                                    Penawaran terkirim, mohon tunggu petani merespon.
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="alert {{ $negosiasi->status == 'diterima' ? 'alert-success' : 'alert-danger' }} d-flex align-items-center mb-0 rounded-3 shadow-sm border-0" role="alert">
                            <i class="bi {{ $negosiasi->status == 'diterima' ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} fs-3 me-3"></i>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">Negosiasi {{ ucfirst($negosiasi->status) }}</h6>
                                <p class="mb-0 small opacity-75">
                                    Tawaran ini telah diproses pada {{ $negosiasi->updated_at->format('d M Y, H:i') }}.
                                    @if($negosiasi->status == 'diterima') Transaksi telah dibuat otomatis. @endif
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
    .font-poppins { font-family: 'Poppins', sans-serif; }
    .bg-gradient-green { background: linear-gradient(135deg, #4CAF50, #2E7D32); }
    .glass-card { backdrop-filter: blur(10px); }
    
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-2px); }
</style>
@endsection
