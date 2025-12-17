@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Main Content Card with Orange Header -->
    <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
        
        <!-- Header Section -->
        <div class="card-header border-0 py-4 px-4 d-flex align-items-center justify-content-between" 
             style="background: linear-gradient(135deg, #FF9800, #F57C00); color: white;">
            <div>
                <h4 class="mb-1 fw-bold font-poppins">
                    <i class="bi bi-shop-window me-2"></i>Pasar Beras
                </h4>
                <p class="mb-0 opacity-75 small font-poppins">Temukan beras berkualitas langsung dari petani</p>
            </div>
            
            @if(auth()->check() && (auth()->user()->peran == 'petani' || auth()->user()->peran == 'admin'))
                <a href="{{ route('market.create') }}" class="btn btn-light text-warning fw-bold rounded-pill shadow-sm px-4 hover-scale" 
                   style="color: #F57C00 !important;">
                    <i class="bi bi-plus-circle-fill me-2"></i>Jual Beras
                </a>
            @endif
        </div>

        <!-- Body Section -->
        <div class="card-body px-4 pb-4 pt-4 position-relative" style="background: linear-gradient(180deg, #E8F5E9 0%, #C8E6C9 100%); min-height: 600px; overflow: hidden;">
            
            <!-- Decorative Background Pattern -->
            <div class="position-absolute top-0 start-0 w-100 h-100" style="opacity: 0.1; pointer-events: none; background-image: radial-gradient(#1B5E20 1px, transparent 1px); background-size: 24px 24px;"></div>
            <div class="position-absolute bottom-0 end-0 w-100 h-50" style="background: linear-gradient(to top, rgba(46, 125, 50, 0.2), transparent); pointer-events: none;"></div>

            @if(!$products->isEmpty())
                <!-- Stats / Info Bar -->
                <div class="row justify-content-center mb-4 animate-fade-in position-relative z-1">
                    <div class="col-md-auto">
                        <div class="card border-0 shadow-sm py-3 px-5 d-flex flex-row align-items-center justify-content-center" 
                             style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(5px); border-radius: 50px; border: 1px solid rgba(46, 125, 50, 0.1) !important;">
                            <div class="bg-gradient-green text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" 
                                 style="width: 48px; height: 48px;">
                                <i class="bi bi-basket3-fill fs-5"></i>
                            </div>
                            <div class="text-center text-md-start">
                                <h6 class="fw-bold mb-0 font-poppins" style="color: #1B5E20;">Total Produk</h6>
                                <span class="fw-bold fs-5" style="color: #2E7D32;">{{ $products->count() }} Pilihan Beras</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Product Grid -->
            @if($products->isEmpty())
                <div class="d-flex flex-column align-items-center justify-content-center py-5 animate-fade-in text-center position-relative z-1">
                    <div class="bg-white p-5 rounded-circle shadow-sm mb-4 d-flex align-items-center justify-content-center position-relative" 
                         style="width: 220px; height: 220px;">
                         <div class="position-absolute w-100 h-100 rounded-circle" style="background: radial-gradient(circle, rgba(46,125,50,0.05) 0%, transparent 70%);"></div>
                        <i class="bi bi-inbox-fill text-muted display-1 opacity-25"></i>
                    </div>
                    <h3 class="text-dark fw-bold mb-2 font-poppins">Belum Ada Produk</h3>
                    <p class="text-secondary fs-5 mb-4" style="max-width: 500px;">Saat ini belum ada petani yang menawarkan hasil panen.</p>
                    
                    @if(auth()->check() && (auth()->user()->peran == 'petani' || auth()->user()->peran == 'admin'))
                        <a href="{{ route('market.create') }}" class="btn btn-success btn-lg rounded-pill px-5 shadow fw-bold hover-scale custom-btn-green">
                            <i class="bi bi-plus-lg me-2"></i>Mulai Jualan
                        </a>
                    @endif
                </div>
            @else
                <div class="row g-4 position-relative z-1">
                    @foreach($products as $index => $product)
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                            <div class="card h-100 border-0 shadow-sm product-card animate-slide-in position-relative overflow-hidden" 
                                 style="background: #ffffff; border-radius: 24px; animation-delay: {{ $index * 0.1 }}s;">
                                
                                <!-- Decorative Gradient Top -->
                                <div class="position-absolute top-0 start-0 w-100 opacity-25" style="height: 100px; background: linear-gradient(180deg, rgba(232, 245, 233, 1) 0%, rgba(255, 255, 255, 0) 100%); pointer-events: none;"></div>

                                <!-- Image Section -->
                                <div class="position-relative p-3" style="height: 250px;">
                                    @if($product->foto)
                                        <div class="w-100 h-100 rounded-4 overflow-hidden position-relative bg-light">
                                            <img src="{{ asset('storage/' . $product->foto) }}" 
                                                 alt="{{ $product->nama_produk }}" 
                                                 class="w-100 h-100 object-fit-contain p-2 hover-zoom"
                                                 style="mix-blend-mode: multiply;">
                                        </div>
                                    @else
                                        <div class="w-100 h-100 rounded-4 bg-light d-flex align-items-center justify-content-center text-muted">
                                            <div class="text-center opacity-50">
                                                <i class="bi bi-image display-4"></i>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Price Tag (Floating) -->
                                    <div class="position-absolute bottom-0 start-0 m-4 z-2">
                                        <div class="badge shadow px-3 py-2 rounded-pill d-flex align-items-center" 
                                              style="background: linear-gradient(135deg, #2E7D32, #43A047); border: 2px solid #ffffff;">
                                            <i class="bi bi-tag-fill me-2 text-warning"></i>
                                            <div class="text-start">
                                                <div style="font-size: 0.65rem; opacity: 0.9; font-weight: normal; line-height: 1;">Harga per Kg</div>
                                                <div class="fs-6 fw-bold mt-1">Rp {{ number_format($product->harga, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="card-body px-4 pb-4 pt-2 d-flex flex-column">
                                    
                                    <!-- Category & Stock -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge bg-white border border-warning rounded-pill px-3 py-1 fw-bold shadow-sm" style="font-size: 0.75rem; color: #E65100 !important;">
                                            {{ $product->jenis_beras }}
                                        </span>
                                        <div class="d-flex align-items-center text-success fw-bold small">
                                            <i class="bi bi-box-seam me-1"></i>
                                            {{ $product->stok }} Kg
                                        </div>
                                    </div>

                                    <!-- Product Title -->
                                    <h5 class="card-title fw-bold text-dark mb-1 font-poppins text-truncate" 
                                        title="{{ $product->nama_produk }}" style="font-size: 1.2rem; letter-spacing: -0.5px;">
                                        {{ $product->nama_produk }}
                                    </h5>
                                    
                                    <!-- Seller Info -->
                                    <div class="d-flex align-items-center mb-4 mt-1">
                                        <i class="bi bi-shop text-muted me-2 small"></i>
                                        <a href="{{ route('market.seller', $product->id_petani) }}" class="text-secondary small fw-bold text-decoration-none hover-text-orange text-truncate" style="max-width: 180px;">
                                            {{ $product->nama_petani }}
                                        </a>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="mt-auto pt-3 border-top border-light d-grid gap-2">
                                        @if(auth()->check())
                                            @if(auth()->user()->peran == 'admin')
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <a href="{{ route('market.edit', $product->id_produk) }}" class="btn btn-light w-100 rounded-pill fw-bold border text-dark small-text hover-scale">Edit</a>
                                                    </div>
                                                    <div class="col-6">
                                                        <button onclick="confirmDelete({{ $product->id_produk }})" class="btn btn-light w-100 rounded-pill fw-bold border text-danger small-text hover-scale">Hapus</button>
                                                    </div>
                                                </div>
                                            @elseif(auth()->user()->peran == 'petani')
                                                <a href="{{ route('market.show', $product->id_produk) }}" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm custom-btn-green py-2 hover-scale">
                                                    Lihat Detail
                                                </a>
                                            @elseif(auth()->user()->peran == 'pengepul' || auth()->user()->peran == 'distributor')
                                                <div class="row g-2">
                                                    <div class="col-8">
                                                        <a href="{{ route('market.show', $product->id_produk) }}" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm custom-btn-green small-text d-flex align-items-center justify-content-center py-2 hover-scale">
                                                            <i class="bi bi-cart-fill me-2"></i>Beli
                                                        </a>
                                                    </div>
                                                    <div class="col-4">
                                                        <button onclick="openNegotiation({{ $product->id_produk }})" class="btn btn-warning w-100 rounded-pill fw-bold shadow-sm custom-btn-orange small-text text-white py-2 hover-scale" title="Nego Harga">
                                                            <i class="bi bi-chat-dots-fill"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <a href="{{ route('market.show', $product->id_produk) }}" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm custom-btn-green py-2 hover-scale">Link Detail</a>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm hover-scale">Login</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-5 d-flex justify-content-center position-relative z-1">
                    @if($products instanceof \Illuminate\Contracts\Pagination\Paginator && $products->hasPages())
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-custom">
                                {{-- Previous Page Link --}}
                                @if ($products->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="bi bi-chevron-left"></i>
                                            <span class="d-none d-sm-inline ms-1">Previous</span>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $products->previousPageUrl() }}" rel="prev">
                                            <i class="bi bi-chevron-left"></i>
                                            <span class="d-none d-sm-inline ms-1">Previous</span>
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($products->links()->elements[0] as $page => $url)
                                    @if ($page == $products->currentPage())
                                        <li class="page-item active" aria-current="page">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($products->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $products->nextPageUrl() }}" rel="next">
                                            <span class="d-none d-sm-inline me-1">Next</span>
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <span class="d-none d-sm-inline me-1">Next</span>
                                            <i class="bi bi-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Negotiation -->
<div class="modal fade" id="negotiationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #FF9800, #F57C00);">
                <h5 class="modal-title fw-bold font-poppins"><i class="bi bi-chat-quote-fill me-2"></i>Ajukan Penawaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form id="negotiationForm" method="POST">
                    @csrf
                    <input type="hidden" id="productId" name="product_id">
                    
                    <div class="alert alert-warning border-0 rounded-3 d-flex align-items-center mb-3">
                        <i class="bi bi-info-circle-fill fs-4 me-3 text-warning"></i>
                        <div class="small lh-sm" style="color: black !important;">
                            Penawaran akan dikirim ke petani. Pastikan harga masuk akal.
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" class="form-control rounded-4 border-0 shadow-sm" id="tawaran_harga" name="tawaran_harga" required min="1000">
                        <label for="tawaran_harga">Harga Tawaran (Rp/kg)</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                         <input type="number" class="form-control rounded-4 border-0 shadow-sm" id="jumlah" name="jumlah" required min="1" placeholder="Jumlah (Kg)">
                         <label for="jumlah">Jumlah (Kg)</label>
                    </div>

                    <div class="form-floating mb-4">
                        <textarea class="form-control rounded-4 border-0 shadow-sm" id="pesan" name="pesan" style="height: 100px"></textarea>
                        <label for="pesan">Pesan Tambahan (Opsional)</label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill shadow custom-btn-orange py-2">
                            Kirim Penawaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openNegotiation(productId) {
        document.getElementById('productId').value = productId;
        // Dynamically set form action
        document.getElementById('negotiationForm').action = "{{ url('/market') }}/" + productId + "/negotiate";
        var myModal = new bootstrap.Modal(document.getElementById('negotiationModal'));
        myModal.show();
    }
    
    function confirmDelete(productId) {
        if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
            alert('Fitur hapus belum aktif dalam demo ini.');
        }
    }
</script>
@endsection