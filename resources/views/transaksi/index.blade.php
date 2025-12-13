@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-receipt me-2"></i>Aktivitas Transaksi</h5>
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group">
                            <button type="button" class="btn btn-light btn-sm dropdown-toggle fw-bold text-success shadow-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('report.export.pdf') }}"><i class="bi bi-file-pdf me-2 text-danger"></i>Download PDF</a></li>
                                <li><a class="dropdown-item" href="{{ route('report.export.csv') }}"><i class="bi bi-file-earmark-spreadsheet me-2 text-success"></i>Download CSV</a></li>
                            </ul>
                        </div>
                        <span class="badge bg-white text-warning rounded-pill px-3 py-2 shadow-sm">
                            <i class="bi bi-file-earmark-text me-1"></i> Total: {{ $activities->total() ?? $activities->count() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Filter Section -->
                    <form action="{{ route('transaksi.index') }}" method="GET" class="mb-4">
                        <div class="row g-2 align-items-center">
                            <div class="col-lg-3 col-md-6">
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-0 text-success ps-3"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="start_date" class="form-control border-0" value="{{ request('start_date') }}" placeholder="Mulai Tanggal" style="background: rgba(255,255,255,0.9);">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-0 text-success ps-3"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="end_date" class="form-control border-0" value="{{ request('end_date') }}" placeholder="Sampai Tanggal" style="background: rgba(255,255,255,0.9);">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-0 text-success ps-3"><i class="bi bi-filter"></i></span>
                                    <select name="type" class="form-select border-0" style="background: rgba(255,255,255,0.9);">
                                        <option value="">Semua Tipe</option>
                                        <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Pembelian</option>
                                        <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Penjualan</option>
                                        <option value="topup" {{ request('type') == 'topup' ? 'selected' : '' }}>Top Up</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <input type="text" name="search" class="form-control border-0" placeholder="Cari..." value="{{ request('search') }}" style="background: rgba(255,255,255,0.9);">
                                    <button type="submit" class="btn btn-light text-success fw-bold"><i class="bi bi-search"></i></button>
                                    <a href="{{ route('transaksi.index') }}" class="btn btn-outline-light fw-bold bg-white text-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if ($activities->isEmpty())
                        <div class="text-center py-5 text-white">
                            <div class="mb-3 opacity-50">
                                <i class="bi bi-inbox display-1"></i>
                            </div>
                            <h4 class="fw-bold">Belum ada aktivitas transaksi</h4>
                            <p class="text-white-50">Transaksi Anda akan muncul di sini.</p>
                        </div>
                    @else
                        <div class="table-responsive rounded-3 bg-white/10 p-2">
                            <table class="table align-middle text-white mb-0 table-hover">
                                <thead class="text-white small text-uppercase" style="border-bottom: 1px solid rgba(255,255,255,0.3); background: rgba(0,0,0,0.1);">
                                    <tr>
                                        <th class="ps-3 py-3">Jenis</th>
                                        <th class="py-3">Keterangan</th>
                                        <th class="py-3">Jumlah</th>
                                        <th class="py-3">Tanggal</th>
                                        <th class="py-3">Status</th>
                                        <th class="text-end pe-3 py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($activities as $activity)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                        <td class="ps-3 py-3">
                                            @php
                                                $badgeClass = 'bg-secondary';
                                                $icon = 'bi-question-circle';
                                                switch($activity->type) {
                                                    case 'purchase': $badgeClass = 'bg-danger'; $icon = 'bi-cart-dash'; break;
                                                    case 'sale': $badgeClass = 'bg-success'; $icon = 'bi-cash-coin'; break;
                                                    case 'topup': $badgeClass = 'bg-primary'; $icon = 'bi-wallet2'; break;
                                                    case 'expenditure': $badgeClass = 'bg-warning text-dark'; $icon = 'bi-arrow-down-circle'; break;
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }} rounded-pill py-2 px-3 shadow-sm border border-light border-opacity-25">
                                                <i class="bi {{ $icon }} me-1"></i> {{ ucfirst($activity->type) }}
                                            </span>
                                        </td>
                                        <td class="py-3 fw-bold">{{ $activity->description }}</td>
                                        <td class="py-3">
                                            @if(in_array($activity->type, ['purchase', 'expenditure']))
                                                <span class="text-danger fw-bold bg-white rounded px-2 py-1 shadow-sm">-Rp {{ number_format($activity->amount, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-success fw-bold bg-white rounded px-2 py-1 shadow-sm">+Rp {{ number_format($activity->amount, 0, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-white">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar3 me-2 text-white-50"></i> {{ \Carbon\Carbon::parse($activity->date)->format('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge {{ $activity->status == 'confirmed' || $activity->status == 'completed' ? 'bg-success' : ($activity->status == 'pending' ? 'bg-warning text-dark' : 'bg-danger') }} shadow-sm">
                                                {{ ucfirst($activity->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-end pe-3">
                                            <form action="{{ route('transaksi.destroy', $activity->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus aktivitas ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light text-danger hover-scale rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if(method_exists($activities, 'links'))
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $activities->withQueryString()->links() }}
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
    .hover-scale:hover { transform: scale(1.1); }
    /* Override pagination styling to match theme if needed */
    .pagination .page-link { background: rgba(255,255,255,0.9); border: none; color: #28a745; margin: 0 4px; border-radius: 8px; }
    .pagination .page-item.active .page-link { background: #ff9800; color: white; }
    /* Table hover effect */
    .table-hover tbody tr:hover { background-color: rgba(255,255,255,0.1) !important; color: white !important; }
</style>
@endsection