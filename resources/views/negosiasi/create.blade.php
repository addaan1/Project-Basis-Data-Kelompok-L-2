@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Glassmorphism Card -->
            <div class="card border-0 shadow-lg" style="border-radius: 20px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); overflow: hidden;">
                
                <!-- Gradient Header (Green for Growth/Agriculture) -->
                <div class="card-header border-0 py-4 px-5 position-relative" style="background: linear-gradient(135deg, #2E7D32, #66BB6A);">
                    <div class="d-flex align-items-center justify-content-between position-relative z-1">
                        <div>
                            <h4 class="mb-1 fw-bold text-white"><i class="bi bi-chat-quote-fill me-2 fs-3"></i>Ajukan Negosiasi</h4>
                            <p class="mb-0 text-white-50 small fs-6">Tawar harga terbaik langsung kepada petani.</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-25 rounded-circle text-white">
                            <i class="bi bi-hand-thumbs-up-fill" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    <!-- Product Summary (Reference) -->
                    <div class="alert alert-success bg-opacity-10 border-success border-opacity-25 rounded-4 mb-5 d-flex align-items-center">
                        <img src="{{ $produk->foto ? asset('storage/'.$produk->foto) : 'https://via.placeholder.com/60' }}" 
                             alt="Produk" class="rounded-3 me-3 object-fit-cover shadow-sm" style="width: 60px; height: 60px;">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold text-success mb-1">{{ $produk->nama_produk }}</h6>
                            <div class="d-flex gap-3 text-muted small">
                                <span><i class="bi bi-tag-fill me-1 text-warning"></i>Rp {{ number_format($produk->harga_per_kg, 0, ',', '.') }}/kg</span>
                                <span><i class="bi bi-archive-fill me-1 text-primary"></i>Stok: {{ $produk->stok }} Kg</span>
                                <span><i class="bi bi-person-fill me-1 text-success"></i>{{ $produk->petani->nama }}</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('negosiasi.store', $produk) }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Left Column: Input Forms -->
                            <div class="col-md-7">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control form-control-lg rounded-4 fw-bold text-dark fs-5" id="harga_penawaran" name="harga_penawaran" value="{{ old('harga_penawaran') }}" required min="1" placeholder="0" style="border: 1px solid #ced4da;">
                                    <label for="harga_penawaran" class="text-secondary opacity-75 fw-bold"><i class="bi bi-currency-dollar me-2 text-warning fs-5"></i>Harga Tawaran (Rp/Kg)</label>
                                    @error('harga_penawaran') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control form-control-lg rounded-4 fw-bold text-dark fs-5" id="jumlah_kg" name="jumlah_kg" value="{{ old('jumlah_kg') }}" required min="1" max="{{ $produk->stok }}" placeholder="0" style="border: 1px solid #ced4da;">
                                    <label for="jumlah_kg" class="text-secondary opacity-75 fw-bold"><i class="bi bi-basket-fill me-2 text-success fs-5"></i>Jumlah (Kg)</label>
                                    <div class="form-text text-muted small ms-2 fw-bold"><i class="bi bi-info-circle me-1"></i>Max: {{ $produk->stok }} Kg</div>
                                    @error('jumlah_kg') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-floating mb-3">
                                    <textarea class="form-control form-control-lg rounded-4 text-dark fs-5" placeholder="Pesan untuk petani..." id="pesan" name="pesan" style="height: 120px; border: 1px solid #ced4da;">{{ old('pesan') }}</textarea>
                                    <label for="pesan" class="text-secondary opacity-75 fw-bold"><i class="bi bi-chat-text-fill me-2 text-primary fs-5"></i>Pesan (Opsional)</label>
                                </div>
                            </div>

                            <!-- Right Column: Calculation Preview -->
                            <div class="col-md-5">
                                <div class="p-4 rounded-4 bg-light h-100 border border-secondary border-opacity-10 d-flex flex-column justify-content-center">
                                    <h6 class="fw-bold text-muted mb-4 small text-uppercase text-center"><i class="bi bi-calculator me-2"></i>Estimasi Total</h6>
                                    
                                    <div class="text-center mb-4">
                                        <small class="d-block text-muted mb-1">Total Nilai Transaksi</small>
                                        <h3 class="fw-bold text-success display-6 total-display">Rp 0</h3>
                                    </div>

                                    <div class="alert alert-warning border-0 bg-warning bg-opacity-10 rounded-3 small">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        Harga tawaran Anda menunggu persetujuan Petani sebelum transaksi dapat diproses.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end align-items-center gap-3 mt-5 border-top border-secondary border-opacity-10 pt-4">
                             <a href="{{ route('pasar.show', $produk) }}" class="btn btn-link text-decoration-none text-muted fw-bold">Batal</a>
                             
                            <button type="submit" class="btn btn-lg px-5 text-white fw-bold rounded-pill shadow-lg hover-scale" 
                                    style="background: linear-gradient(135deg, #FF9800, #F57C00); border: none;">
                                <i class="bi bi-send-fill me-2"></i>Kirim Tawaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Real-time calculation script
    const hargaInput = document.getElementById('harga_penawaran');
    const jumlahInput = document.getElementById('jumlah_kg');
    const totalDisplay = document.querySelector('.total-display');

    function calculateTotal() {
        const harga = parseFloat(hargaInput.value) || 0;
        const jumlah = parseFloat(jumlahInput.value) || 0;
        const total = harga * jumlah;
        
        // Format Currency
        totalDisplay.innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    hargaInput.addEventListener('input', calculateTotal);
    jumlahInput.addEventListener('input', calculateTotal);
</script>
@endsection