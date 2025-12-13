@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-header bg-gradient-green d-flex justify-content-between align-items-center p-4">
                    <h5 class="mb-0 fw-bold text-white font-poppins">
                        <i class="bi bi-chat-quote-fill me-2 text-warning"></i>Daftar Negosiasi
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-white text-success rounded-pill px-3 py-2 shadow-sm font-poppins">
                            <i class="bi bi-hash me-1"></i> Total: {{ $negotiations->count() }}
                        </span>
                        @if(auth()->user()->peran == 'pengepul')
                        <a href="{{ route('market.index') }}" class="btn btn-warning text-dark fw-bold btn-sm d-flex align-items-center rounded-pill px-3 shadow-hover">
                            <i class="bi bi-cart-plus me-2"></i>Pasar
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0"> 

                    @if ($negotiations->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-3 opacity-25">
                                <i class="bi bi-chat-text display-1 text-success"></i>
                            </div>
                            <h4 class="fw-bold text-secondary font-poppins">Belum ada negosiasi</h4>
                            <p class="text-muted">Mulai dengan menawarkan harga pada produk di pasar.</p>
                            @if(auth()->user()->peran == 'pengepul')
                                <a href="{{ route('market.index') }}" class="btn btn-success fw-bold px-4 rounded-pill mt-3 shadow-hover">
                                    <i class="bi bi-search me-2"></i>Cari Produk
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="bg-light small text-uppercase fw-bold text-secondary font-poppins">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">ID</th>
                                        <th class="py-3 border-0">Produk</th>
                                        <th class="py-3 border-0">Pihak Terkait</th>
                                        <th class="py-3 border-0">Harga Tawaran</th>
                                        <th class="py-3 border-0">Status</th>
                                        <th class="py-3 text-end pe-4 border-0">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($negotiations as $index => $negotiation)
                                    <tr class="negotiation-row border-bottom">
                                        <td class="ps-4 py-3 font-monospace small text-muted">#{{ $negotiation->id }}</td>
                                        <td class="py-3">
                                            @if($negotiation->produk)
                                                <span class="fw-bold d-block text-dark font-poppins">{{ $negotiation->produk->nama_produk }}</span>
                                                <small class="text-muted">{{ $negotiation->produk->jenis_beras }}</small>
                                            @else
                                                <span class="text-danger fst-italic">Produk dihapus</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-success text-white me-2 small d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; border-radius: 50%;">
                                                    <i class="bi bi-person-fill"></i>
                                                </div>
                                                <div>
                                                    @if(auth()->user()->peran == 'petani')
                                                        <span class="d-block text-dark fw-medium">{{ $negotiation->pengepul->nama ?? 'Unknown' }}</span>
                                                        <small class="text-muted" style="font-size: 0.75rem;">Pengepul</small>
                                                    @else
                                                        <span class="d-block text-dark fw-medium">{{ $negotiation->petani->nama ?? 'Unknown' }}</span>
                                                        <small class="text-muted" style="font-size: 0.75rem;">Petani</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="fw-bold text-success font-poppins">
                                                Rp {{ number_format($negotiation->harga_penawaran, 0, ',', '.') }}
                                            </span>
                                            <small class="d-block text-muted">/ {{ $negotiation->jumlah_kg }} kg</small>
                                        </td>
                                        <td class="py-3">
                                            @php
                                                $statusClass = 'bg-secondary';
                                                $statusIcon = 'bi-question-circle';
                                                $statusText = $negotiation->status;
                                                
                                                if($negotiation->status == 'diterima') { 
                                                    $statusClass = 'bg-success'; 
                                                    $statusIcon = 'bi-check-circle-fill'; 
                                                } elseif($negotiation->status == 'dalam_proses') { 
                                                    $statusClass = 'bg-warning text-dark'; 
                                                    $statusIcon = 'bi-hourglass-split'; 
                                                    $statusText = 'Menunggu';
                                                } elseif($negotiation->status == 'ditolak') { 
                                                    $statusClass = 'bg-danger'; 
                                                    $statusIcon = 'bi-x-circle-fill'; 
                                                }
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill py-2 px-3 shadow-sm font-poppins">
                                                <i class="bi {{ $statusIcon }} me-1"></i>
                                                {{ ucfirst($statusText) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-end pe-4 text-muted small">
                                            {{ $negotiation->created_at->format('d M Y') }}<br>
                                            {{ $negotiation->created_at->format('H:i') }}
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('negosiasi.show', $negotiation->id) }}" class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-hover">
                                                Detail <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if(method_exists($negotiations, 'links'))
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $negotiations->links() }}
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.05); }
    
    .negotiation-row:hover {
        background-color: rgba(255, 255, 255, 0.15) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .table-borderless > :not(caption) > * > * { border-bottom-width: 0; }
    .table > :not(caption) > * > * { background-color: transparent; color: white; }
    
    /* Pagination Override */
    .pagination .page-link { background: rgba(255,255,255,0.9); border: none; color: #198754; margin: 0 4px; border-radius: 8px; }
    .pagination .page-item.active .page-link { background: #ff9800; color: white; }
</style>
@endsection