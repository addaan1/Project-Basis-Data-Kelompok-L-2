@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Seller Profile Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center py-4" style="border-radius: 16px;">
                <div class="card-body">
                    <div class="mb-3">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-user-circle fa-4x text-muted"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $seller->nama }}</h4>
                    <span class="badge bg-success mb-3">{{ ucfirst($seller->peran) }}</span>
                    
                    <hr>
                    
                    <div class="text-start px-3">
                        <p class="mb-2"><i class="fas fa-envelope me-2 text-muted"></i> {{ $seller->email }}</p>
                        <p class="mb-2"><i class="fas fa-map-marker-alt me-2 text-muted"></i> {{ $seller->alamat ?? 'Alamat belum diatur' }}</p>
                        <p class="mb-2"><i class="fas fa-phone me-2 text-muted"></i> {{ $seller->telepon ?? 'No. Telepon belum diatur' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seller's Products -->
        <div class="col-md-8">
            <h4 class="mb-4 fw-bold border-start border-4 border-success ps-3">Produk dari Penjual Ini</h4>
            
            @if($products->isEmpty())
                <div class="alert alert-info border-0 shadow-sm">
                    <i class="fas fa-info-circle me-2"></i> Penjual ini belum memiliki produk yang dijual.
                </div>
            @else
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100 product-card-hover">
                                @if($product->foto)
                                    <img src="{{ asset('storage/produk/' . $product->foto) }}" class="card-img-top" alt="{{ $product->nama_produk }}" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-image text-muted fa-3x"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title fw-bold text-dark">{{ $product->nama_produk }}</h5>
                                    <span class="badge bg-info text-dark mb-2">{{ $product->jenis_beras }}</span>
                                    <span class="badge bg-warning text-dark mb-2">{{ $product->kualitas }}</span>
                                    <h5 class="text-success fw-bold mb-3">Rp {{ number_format($product->harga, 0, ',', '.') }} <small class="text-muted fw-normal">/ kg</small></h5>
                                    
                                    <div class="d-grid">
                                        <a href="{{ route('market.show', $product->id_produk) }}" class="btn btn-outline-success">Lihat Produk</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
