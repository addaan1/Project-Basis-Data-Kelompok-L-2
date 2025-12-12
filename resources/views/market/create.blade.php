@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-white py-3 pl-4">
                    <h5 class="mb-0 fw-bold" style="color: #2E7D32;">Tambah Produk Baru</h5>
                </div>
                <div class="card-body p-4" style="background-color: #fcfcfc;">
                    <form method="POST" action="{{ route('market.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Petani Name is auto-filled by Auth --}}
                        <div class="mb-4">
                            <label class="form-label text-muted small text-uppercase fw-bold">Penjual (Anda)</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->nama }}" disabled readonly style="background-color: #e9ecef;">
                        </div>

                        <div class="mb-4">
                            <label for="nama_produk" class="form-label fw-bold">Nama Produk</label>
                            <input id="nama_produk" type="text" class="form-control @error('nama_produk') is-invalid @enderror" name="nama_produk" value="{{ old('nama_produk') }}" required placeholder="Contoh: Beras Pandan Wangi Super">
                            @error('nama_produk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="jenis_beras" class="form-label fw-bold">Jenis Beras</label>
                                <select id="jenis_beras" class="form-select @error('jenis_beras') is-invalid @enderror" name="jenis_beras" required onchange="updateQuality()">
                                    <option value="">Pilih Jenis Beras</option>
                                    {{-- 30 Varieties --}}
                                    <optgroup label="Premium">
                                        <option value="Pandan Wangi">Pandan Wangi</option>
                                        <option value="Rojolele">Rojolele</option>
                                        <option value="Menthik Wangi">Menthik Wangi</option>
                                        <option value="Menthik Susu">Menthik Susu</option>
                                        <option value="Beras Merah">Beras Merah</option>
                                        <option value="Beras Hitam">Beras Hitam</option>
                                        <option value="Beras Coklat">Beras Coklat</option>
                                        <option value="Beras Ketan Putih">Beras Ketan Putih</option>
                                        <option value="Beras Ketan Hitam">Beras Ketan Hitam</option>
                                        <option value="Sintanur">Sintanur</option>
                                        <option value="Hibrida Sembada">Hibrida Sembada</option>
                                        <option value="Hibrida Mapa">Hibrida Mapa</option>
                                        <option value="Beras Basmati">Beras Basmati</option>
                                        <option value="Beras Japonica">Beras Japonica</option>
                                        <option value="Beras Jasmine">Beras Jasmine</option>
                                        <option value="Beras Ketan">Beras Ketan</option>
                                    </optgroup>
                                    <optgroup label="Medium">
                                        <option value="IR 64">IR 64 (Setra Ramos)</option>
                                        <option value="IR 42">IR 42 (Pera)</option>
                                        <option value="Cisadane">Cisadane</option>
                                        <option value="Ciherang">Ciherang</option>
                                        <option value="Inpari 32">Inpari 32</option>
                                        <option value="Inpari 42">Inpari 42</option>
                                        <option value="Batang Lembang">Batang Lembang</option>
                                        <option value="Ciliwung">Ciliwung</option>
                                        <option value="Way Apo Buru">Way Apo Buru</option>
                                        <option value="Memberamo">Memberamo</option>
                                        <option value="Inpara">Inpara</option>
                                        <option value="Inpago">Inpago</option>
                                        <option value="Beras Pecah Kulit">Beras Pecah Kulit</option>
                                    </optgroup>
                                    <optgroup label="Standard">
                                        <option value="Beras Menir">Beras Menir</option>
                                    </optgroup>
                                </select>
                                @error('jenis_beras')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="kualitas" class="form-label fw-bold">Kualitas (Otomatis)</label>
                                <input id="kualitas" type="text" class="form-control @error('kualitas') is-invalid @enderror" name="kualitas" value="{{ old('kualitas') }}" readonly required style="background-color: #e9ecef;">
                                @error('kualitas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="harga" class="form-label fw-bold">Harga per Kg (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input id="harga" type="number" class="form-control @error('harga') is-invalid @enderror" name="harga" value="{{ old('harga') }}" required min="0" placeholder="0">
                                </div>
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="stok" class="form-label fw-bold">Stok Tersedia (Kg)</label>
                                <input id="stok" type="number" class="form-control @error('stok') is-invalid @enderror" name="stok" value="{{ old('stok') }}" required min="0" placeholder="0">
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="lokasi_gudang" class="form-label fw-bold">Lokasi Gudang</label>
                            <input id="lokasi_gudang" type="text" class="form-control @error('lokasi_gudang') is-invalid @enderror" name="lokasi_gudang" value="{{ old('lokasi_gudang') }}" required placeholder="Contoh: Gudang KUD Jaya Makmur, Karawang">
                            @error('lokasi_gudang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="form-label fw-bold">Deskripsi Produk</label>
                            <textarea id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" name="deskripsi" required rows="4" placeholder="Jelaskan detail produk Anda...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="foto" class="form-label fw-bold">Foto Produk</label>
                            <input id="foto" type="file" class="form-control @error('foto') is-invalid @enderror" name="foto" accept="image/*">
                            <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-5">
                            <a href="{{ route('market.index') }}" class="btn btn-light border px-4">Batal</a>
                            <button type="submit" class="btn btn-success px-5 fw-bold">
                                <i class="fas fa-plus-circle me-2"></i>Tambah Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const qualityMap = {
        // Premium
        "Pandan Wangi": "Premium", "Rojolele": "Premium", "Menthik Wangi": "Premium", 
        "Menthik Susu": "Premium", "Beras Merah": "Premium", "Beras Hitam": "Premium", 
        "Beras Coklat": "Premium", "Beras Ketan Putih": "Premium", "Beras Ketan Hitam": "Premium",
        "Sintanur": "Premium", "Hibrida Sembada": "Premium", "Hibrida Mapa": "Premium",
        "Beras Basmati": "Premium", "Beras Japonica": "Premium", "Beras Jasmine": "Premium",
        "Beras Ketan": "Premium",
        
        // Medium
        "IR 64": "Medium", "IR 42": "Medium", "Cisadane": "Medium", "Ciherang": "Medium",
        "Inpari 32": "Medium", "Inpari 42": "Medium", "Batang Lembang": "Medium",
        "Ciliwung": "Medium", "Way Apo Buru": "Medium", "Memberamo": "Medium",
        "Inpara": "Medium", "Inpago": "Medium", "Beras Pecah Kulit": "Medium",
        
        // Standard
        "Beras Menir": "Standard"
    };

    function updateQuality() {
        const select = document.getElementById('jenis_beras');
        const qualityInput = document.getElementById('kualitas');
        const selectedVariety = select.value;
        
        if (selectedVariety && qualityMap[selectedVariety]) {
            qualityInput.value = qualityMap[selectedVariety];
        } else {
            qualityInput.value = '';
        }
    }
</script>
@endsection