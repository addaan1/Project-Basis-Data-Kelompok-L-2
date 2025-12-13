@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Glassmorphism Card -->
            <div class="card border-0 shadow-lg" style="border-radius: 20px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); overflow: hidden;">
                
                <!-- Gradient Header -->
                <div class="card-header border-0 py-4 px-5 position-relative" style="background: linear-gradient(135deg, #2E7D32, #66BB6A);">
                    <div class="d-flex align-items-center justify-content-between position-relative z-1">
                        <div>
                            <h4 class="mb-1 fw-bold text-white"><i class="bi bi-pencil-square me-2"></i>Edit Produk Beras</h4>
                            <p class="mb-0 text-white-50 small">Perbarui informasi produk Anda di pasar.</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-25 rounded-circle text-white">
                            <i class="bi bi-basket2" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    <form action="{{ route('market.update', $product->id_produk) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <!-- Left: Basic Info -->
                            <div class="col-md-8">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control form-control-lg rounded-4 fw-bold text-dark fs-5" id="nama_produk" name="nama_produk" value="{{ old('nama_produk', $product->nama_produk) }}" required style="border: 1px solid #ced4da;">
                                    <label for="nama_produk" class="text-secondary opacity-75 fw-bold"><i class="bi bi-tag-fill me-2 text-success fs-5"></i>Nama Produk</label>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control form-control-lg rounded-4 bg-light fs-5" value="{{ $product->jenis_beras }}" readonly>
                                            <label class="text-secondary opacity-75 fw-bold"><i class="bi bi-grid-fill me-2 text-success fs-5"></i>Jenis (Tidak dapat diubah)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control form-control-lg rounded-4 bg-light fs-5" value="{{ $product->kualitas }}" readonly>
                                            <label class="text-secondary opacity-75 fw-bold"><i class="bi bi-award-fill me-2 text-warning fs-5"></i>Kualitas</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-floating mb-3">
                                    <textarea class="form-control form-control-lg rounded-4 text-dark fs-5" id="deskripsi" name="deskripsi" style="height: 160px; border: 1px solid #ced4da;" required>{{ old('deskripsi', $product->deskripsi) }}</textarea>
                                    <label for="deskripsi" class="text-secondary opacity-75 fw-bold"><i class="bi bi-file-text-fill me-2 text-success fs-5"></i>Deskripsi Produk</label>
                                </div>
                            </div>

                            <!-- Right: Price, Stock, Photo -->
                            <div class="col-md-4">
                                <div class="p-4 rounded-4 bg-white h-100 shadow-sm border border-light">
                                    <h6 class="fw-bold text-success mb-3 small text-uppercase letter-spacing-1"><i class="bi bi-currency-dollar me-2"></i>Harga & Stok</h6>
                                    
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">Harga per Kg</label>
                                        <div class="input-group input-group-lg shadow-sm rounded-4">
                                            <span class="input-group-text bg-success text-white border-0 fw-bold fs-5">Rp</span>
                                            <input type="number" class="form-control border-0 bg-light fw-bold text-dark fs-5" name="harga" value="{{ old('harga', $product->harga) }}" required min="0">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">Stok Tersedia</label>
                                        <div class="input-group input-group-lg shadow-sm rounded-4">
                                            <input type="number" class="form-control border-0 bg-light fw-bold text-dark fs-5" name="stok" value="{{ old('stok', $product->stok) }}" required min="0">
                                            <span class="input-group-text bg-warning text-dark border-0 fw-bold fs-5">Kg</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">Lokasi Gudang</label>
                                        <input type="text" class="form-control form-control-lg rounded-4 border-light bg-light text-dark fs-5" name="lokasi_gudang" value="{{ old('lokasi_gudang', $product->lokasi_gudang) }}" required>
                                    </div>
                                    
                                    <hr class="my-4 border-secondary border-opacity-10">
                                    
                                    <!-- Current Photo Preview -->
                                    <div class="mb-2 text-center">
                                         <small class="text-muted d-block mb-2">Foto Saat Ini</small>
                                         <img src="{{ $product->foto ? asset('storage/'.$product->foto) : 'https://via.placeholder.com/100' }}" class="rounded-3 shadow-sm object-fit-cover" style="width: 100%; height: 120px;">
                                    </div>

                                    <label class="form-label small fw-bold mb-2 text-secondary mt-3"><i class="bi bi-camera-fill me-2"></i>Ganti Foto</label>
                                    <input type="file" class="form-control form-control-sm rounded-3" name="foto" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end align-items-center gap-3 mt-5 border-top border-secondary border-opacity-10 pt-4">
                            <a href="{{ route('market.index') }}" class="btn btn-link text-decoration-none text-muted fw-bold">Batal</a>
                            
                            <button type="submit" class="btn btn-lg px-5 text-white fw-bold rounded-pill shadow-lg hover-scale" 
                                    style="background: linear-gradient(135deg, #FF9800, #F57C00); border: none;">
                                <i class="bi bi-save-fill me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection