{{-- index.blade.php --}}
@extends('layouts.main')

@section('content')
    <!-- Card Utama Negosiasi -->
    <div class="negotiation-card animate-fade-in">
        <div class="card-header-custom d-flex align-items-center justify-content-between mb-4">
            <h2 class="text-white mb-0 font-weight-bold">
                <i class="fas fa-list me-2"></i>Daftar Negosiasi
            </h2>
            <div class="stats-summary d-flex gap-3 align-items-center">
                <span class="badge bg- px-3 py-2">
                    <i class="fas fa-file-contract me-1"></i>Total: {{ $negotiations->count() }}
                </span>
                @if(auth()->user()->peran == 'pengepul')
                    <a href="{{ route('market.index') }}" class="btn-back-to-market">
                        <i class="fas fa-store me-1"></i>Kembali ke Pasar
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            @if ($negotiations->isEmpty())
                <div class="empty-state text-center py-8">
                    <i class="fas fa-comments fs-2 text-light mb-4"></i>
                    <h4 class="text-white mb-2">Belum ada negosiasi yang tercatat</h4>
                    <p class="text-light">Mulai dengan menawarkan harga pada produk di pasar.</p>
                    @if(auth()->user()->peran == 'pengepul')
                        <a href="{{ route('market.index') }}" class="btn btn-outline-light mt-3">
                            <i class="fas fa-store me-1"></i>Cari Produk untuk Negosiasi
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="negotiation-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>ID Negosiasi</th>
                                <th><i class="fas fa-box me-1"></i>Produk</th>
                                <th><i class="fas fa-user me-1"></i>Penawar</th>
                                <th><i class="fas fa-coins me-1"></i>Harga Penawaran</th>
                                <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                <th><i class="fas fa-clock me-1"></i>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($negotiations as $index => $negotiation)
                                <tr class="animate-slide-in" style="animation-delay: {{ $index * 0.1 }}s;">
                                    <td>{{ $negotiation->id }}</td>
                                    <td>
                                        <span class="product-link">
                                            {{ $negotiation->product_name }}
                                        </span>
                                    </td>
                                    <td>{{ $negotiation->bidder_name }}</td>
                                    <td>
                                        <span class="price-badge">Rp {{ number_format($negotiation->offer_price, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <span class="status-badge 
                                            @if($negotiation->status == 'diterima') status-accepted
                                            @elseif($negotiation->status == 'dalam_proses') status-pending
                                            @else status-rejected @endif">
                                            {{ ucfirst(str_replace('_', ' ', $negotiation->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $negotiation->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($negotiations, 'links'))
                    <div class="pagination-wrapper mt-4">
                        {{ $negotiations->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
    .negotiation-card {
        background: linear-gradient(135deg, rgba(139, 195, 74, 0.15), rgba(244, 196, 48, 0.15));
        backdrop-filter: blur(20px);
        border: 2px solid rgba(244, 196, 48, 0.3);
        border-radius: 24px;
        box-shadow: var(--shadow-md);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        padding: var(--space-lg);
    }
    
    .negotiation-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--rice-gold), var(--rice-green));
    }
    
    .negotiation-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-xl);
        border-color: var(--rice-gold);
    }
    
    .title-box {
        background: linear-gradient(135deg, #FF9800, #FFB74D);
        border: 1px solid #2E7D32;
        border-radius: 16px;
        padding: 16px 24px;
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
    
    .card-header-custom {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        border-radius: 12px;
        padding: 20px;
        backdrop-filter: blur(5px);
        margin-bottom: 0;
    }
    
    .stats-summary .badge {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        transition: all 0.3s ease;
        color: #fff !important;
    }
    
    .btn-back-to-market {
        display: inline-flex;
        align-items: center;
        padding: var(--space-xs) var(--space-md);
        background: linear-gradient(135deg, var(--rice-gold), var(--rice-green));
        color: var(--pure-white);
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        box-shadow: var(--shadow-sm);
        font-family: var(--font-body);
    }
    
    .btn-back-to-market:hover {
        background: linear-gradient(135deg, var(--rice-green), var(--rice-gold));
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        color: var(--pure-white);
    }
    
    .empty-state {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 40px 20px;
        backdrop-filter: blur(5px);
        border: 1px dashed rgba(255, 255, 255, 0.3);
    }
    
    .empty-state i {
        opacity: 0.5;
        color: rgba(255, 255, 255, 0.5);
    }
    
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .negotiation-table {
        width: 100%;
        background: rgba(255, 255, 255, 0.15);
        border-collapse: collapse;
        color: #fff;
        font-size: 0.95rem;
    }
    
    .negotiation-table th {
        background: rgba(255, 255, 255, 0.1);
        padding: 16px 12px;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        color: #fff;
    }
    
    .negotiation-table td {
        padding: 16px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .negotiation-table tr:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.01);
        transition: all 0.2s ease;
    }
    
    .product-link {
        color: #fff;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    
    .product-link:hover {
        color: #FFB74D;
        text-decoration: underline;
    }
    
    .price-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 500;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-block;
        transition: all 0.3s ease;
    }
    
    .status-pending {
        background: rgba(255, 193, 7, 0.2);
        color: #FFC107;
        border: 1px solid #FFC107;
    }
    
    .status-accepted {
        background: rgba(76, 175, 80, 0.2);
        color: #4CAF50;
        border: 1px solid #4CAF50;
    }
    
    .status-rejected {
        background: rgba(244, 67, 54, 0.2);
        color: #F44336;
        border: 1px solid #F44336;
    }
    
    .pagination-wrapper {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 15px;
        backdrop-filter: blur(5px);
        text-align: center;
    }
    
    .pagination-wrapper .pagination .page-link {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #fff !important;
        margin: 0 2px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .pagination-wrapper .pagination .page-link:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
    }
    
    .pagination-wrapper .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #FF9800, #FFB74D);
        border-color: #FF9800;
    }
    
    /* Animasi */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    .animate-fade-in { animation: fadeInUp 0.6s ease-out forwards; }
    .animate-slide-in { animation: slideIn 0.4s ease-out forwards; }
    
    /* Responsiveness */
    @media (max-width: 768px) {
        .title-box {
            padding: 12px 20px;
            width: 100%;
            display: block;
        }
        
        .card-header-custom {
            flex-direction: column;
            text-align: center;
            gap: 10px;
        }
        
        .stats-summary {
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .negotiation-table {
            font-size: 0.85rem;
        }
        
        .negotiation-table th, .negotiation-table td {
            padding: 12px 8px;
        }
        
        .status-badge {
            padding: 4px 8px;
            font-size: 0.75rem;
        }
    }
</style>
@endsection