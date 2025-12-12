@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 py-8 text-center">
    <!-- Judul dengan Box Orange -->

    <!-- Card Utama Market -->
    <div class="market-card animate-fade-in">
        <div class="card-header-custom d-flex align-items-center justify-content-between mb-4">
            <h2 class="text-white mb-0 font-weight-bold">
                <i class="fas fa-store me-2"></i>Daftar Produk Tersedia
            </h2>
            <div class="stats-summary d-flex gap-3 align-items-center">
                <span class="badge bg-orange text-white px-3 py-2">
                    <i class="fas fa-boxes me-1"></i>Total: {{ $products->count() }}
                </span>
                <!-- Tombol Global Tambah Produk untuk Petani/Admin -->
                @if(auth()->check() && (auth()->user()->peran == 'petani' || auth()->user()->peran == 'admin'))
                    <a href="{{ route('market.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i>Tambah Produk
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            @if($products->isEmpty())
                <div class="empty-state text-center py-8">
                    <i class="fas fa-inbox fs-1 text-light mb-4"></i>
                    <h4 class="text-white mb-2">Tidak ada produk beras yang tersedia</h4>
                    <p class="text-light">Pasar sedang kosong. Cek lagi nanti atau hubungi petani terdekat.</p>
                    @if(auth()->check() && (auth()->user()->peran == 'petani' || auth()->user()->peran == 'admin'))
                        <a href="{{ route('market.create') }}" class="btn btn-success mt-3">
                            <i class="fas fa-plus me-1"></i>Tambah Produk Pertama
                        </a>
                    @else
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline-light mt-3">Kembali ke Dashboard</a>
                    @endif
                </div>
            @else
                <div class="row">
                    @foreach($products as $index => $product)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="product-card animate-slide-in" style="animation-delay: {{ $index * 0.1 }}s;">
                                @if($product->foto)
                                    <img src="{{ asset('storage/produk/' . $product->foto) }}" alt="{{ $product->nama_produk }}" class="product-image">
                                @else
                                    <div class="product-placeholder">
                                        <i class="fas fa-image text-muted"></i>
                                        <span class="text-muted">Tidak ada foto</span>
                                    </div>
                                @endif
                                
                                <div class="product-body">
                                    <h3 class="product-title">{{ $product->nama_produk }}</h3>
                                    
                                    <div class="product-info">
                                        <div class="info-item">
                                            <i class="fas fa-tag text-info me-1"></i>
                                            <span>{{ $product->jenis_beras }}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-user text-success me-1"></i>
                                            <a href="{{ route('market.seller', $product->id_petani) }}" class="text-decoration-none text-white hover-underline">
                                                <span>{{ $product->nama_petani }}</span>
                                            </a>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-map-marker-alt text-warning me-1"></i>
                                            <span>{{ $product->lokasi_gudang }}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-warehouse text-secondary me-1"></i>
                                            <span>{{ $product->stok }} kg tersedia</span>
                                        </div>
                                    </div>
                                    
                                    <div class="product-price">
                                        <i class="fas fa-coins text-success me-1"></i>
                                        <span class="price">Rp {{ number_format($product->harga, 0, ',', '.') }} / kg</span>
                                    </div>
                                    
                                    <!-- Tombol Berdasarkan Role (Fokus: Beli Produk atau Negosiasi) -->
                                    <div class="product-actions">
                                        @if(auth()->user()->peran == 'admin')
                                            <a href="{{ route('market.edit', $product->id_produk) }}" class="btn btn-warning btn-sm me-2">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $product->id_produk }})">
                                                <i class="fas fa-trash me-1"></i>Hapus
                                            </button>
                                        @elseif(auth()->user()->peran == 'petani')
                                            <a href="{{ route('market.show', $product->id_produk) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Lihat Detail
                                            </a>
                                        @elseif(auth()->user()->peran == 'pengepul')
                                            <!-- Pengepul: Beli Langsung atau Negosiasi -->
                                            <a href="{{ route('market.show', $product->id_produk) }}" class="btn btn-success btn-sm me-2">
                                                <i class="fas fa-shopping-cart me-1"></i>Beli Langsung
                                            </a>
                                            <button class="btn btn-info btn-sm" onclick="openNegotiation({{ $product->id_produk }})">
                                                <i class="fas fa-comments me-1"></i>Negosiasi Harga
                                            </button>
                                        @elseif(auth()->user()->peran == 'distributor')
                                            <!-- Distributor: Hanya Beli Langsung (dari pengepul, tapi di market ini tampilkan opsi beli) -->
                                            <a href="{{ route('market.show', $product->id_produk) }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-shopping-cart me-1"></i>Beli Langsung
                                            </a>
                                        @else
                                            <a href="{{ route('market.show', $product->id_produk) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Lihat Detail
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination jika diperlukan (asumsi dari Laravel) -->
                @if(method_exists($products, 'links'))
                    <div class="pagination-wrapper mt-4">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Modal Negosiasi (Contoh sederhana, sesuaikan dengan JS backend) -->
