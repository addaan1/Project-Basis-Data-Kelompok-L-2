@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-green text-white p-4">
                    <h4 class="mb-0 fw-bold font-poppins d-flex align-items-center">
                        <i class="bi bi-plus-circle-fill me-3 opacity-75"></i>Tambah Stok Inventaris
                    </h4>
                    <p class="mb-0 mt-2 text-white-50 small">Masukkan data hasil panen atau stok baru Anda ke gudang.</p>
                </div>
                <div class="card-body p-4 p-lg-5 bg-white">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-octagon-fill fs-4 me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Ada yang perlu diperbaiki:</h6>
                                    <ul class="mb-0 small ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Main Info Section -->
                        <h6 class="text-uppercase text-muted fw-bold small mb-4 letter-spacing-1">Informasi Produk</h6>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select bg-light border-0" id="jenis_beras" name="jenis_beras" required>
                                        <option value="" disabled selected>Pilih Jenis Beras</option>
                                        <option value="Setra Ramos (IR64)" {{ old('jenis_beras') == 'Setra Ramos (IR64)' ? 'selected' : '' }}>Setra Ramos (IR64)</option>
                                        <option value="Pandan Wangi" {{ old('jenis_beras') == 'Pandan Wangi' ? 'selected' : '' }}>Pandan Wangi</option>
                                        <option value="Rojolele" {{ old('jenis_beras') == 'Rojolele' ? 'selected' : '' }}>Rojolele</option>
                                        <option value="Menthik Wangi" {{ old('jenis_beras') == 'Menthik Wangi' ? 'selected' : '' }}>Menthik Wangi</option>
                                        <option value="IR 42 (Pera)" {{ old('jenis_beras') == 'IR 42 (Pera)' ? 'selected' : '' }}>IR 42 (Pera)</option>
                                        <option value="Beras Merah" {{ old('jenis_beras') == 'Beras Merah' ? 'selected' : '' }}>Beras Merah</option>
                                        <option value="Beras Hitam" {{ old('jenis_beras') == 'Beras Hitam' ? 'selected' : '' }}>Beras Hitam</option>
                                        <option value="Ketan Putih" {{ old('jenis_beras') == 'Ketan Putih' ? 'selected' : '' }}>Ketan Putih</option>
                                    </select>
                                    <label for="jenis_beras" class="text-secondary">Jenis Beras</label>
                                </div>
                                <div class="form-text ms-2"><i class="bi bi-info-circle me-1"></i>Pilih varietas beras yang valid.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select bg-light border-0" id="kualitas_display" disabled style="cursor: not-allowed; background-color: #e9ecef !important;">
                                        <option value="" disabled selected>Otomatis sesuai jenis</option>
                                        <option value="Premium">Premium (Bagus Sekali)</option>
                                        <option value="Medium">Medium (Standar)</option>
                                        <option value="Standard">Standard (Biasa)</option>
                                    </select>
                                    <label for="kualitas_display" class="text-secondary">Kualitas (Otomatis)</label>
                                    <!-- Hidden input to submit the value -->
                                    <input type="hidden" id="kualitas" name="kualitas" value="{{ old('kualitas') }}">
                                </div>
                                <div class="form-text ms-2"><i class="bi bi-lock-fill me-1"></i>Ditentukan otomatis dari jenis beras.</div>
                            </div>
                        </div>

                        <!-- Javascript for Auto-Quality -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const berasSelect = document.getElementById('jenis_beras');
                                const kualitasDisplay = document.getElementById('kualitas_display');
                                const kualitasInput = document.getElementById('kualitas');

                                const qualityMap = {
                                    'Pandan Wangi': 'Premium',
                                    'Rojolele': 'Premium',
                                    'Menthik Wangi': 'Premium',
                                    'Beras Merah': 'Premium',
                                    'Beras Hitam': 'Premium',
                                    'Setra Ramos (IR64)': 'Medium',
                                    'IR 42 (Pera)': 'Standard',
                                    'Ketan Putih': 'Standard'
                                };

                                function updateQuality() {
                                    const selectedRice = berasSelect.value;
                                    const quality = qualityMap[selectedRice] || '';
                                    
                                    if (quality) {
                                        kualitasDisplay.value = quality;
                                        kualitasInput.value = quality;
                                    } else {
                                        kualitasDisplay.value = '';
                                        kualitasInput.value = '';
                                    }
                                }

                                berasSelect.addEventListener('change', updateQuality);
                                
                                // Run on load if old value exists
                                if(berasSelect.value) updateQuality();
                            });
                        </script>

                        <!-- Quantity Section -->
                        <h6 class="text-uppercase text-muted fw-bold small mb-4 letter-spacing-1 border-top pt-4">Jumlah & Waktu</h6>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="jumlah" class="form-label fw-bold text-dark mb-1">Berat Total (Kg)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-speedometer2"></i></span>
                                    <input type="number" class="form-control bg-light border-0" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" min="1" placeholder="0" required>
                                    <span class="input-group-text bg-light border-0 fw-bold text-success">Kg</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="tanggal_masuk" class="form-label fw-bold text-dark mb-1">Tanggal Panen/Masuk</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" class="form-control bg-light border-0" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="mb-5">
                            <label for="keterangan" class="form-label fw-bold text-dark mb-1">Keterangan / Catatan (Opsional)</label>
                            <textarea class="form-control bg-light border-0" id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Hasil panen lahan utara, kondisi kering sempurna...">{{ old('keterangan') }}</textarea>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-end gap-3 border-top pt-4">
                            <a href="{{ route('inventory.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">Batal</a>
                            <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-lg hover-scale">
                                <i class="bi bi-save me-2"></i>Simpan ke Gudang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-poppins { font-family: 'Poppins', sans-serif; }
    .bg-gradient-green { background: linear-gradient(135deg, #1B5E20, #43A047); }
    .letter-spacing-1 { letter-spacing: 1px; }
    .form-control:focus, .form-select:focus { box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2); border-color: #4CAF50; background-color: #fff !important; }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-2px); }
</style>
@endsection