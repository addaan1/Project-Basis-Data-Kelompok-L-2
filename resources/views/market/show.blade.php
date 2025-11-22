@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <div class="main-card animate-fade-in">
        
        <div class="card-header-custom d-flex align-items-center justify-content-between">
            <h2 class="text-white mb-0 font-weight-bold d-flex align-items-center">
                <i class="fas fa-box-open me-3"></i>
                {{ $product->nama_produk }}
            </h2>
            <a href="{{ route('market.index') }}" class="btn-back-custom">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-5 mb-4">
                    <div class="glass-panel p-3 mb-4">
                        @if($product->foto)
                            <div class="image-wrapper">
                                <img src="{{ asset('storage/produk/' . $product->foto) }}" alt="{{ $product->nama_produk }}" class="product-detail-image">
                            </div>
                        @else
                            <div class="product-placeholder-large">
                                <i class="fas fa-image fs-1 text-white-50 mb-3"></i>
                                <span class="text-white-50">Tidak ada foto produk</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="action-area">
                        @if(auth()->check())
                            @if(auth()->user()->peran == 'admin')
                                <div class="glass-panel p-3">
                                    <h6 class="text-white mb-3 border-bottom border-white-50 pb-2"><i class="fas fa-cog me-2"></i>Admin Actions</h6>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('market.edit', $product->id_produk) }}" class="btn btn-warning flex-fill text-white">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <button class="btn btn-danger flex-fill" onclick="confirmDelete({{ $product->id_produk }})">
                                            <i class="fas fa-trash me-1"></i>Hapus
                                        </button>
                                    </div>
                                </div>

                            @elseif(auth()->user()->peran == 'petani')
                                <div class="glass-panel p-3 text-center">
                                    <p class="text-white mb-3">Anda melihat detail produk Anda sendiri.</p>
                                    <a href="{{ route('market.index') }}" class="btn btn-primary w-100">
                                        <i class="fas fa-list me-1"></i>Kelola Produk Lain
                                    </a>
                                </div>

                            @elseif(auth()->user()->peran == 'pengepul')
                                <div class="glass-panel p-4">
                                    <h5 class="text-white mb-3 font-bold"><i class="fas fa-shopping-cart me-2"></i>Beli Langsung</h5>
                                    <form method="POST" action="{{ route('market.buy', ['market' => $product->id_produk]) }}">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label class="text-white small mb-1">Jumlah (kg)</label>
                                            <div class="input-group">
                                                <input type="number" name="jumlah" min="1" max="{{ $product->stok }}" required 
                                                       class="form-control glass-input" 
                                                       placeholder="Maks: {{ $product->stok }} kg">
                                                <span class="input-group-text glass-addon">kg</span>
                                            </div>
                                            <x-input-error :messages="$errors->get('jumlah')" class="mt-2 text-danger bg-white rounded px-2" />
                                        </div>
                                        
                                        <div class="total-preview bg-white/10 p-3 rounded mb-3 border border-white-50">
                                            <small class="text-white-50 d-block">Total Estimasi</small>
                                            <h4 class="text-white mb-0 font-weight-bold">Rp <span id="totalHarga">0</span></h4>
                                        </div>

                                        <button type="submit" class="btn-action-primary w-100 mb-3">
                                            <i class="fas fa-credit-card me-2"></i>Beli Sekarang
                                        </button>
                                    </form>

                                    <div class="text-center my-2 text-white-50">- atau -</div>

                                    <button class="btn-action-secondary w-100" onclick="openNegotiation({{ $product->id_produk }})">
                                        <i class="fas fa-comments me-2"></i>Negosiasi Harga
                                    </button>
                                </div>

                            @elseif(auth()->user()->peran == 'distributor')
                                <div class="glass-panel p-4 text-center">
                                    <div class="mb-3"><i class="fas fa-info-circle fa-3x text-info"></i></div>
                                    <h6 class="text-white mb-2">Info Distributor</h6>
                                    <p class="text-white-50 small mb-3">Produk ini dijual oleh Petani. Distributor hanya dapat membeli dari Pengepul.</p>
                                    <a href="{{ route('market.pengepul') }}" class="btn btn-outline-light btn-sm">
                                        <i class="fas fa-store me-1"></i>Cari Produk Pengepul
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="glass-panel p-4 text-center">
                                <i class="fas fa-lock fa-3x text-warning mb-3"></i>
                                <p class="text-white mb-3">Silakan login untuk melakukan transaksi.</p>
                                <a href="{{ route('login') }}" class="btn btn-light w-100 fw-bold text-success">Login Sekarang</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="glass-panel h-100 p-4">
                        <h4 class="text-white mb-4 border-bottom border-white-50 pb-2">Detail Informasi</h4>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="icon-box bg-info"><i class="fas fa-tag text-white"></i></div>
                                <div class="content">
                                    <label>Jenis Beras</label>
                                    <span>{{ $product->jenis_beras }}</span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="icon-box bg-success"><i class="fas fa-coins text-white"></i></div>
                                <div class="content">
                                    <label>Harga Satuan</label>
                                    <span class="fw-bold text-warning fs-5">Rp {{ number_format($product->harga, 0, ',', '.') }} <small class="text-white-50">/kg</small></span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="icon-box bg-primary"><i class="fas fa-user text-white"></i></div>
                                <div class="content">
                                    <label>Petani</label>
                                    <span>{{ $product->nama_petani }}</span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="icon-box bg-warning"><i class="fas fa-map-marker-alt text-white"></i></div>
                                <div class="content">
                                    <label>Lokasi Gudang</label>
                                    <span>{{ $product->lokasi_gudang }}</span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="icon-box bg-secondary"><i class="fas fa-warehouse text-white"></i></div>
                                <div class="content">
                                    <label>Stok Tersedia</label>
                                    <span>{{ $product->stok }} kg</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top border-white-50">
                            <h6 class="text-white mb-2"><i class="fas fa-align-left me-2"></i>Deskripsi Produk</h6>
                            <p class="text-white-50 leading-relaxed" style="text-align: justify;">
                                {{ $product->deskripsi }}
                            </p>
                        </div>

                        @if(isset($product->rating) && $product->rating > 0)
                            <div class="rating-box mt-4 p-3 rounded d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-white mb-1">Rating Petani</h6>
                                    <small class="text-white-50">{{ $product->review_count ?? 0 }} Ulasan</small>
                                </div>
                                <div class="text-end">
                                    <div class="stars text-warning fs-5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $product->rating ? '' : 'text-white-50' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="fw-bold text-white">{{ $product->rating }}/5.0</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->check() && auth()->user()->peran == 'pengepul')
