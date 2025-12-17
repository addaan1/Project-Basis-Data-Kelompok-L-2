@extends('layouts.main')

@section('content')
<div class="container-fluid p-4 negosiasi-container">
    <!-- Enhanced Header with Glassmorphism -->
    <div class="negosiasi-header mb-4 animate-fade-in">
        <div class="header-content-wrapper">
            <div class="header-text">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-wrapper me-3">
                        <i class="bi bi-chat-dots-fill"></i>
                    </div>
                    <div>
                        <h1 class="header-title mb-0">Daftar Negosiasi</h1>
                        <p class="header-subtitle mb-0">Kelola penawaran harga Anda dengan mudah</p>
                    </div>
                </div>
            </div>
            @if(Auth::user()->peran === 'pengepul')
                <a href="{{ route('market.index') }}" class="btn-create-offer">
                    <i class="bi bi-plus-circle me-2"></i>Buat Penawaran Baru
                </a>
            @endif
        </div>
    </div>

    @if($negotiations->isEmpty())
        <!-- Enhanced Empty State -->
        <div class="empty-state-card animate-fade-in-delay">
            <div class="empty-state-content">
                <div class="empty-icon-wrapper mb-4">
                    <i class="bi bi-inbox empty-icon"></i>
                </div>
                <h5 class="empty-title">Belum Ada Negosiasi</h5>
                <p class="empty-text">
                    @if(Auth::user()->peran === 'pengepul')
                        Mulai tawar-menawar harga dengan petani untuk mendapatkan deal terbaik.
                    @else
                        Belum ada penawaran masuk dari pengepul saat ini.
                    @endif
                </p>
                @if(Auth::user()->peran === 'pengepul')
                    <a href="{{ route('market.index') }}" class="btn-explore">
                        <i class="bi bi-search me-2"></i>Jelajahi Pasar
                    </a>
                @endif
            </div>
        </div>
    @else
        <!-- Enhanced Negotiation Cards Grid -->
        <div class="row g-4">
            @foreach($negotiations as $nego)
                <div class="col-md-6 col-lg-4 animate-card-entry" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                    <div class="nego-card">
                        <!-- Product Image with Overlay -->
                        <div class="card-image-wrapper">
                            <img src="{{ $nego->produk->foto ? asset('storage/' . $nego->produk->foto) : 'https://via.placeholder.com/400x200?text=Produk+Beras' }}" 
                                 class="card-image" 
                                 alt="{{ $nego->produk->nama_produk }}">
                            <div class="image-overlay"></div>
                            
                            <!-- Enhanced Status Badge -->
                            <div class="status-badge-wrapper">
                                @php
                                    $statusConfig = match($nego->status) {
                                        'diterima' => ['bg' => '#4CAF50', 'text' => 'Diterima', 'icon' => 'check-circle-fill', 'animate' => 'pulse-success'],
                                        'ditolak' => ['bg' => '#f44336', 'text' => 'Ditolak', 'icon' => 'x-circle-fill', 'animate' => ''],
                                        default => ['bg' => '#FF9800', 'text' => 'Menunggu', 'icon' => 'hourglass-split', 'animate' => 'pulse-warning']
                                    };
                                @endphp
                                <span class="status-badge {{ $statusConfig['animate'] }}" style="background: {{ $statusConfig['bg'] }};">
                                    <i class="bi bi-{{ $statusConfig['icon'] }} me-1"></i>{{ $statusConfig['text'] }}
                                </span>
                            </div>
                        </div>

                        <div class="nego-card-body">
                            <!-- Product Info -->
                            <div class="product-header mb-3">
                                <h6 class="product-name">{{ $nego->produk->nama_produk }}</h6>
                                <p class="product-type">
                                    <i class="bi bi-tag me-1"></i>{{ $nego->produk->jenis_beras }}
                                </p>
                            </div>

                            <!-- Enhanced Price Display -->
                            <div class="price-card mb-3">
                                <div class="price-row">
                                    <span class="price-label">Harga Awal</span>
                                    <span class="price-value price-initial">Rp {{ number_format($nego->harga_awal, 0, ',', '.') }}<span class="price-unit">/kg</span></span>
                                </div>
                                <div class="price-divider"></div>
                                <div class="price-row">
                                    <span class="price-label">Harga Tawar</span>
                                    <span class="price-value price-offer">Rp {{ number_format($nego->harga_penawaran, 0, ',', '.') }}<span class="price-unit">/kg</span></span>
                                </div>
                            </div>

                            <!-- Quantity & Total Info -->
                            <div class="quantity-total-wrapper mb-3">
                                <div class="quantity-info">
                                    <i class="bi bi-box"></i>
                                    <span>{{ number_format($nego->jumlah_kg) }} Kg</span>
                                </div>
                                <div class="total-info">
                                    <span class="total-label">Total:</span>
                                    <span class="total-value">Rp {{ number_format($nego->harga_penawaran * $nego->jumlah_kg, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Enhanced Partner Info -->
                            <div class="partner-info mb-3">
                                <div class="partner-avatar">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="partner-details">
                                    <span class="partner-role">{{ Auth::user()->peran == 'petani' ? 'Pengepul' : 'Petani' }}</span>
                                    <span class="partner-name">{{ Auth::user()->peran == 'petani' ? ($nego->pengepul->nama ?? 'Unknown') : ($nego->petani->nama ?? 'Unknown') }}</span>
                                </div>
                            </div>

                            <!-- Enhanced Action Button -->
                            <a href="{{ route('negosiasi.show', $nego) }}" class="btn-view-detail">
                                <span class="btn-text">
                                    <i class="bi bi-eye me-2"></i>Lihat Detail
                                </span>
                                <span class="btn-bg"></span>
                            </a>

                            <!-- Timestamp -->
                            <div class="card-timestamp">
                                <i class="bi bi-clock me-1"></i>{{ $nego->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Enhanced Pagination -->
        <div class="pagination-wrapper mt-5">
            {{ $negotiations->links() }}
        </div>
    @endif
</div>

<style>
    /* ===========================
       CONTAINER & LAYOUT
    =========================== */
    .negosiasi-container {
        min-height: 100vh;
        background: transparent;
        position: relative;
    }

    /* ===========================
       ENHANCED HEADER SECTION
    =========================== */
    .negosiasi-header {
        background: linear-gradient(135deg, #4CAF50, #81C784);
        border: 2px solid #FF9800;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        position: relative;
        overflow: hidden;
    }

    .negosiasi-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #FF9800, #FFB74D, #4CAF50);
    }

    .header-content-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .icon-wrapper {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #FF9800, #FFB74D);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
    }

    .icon-wrapper i {
        font-size: 1.8rem;
        color: white;
    }

    .header-title {
        font-size: 2rem;
        font-weight: 800;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        letter-spacing: -0.5px;
    }

    .header-subtitle {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.95);
        font-weight: 500;
    }

    .btn-create-offer {
        background: linear-gradient(135deg, #FF9800, #FFB74D);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid white;
        display: inline-flex;
        align-items: center;
    }

    .btn-create-offer:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255, 152, 0, 0.4);
        color: white;
    }

    /* ===========================
       EMPTY STATE
    =========================== */
    .empty-state-card {
        background: linear-gradient(135deg, #4CAF50, #81C784);
        border: 2px solid #FF9800;
        border-radius: 20px;
        padding: 4rem 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        text-align: center;
    }

    .empty-state-content {
        max-width: 500px;
        margin: 0 auto;
    }

    .empty-icon-wrapper {
        position: relative;
        display: inline-block;
    }

    .empty-icon {
        font-size: 5rem;
        color: rgba(255, 255, 255, 0.6);
        animation: float 3s ease-in-out infinite;
    }

    .empty-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        margin-bottom: 1rem;
    }

    .empty-text {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
        margin-bottom: 2rem;
    }

    .btn-explore {
        background: linear-gradient(135deg, #FF9800, #FFB74D);
        color: white;
        padding: 0.875rem 2.5rem;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
        transition: all 0.3s ease;
        display: inline-block;
        border: 2px solid white;
    }

    .btn-explore:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255, 152, 0, 0.4);
        color: white;
    }

    /* ===========================
       NEGOTIATION CARDS
    =========================== */
    .nego-card {
        background: linear-gradient(135deg, #4CAF50, #81C784);
        border: 2px solid #FF9800;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .nego-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #FF9800, #FFB74D, #4CAF50);
        z-index: 2;
    }

    .nego-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 15px 40px rgba(76, 175, 80, 0.25), 0 0 0 3px rgba(255, 152, 0, 0.3);
    }

    /* Card Image */
    .card-image-wrapper {
        position: relative;
        height: 200px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.95);
    }

    .card-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.4s ease;
    }

    .nego-card:hover .card-image {
        transform: scale(1.1);
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(76, 175, 80, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .nego-card:hover .image-overlay {
        opacity: 1;
    }

    /* Status Badge */
    .status-badge-wrapper {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 3;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.625rem 1.25rem;
        border-radius: 50px;
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        border: 2px solid white;
        backdrop-filter: blur(10px);
    }

    /* Card Body */
    .nego-card-body {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* Product Info */
    .product-header {
        border-bottom: 2px solid rgba(255, 255, 255, 0.3);
        padding-bottom: 0.75rem;
    }

    .product-name {
        font-size: 1.25rem;
        font-weight: 800;
        color: white;
        margin-bottom: 0.25rem;
        line-height: 1.3;
    }

    .product-type {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
        font-weight: 500;
    }

    .product-type i {
        color: #FFB74D;
    }

    /* Enhanced Price Card */
    .price-card {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.25rem 0;
    }

    .price-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.95);
    }

    .price-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: white;
    }

    .price-unit {
        font-size: 0.75rem;
        font-weight: 600;
        opacity: 0.9;
    }

    .price-offer {
        color: #FFD54F;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .price-divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.3);
        margin: 0.5rem 0;
    }

    /* Quantity & Total */
    .quantity-total-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .quantity-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .quantity-info i {
        color: #FFB74D;
        font-size: 1.1rem;
    }

    .total-info {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .total-label {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 600;
    }

    .total-value {
        font-size: 1rem;
        font-weight: 800;
        color: white;
    }

    /* Partner Info */
    .partner-info {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.875rem;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .partner-avatar {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #FF9800, #FFB74D);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(255, 152, 0, 0.3);
        border: 2px solid white;
    }

    .partner-avatar i {
        color: white;
        font-size: 1.1rem;
    }

    .partner-details {
        display: flex;
        flex-direction: column;
        min-width: 0;
        flex: 1;
    }

    .partner-role {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.85);
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .partner-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: white;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Enhanced Action Button */
    .btn-view-detail {
        background: linear-gradient(135deg, #FF9800, #FFB74D);
        color: white;
        padding: 0.875rem 1.5rem;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        text-align: center;
        box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        margin-top: auto;
    }

    .btn-view-detail .btn-text {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
    }

    .btn-view-detail:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 152, 0, 0.4);
        color: white;
    }

    /* Card Timestamp */
    .card-timestamp {
        text-align: center;
        margin-top: 0.75rem;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 500;
    }

    .card-timestamp i {
        opacity: 0.8;
    }

    /* ===========================
       ANIMATIONS
    =========================== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-15px);
        }
    }

    @keyframes pulse-warning {
        0%, 100% {
            box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
        }
        50% {
            box-shadow: 0 4px 20px rgba(255, 152, 0, 0.6);
        }
    }

    @keyframes pulse-success {
        0%, 100% {
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }
        50% {
            box-shadow: 0 4px 20px rgba(76, 175, 80, 0.6);
        }
    }

    .animate-fade-in {
        animation: fadeInUp 0.6s ease-out;
    }

    .animate-fade-in-delay {
        animation: fadeInUp 0.6s ease-out 0.2s both;
    }

    .animate-card-entry {
        animation: fadeInUp 0.6s ease-out both;
    }

    .pulse-warning {
        animation: pulse-warning 2s ease-in-out infinite;
    }

    .pulse-success {
        animation: pulse-success 2s ease-in-out infinite;
    }

    /* ===========================
       PAGINATION STYLING
    =========================== */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
    }

    .pagination-wrapper .pagination {
        gap: 0.5rem;
    }

    .pagination-wrapper .page-link {
        background: linear-gradient(135deg, #4CAF50, #81C784);
        border: 2px solid #FF9800;
        color: white;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.625rem 1rem;
        transition: all 0.3s ease;
    }

    .pagination-wrapper .page-link:hover {
        background: linear-gradient(135deg, #FF9800, #FFB74D);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
    }

    .pagination-wrapper .page-item.active .page-link {
        background: linear-gradient(135deg, #FF9800, #FFB74D);
        border-color: white;
        box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
    }

    /* ===========================
       RESPONSIVE DESIGN
    =========================== */
    @media (max-width: 992px) {
        .header-content-wrapper {
            flex-direction: column;
            text-align: center;
        }

        .header-text {
            width: 100%;
        }

        .header-text .d-flex {
            justify-content: center;
        }

        .btn-create-offer {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .negosiasi-header {
            padding: 1.5rem;
        }

        .header-title {
            font-size: 1.5rem;
        }

        .icon-wrapper {
            width: 50px;
            height: 50px;
        }

        .icon-wrapper i {
            font-size: 1.4rem;
        }

        .nego-card-body {
            padding: 1.25rem;
        }

        .empty-state-card {
            padding: 3rem 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .negosiasi-container {
            padding: 1rem !important;
        }

        .price-value {
            font-size: 0.95rem;
        }

        .quantity-total-wrapper {
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .total-info {
            align-items: flex-start;
        }
    }
</style>
@endsection
