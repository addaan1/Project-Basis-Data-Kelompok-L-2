@extends('layouts.main')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark font-poppins mb-1">
                <i class="bi bi-box-seam-fill text-success me-2"></i>Gudang Inventaris
            </h1>
            <p class="text-muted small">Kelola stok hasil panen Anda sebelum dijual ke pasar.</p>
        </div>
        <a href="{{ route('inventory.create') }}" class="btn btn-warning rounded-pill px-4 shadow-hover fw-bold text-dark">
            <i class="bi bi-plus-lg me-2"></i>Tambah Stok
        </a>
    </div>

    <!-- Inventory Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm glass-card text-white bg-gradient-green h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white-50 small text-uppercase fw-bold">Total Berat Stok</p>
                            <h2 class="fw-bold mb-0">{{ number_format($inventories->sum('jumlah'), 0, ',', '.') }} <small class="fs-6">Kg</small></h2>
                        </div>
                        <i class="bi bi-scale fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm glass-card h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted small text-uppercase fw-bold">Varian Beras</p>
                            <h2 class="fw-bold text-dark mb-0">{{ $inventories->unique('jenis_beras')->count() }} <small class="fs-6 text-muted">Jenis</small></h2>
                        </div>
                        <div class="bg-light rounded-circle p-3 text-success">
                            <i class="bi bi-tags-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm glass-card h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted small text-uppercase fw-bold">Estimasi Nilai</p>
                            <h2 class="fw-bold text-dark mb-0">
                                <!-- Dummy estimation logic, ideally from market price -->
                                Rp {{ number_format($inventories->sum('jumlah') * 12000, 0, ',', '.') }}
                            </h2>
                            <small class="text-xs text-muted">*Estimasi Rp 12.000/kg</small>
                        </div>
                        <div class="bg-light rounded-circle p-3 text-warning">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory List -->
    <div class="card border-0 shadow rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small fw-bold text-secondary font-poppins">
                        <tr>
                            <th class="ps-4 py-3 border-0">Jenis Beras</th>
                            <th class="py-3 border-0">Kualitas</th>
                            <th class="py-3 border-0">Stok (Kg)</th>
                            <th class="py-3 border-0">Tgl Masuk</th>
                            <th class="py-3 border-0">Keterangan</th>
                            <th class="py-3 border-0 text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $item)
                        <tr class="border-bottom">
                            <td class="ps-4 py-3">
                                <span class="fw-bold text-dark d-block">{{ $item->jenis_beras }}</span>
                            </td>
                            <td class="py-3">
                                @php
                                    $badgeColor = match($item->kualitas) {
                                        'Premium' => 'bg-info bg-opacity-10 text-info',
                                        'Medium' => 'bg-success bg-opacity-10 text-success',
                                        default => 'bg-secondary bg-opacity-10 text-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }} rounded-pill px-3 py-2 fw-bold">
                                    {{ $item->kualitas }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="fw-bold text-dark fs-5">{{ number_format($item->jumlah, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-3 text-muted small">
                                {{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d M Y') }}
                            </td>
                            <td class="py-3 text-muted small fst-italic">
                                {{ Str::limit($item->keterangan, 30) ?: '-' }}
                            </td>
                            <td class="py-3 text-end pe-4">
                                <form action="{{ route('inventory.destroy', $item->id_inventory) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle shadow-hover" onclick="return confirm('Yakin ingin menghapus stok ini?')" style="width: 32px; height: 32px; padding: 0;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="mb-3 opacity-25">
                                    <i class="bi bi-box-seam display-1 text-secondary"></i>
                                </div>
                                <h5 class="text-muted fw-bold">Gudang Kosong</h5>
                                <p class="text-muted small">Belum ada stok beras yang tercatat.</p>
                                <a href="{{ route('inventory.create') }}" class="btn btn-sm btn-outline-success rounded-pill px-4 mt-2">
                                    Tambah Stok Baru
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .font-poppins { font-family: 'Poppins', sans-serif; }
    .bg-gradient-green { background: linear-gradient(135deg, #1B5E20, #4CAF50); }
    .glass-card { backdrop-filter: blur(10px); }
    .shadow-hover { transition: all 0.2s; }
    .shadow-hover:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>
@endsection