<div class="modal fade" id="negotiationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white"><i class="fas fa-comments me-2"></i>Negosiasi Harga</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="negotiationForm" method="POST" action="{{ route('market.negotiate', ['market' => $product->id_produk]) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="text-white mb-1">Jumlah (kg)</label>
                        <input type="number" name="jumlah" class="form-control glass-input" min="1" max="{{ $product->stok }}" placeholder="Contoh: 100">
                        <x-input-error :messages="$errors->get('jumlah')" class="mt-2 text-danger bg-white rounded px-2" />
                    </div>
                    <div class="mb-3">
                        <label class="text-white mb-1">Harga Tawaran (Rp/kg)</label>
                        <input type="number" name="tawaran_harga" class="form-control glass-input" required min="0" step="100" placeholder="Harga normal: {{ $product->harga }}">
                        <x-input-error :messages="$errors->get('tawaran_harga')" class="mt-2 text-danger bg-white rounded px-2" />
                    </div>
                    <div class="mb-3">
                        <label class="text-white mb-1">Pesan (Opsional)</label>
                        <textarea name="pesan" class="form-control glass-input" rows="3" placeholder="Alasan penawaran..."></textarea>
                    </div>
                    <button type="submit" class="btn-action-primary w-100">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Tawaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    // Kalkulasi harga realtime
    const jumlahInput = document.querySelector('input[name="jumlah"]');
    if(jumlahInput) {
        jumlahInput.addEventListener('input', function() {
            const jumlah = parseInt(this.value) || 0;
            const harga = {{ $product->harga }};
            const total = jumlah * harga;
            document.getElementById('totalHarga').textContent = new Intl.NumberFormat('id-ID').format(total);
        });
    }

    function openNegotiation(productId) {
        new bootstrap.Modal(document.getElementById('negotiationModal')).show();
    }

    function confirmDelete(productId) {
        if (confirm('Yakin ingin menghapus produk ini?')) {
            window.location.href = `/market/${productId}/delete`; 
        }
    }
