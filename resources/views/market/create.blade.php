@extends('layouts.main')

@section('content')
<div class="container py-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Glassmorphism Card -->
            <div class="card border-0 shadow-lg" style="border-radius: 20px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px);">
                
                <!-- Gradient Header (Green for Growth/Agriculture) -->
                <div class="card-header border-0 py-4 px-5 position-relative" style="background: linear-gradient(135deg, #2E7D32, #66BB6A); border-radius: 20px 20px 0 0;">
                    <div class="d-flex align-items-center justify-content-between position-relative z-1">
                        <div>
                            <h4 class="mb-1 fw-bold text-white"><i class="bi bi-basket2-fill me-2"></i>Jual Beras Baru</h4>
                            <p class="mb-0 text-white-50 small">Isi formulir di bawah untuk menawarkan hasil panen Anda.</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-25 rounded-circle text-white">
                            <i class="bi bi-shop-window" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    <form method="POST" action="{{ route('market.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Section: Info Penjual -->
                        <div class="mb-4 p-3 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-10 d-flex align-items-center">
                            <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-success">
                                <i class="bi bi-person-badge-fill fs-4"></i>
                            </div>
                            <div>
                                <small class="text-uppercase fw-bold text-success d-block" style="font-size: 0.75rem; letter-spacing: 1px;">Penjual</small>
                                <span class="fw-bold text-dark fs-5">{{ Auth::user()->name ?? Auth::user()->nama }}</span>
                            </div>
                        </div>

                        <!-- Main Form Grid -->
                        <div class="row g-4">
                            <!-- Left Column: Details -->
                            <div class="col-md-8">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control form-control-lg rounded-4 fw-bold text-dark fs-5" id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}" placeholder="Contoh: Beras Pandan Wangi Super" required style="border: 1px solid #ced4da;">
                                    <label for="nama_produk" class="text-secondary opacity-75 fw-bold"><i class="bi bi-tag-fill me-2 text-success fs-5"></i>Nama Produk</label>
                                    @error('nama_produk') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select form-select-lg rounded-4 fw-bold text-dark fs-5" id="jenis_beras" name="jenis_beras" required onchange="updateQuality()" style="border: 1px solid #ced4da;">
                                                <option value="" selected disabled>Pilih Jenis</option>
                                                <optgroup label="Premium">
                                                    <option value="Pandan Wangi">Pandan Wangi</option>
                                                    <option value="Rojolele">Rojolele</option>
                                                    <option value="Menthik Wangi">Menthik Wangi</option>
                                                    <option value="Beras Merah">Beras Merah</option>
                                                    <option value="Beras Hitam">Beras Hitam</option>
                                                </optgroup>
                                                <optgroup label="Medium">
                                                    <option value="IR 64">IR 64 (Setra Ramos)</option>
                                                    <option value="Ciherang">Ciherang</option>
                                                    <option value="Inpari 32">Inpari 32</option>
                                                </optgroup>
                                                <optgroup label="Lainnya">
                                                    <option value="Beras Ketan">Beras Ketan</option>
                                                    <option value="Beras Menir">Beras Menir</option>
                                                </optgroup>
                                            </select>
                                            <label for="jenis_beras" class="text-secondary opacity-75 fw-bold"><i class="bi bi-grid-fill me-2 text-success fs-5"></i>Jenis Beras</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control form-control-lg rounded-4 bg-light fs-5" id="kualitas" name="kualitas" value="{{ old('kualitas') }}" readonly placeholder="Otomatis" style="border: 1px solid #e9ecef;">
                                            <label for="kualitas" class="text-secondary opacity-75 fw-bold"><i class="bi bi-award-fill me-2 text-warning fs-5"></i>Kualitas</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-floating mb-3">
                                    <textarea class="form-control form-control-lg rounded-4 text-dark fs-5" placeholder="Jelaskan detail produk..." id="deskripsi" name="deskripsi" style="height: 160px; border: 1px solid #ced4da;" required>{{ old('deskripsi') }}</textarea>
                                    <label for="deskripsi" class="text-secondary opacity-75 fw-bold"><i class="bi bi-file-text-fill me-2 text-success fs-5"></i>Deskripsi Produk</label>
                                </div>
                            </div>

                            <!-- Right Column: Price & Photo -->
                            <div class="col-md-4">
                                <div class="p-4 rounded-4 bg-white h-100 shadow-sm border border-light">
                                    <h6 class="fw-bold text-success mb-3 small text-uppercase letter-spacing-1"><i class="bi bi-currency-dollar me-2"></i>Harga & Stok</h6>
                                    
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">Harga per Kg</label>
                                        <div class="input-group input-group-lg shadow-sm rounded-4">
                                            <span class="input-group-text bg-success text-white border-0 fw-bold fs-5">Rp</span>
                                            <input type="number" class="form-control border-0 bg-light fw-bold text-dark fs-5" name="harga" value="{{ old('harga') }}" required min="0" placeholder="0">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">Stok Tersedia</label>
                                        <div class="input-group input-group-lg shadow-sm rounded-4">
                                            <input type="number" class="form-control border-0 bg-light fw-bold text-dark fs-5" name="stok" value="{{ old('stok') }}" required min="0" placeholder="0">
                                            <span class="input-group-text bg-warning text-dark border-0 fw-bold fs-5">Kg</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary">Lokasi Gudang</label>
                                        <input type="text" class="form-control form-control-lg rounded-4 border-light bg-light text-dark fs-5" name="lokasi_gudang" value="{{ old('lokasi_gudang') }}" required placeholder="Kota/Kabupaten">
                                    </div>

                                    <hr class="my-4 border-secondary border-opacity-10">

                                    <label class="form-label small fw-bold mb-2 text-secondary"><i class="bi bi-camera-fill me-2"></i>Foto Produk</label>
                                    <div class="upload-box position-relative rounded-4 border-2 border-dashed border-success border-opacity-25 p-4 text-center bg-light transition-all hover-scale" onclick="document.getElementById('foto').click()" style="cursor: pointer;">
                                        <i class="bi bi-cloud-arrow-up-fill text-success fs-1 mb-2 opacity-50"></i>
                                        <p class="small text-muted mb-0 fw-medium">Upload Foto</p>
                                        <input type="file" id="foto" name="foto" class="d-none" accept="image/*" onchange="previewImage(this)">
                                        <img id="preview" src="" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover rounded-4 d-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end align-items-center gap-3 mt-5 border-top border-secondary border-opacity-10 pt-4">
                            <a href="{{ route('market.index') }}" class="btn btn-link text-decoration-none text-muted fw-bold">Batal</a>
                            
                            <!-- Gradient Orange Button for 'Jual' (Transaction action) -->
                            <button type="submit" class="btn btn-lg px-5 text-white fw-bold rounded-pill shadow-lg hover-scale" 
                                    style="background: linear-gradient(135deg, #FF9800, #F57C00); border: none;">
                                <i class="bi bi-check-circle-fill me-2"></i>Terbitkan Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('preview').classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

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