<div class="modal fade" id="negotiationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Negosiasi Harga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="negotiationForm">
                    <input type="hidden" id="productId" name="product_id">
                    <div class="mb-3">
                        <label class="form-label">Harga Tawaran (Rp/kg)</label>
                        <input type="number" class="form-control" name="tawaran_harga" required min="0" step="1000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pesan untuk Petani (Opsional)</label>
                        <textarea class="form-control" name="pesan" rows="3" placeholder="Tulis alasan tawaran Anda..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Tawaran</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openNegotiation(productId) {
        document.getElementById('productId').value = productId;
        new bootstrap.Modal(document.getElementById('negotiationModal')).show();
    }
    
    function confirmDelete(productId) {
        if (confirm('Yakin ingin menghapus produk ini?')) {
            // Redirect ke route delete (asumsi route ada)
            window.location.href = `/market/${productId}`;
        }
    }
    
    // Handle form negosiasi (contoh AJAX, sesuaikan dengan backend)
    document.getElementById('negotiationForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        // Kirim AJAX ke route negosiasi (contoh: fetch atau axios)
        const formData = new FormData(this);
        // fetch('/market/negotiate', { method: 'POST', body: formData })
        //     .then(response => response.json())
        //     .then(data => { alert('Tawaran dikirim!'); })
        //     .catch(error => alert('Error: ' + error));
        alert('Tawaran dikirim! Menunggu konfirmasi petani.');
        bootstrap.Modal.getInstance(document.getElementById('negotiationModal')).hide();
        this.reset();
    });
</script>

<style>
    .market-card {
        background: linear-gradient(135deg, rgba(139, 195, 74, 0.15), rgba(244, 196, 48, 0.15));
        backdrop-filter: blur(20px);
        border: 2px solid rgba(244, 196, 48, 0.3);
        border-radius: 24px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        padding: var(--space-lg);
    }
    
    .market-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--rice-gold), var(--rice-green));
    }
    
    .market-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
        border-color: var(--rice-gold);
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
    
    .stats-summary .badge:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }
    
    .product-card {
        background: rgba(255, 255, 255, 0.25);
        border: 1px solid rgba(244, 196, 48, 0.2);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-sm);
    }
    
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        background: rgba(255, 255, 255, 0.35);
        border-color: var(--rice-gold);
    }
    
    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    .product-placeholder {
        width: 100%;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.875rem;
    }
    
    .product-placeholder i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .product-body {
        padding: 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .product-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--deep-green);
        margin-bottom: var(--space-sm);
        line-height: 1.3;
        font-family: var(--font-heading);
    }
    
    .product-info {
        margin-bottom: 1rem;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0.5rem;
    }
    
    .product-price {
        margin-bottom: 1rem;
        font-size: 1.125rem;
        font-weight: 600;
        color: #fff;
    }
    
    .price {
        color: #fff;
    }
    
    .product-actions {
        margin-top: auto;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .product-actions .btn {
        flex: 1;
        min-width: auto;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .product-actions .btn:hover {
        transform: translateY(-1px);
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
            padding: 10px 20px;
            width: 100%;
            display: block;
        }
        
        .text-2xl { font-size: 1.5rem !important; }
        
        .card-header-custom {
            flex-direction: column;
            text-align: center;
            gap: 10px;
        }
        
        .stats-summary {
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .product-actions {
            flex-direction: column;
        }
        
        .product-actions .btn {
            width: 100%;
        }
    }
</style>
@endsection