</script>

<style>
    /* --- Main Structure Styles --- */
    .main-card {
        background: linear-gradient(135deg, #4CAF50, #81C784);
        border: 1px solid #FF9800;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        color: #fff;
        position: relative;
    }

    .main-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, #FF9800, #FFEB3B);
    }

    .card-header-custom {
        background: rgba(255, 255, 255, 0.1);
        padding: 20px 30px;
        backdrop-filter: blur(5px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* --- Glass Panels & Components --- */
    .glass-panel {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        backdrop-filter: blur(5px);
        transition: transform 0.3s ease;
    }
    
    .glass-panel:hover {
        border-color: rgba(255, 255, 255, 0.4);
    }

    /* --- Images --- */
    .image-wrapper {
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .product-detail-image {
        width: 100%;
        height: 350px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .image-wrapper:hover .product-detail-image {
        transform: scale(1.05);
    }

    .product-placeholder-large {
        height: 350px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 2px dashed rgba(255, 255, 255, 0.3);
        border-radius: 8px;
    }

    /* --- Info Grid System --- */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }

    .info-item {
        background: rgba(0, 0, 0, 0.1);
        padding: 12px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .icon-box {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .info-item .content {
        display: flex;
        flex-direction: column;
    }

    .info-item label {
        font-size: 0.8rem;
        color: rgba(255,255,255,0.7);
        margin-bottom: 0;
    }

    .info-item span {
        font-weight: 600;
        font-size: 1rem;
    }

    /* --- Form Elements --- */
    .glass-input {
        background: rgba(255, 255, 255, 0.2) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #fff !important;
        border-radius: 8px;
    }
    
    .glass-input::placeholder { color: rgba(255,255,255,0.6); }
    
    .glass-input:focus {
        background: rgba(255, 255, 255, 0.3) !important;
        box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.3) !important;
        border-color: #FF9800 !important;
    }

    .glass-addon {
        background: rgba(255, 255, 255, 0.3) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #fff !important;
    }

    /* --- Buttons --- */
    .btn-back-custom {
        padding: 8px 20px;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: #fff;
        border-radius: 20px;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-back-custom:hover {
        background: #FF9800;
        border-color: #FF9800;
        color: #fff;
        transform: translateX(-3px);
    }

    .btn-action-primary {
        background: linear-gradient(135deg, #FF9800, #FFB74D);
        border: none;
        padding: 12px;
        border-radius: 8px;
        color: white;
        font-weight: bold;
        transition: all 0.3s;
    }

    .btn-action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 152, 0, 0.4);
    }

    .btn-action-secondary {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.4);
        padding: 12px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-action-secondary:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }

    /* --- Modal Styles --- */
    .glass-modal {
        background: linear-gradient(135deg, #43A047, #66BB6A);
        border: 1px solid #FF9800;
        color: #fff;
    }

    .rating-box {
        background: rgba(0,0,0,0.2);
    }

    /* --- Animation --- */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeInUp 0.6s ease-out forwards; }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .card-header-custom {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection