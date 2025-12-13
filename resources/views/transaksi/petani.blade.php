@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white">
                        <i class="bi bi-receipt me-2"></i>
                        {{ (auth()->user()->peran ?? '') === 'petani' ? 'Aktivitas Penjualan (Petani)' : 'Aktivitas Transaksi (Pembeli)' }}
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-white text-warning rounded-pill px-3 py-2 shadow-sm">
                            <i class="bi bi-file-earmark-text me-1"></i> Total: {{ $transactions->total() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Filter Section -->
                    <form method="GET" class="mb-4">
                        <div class="row g-2 align-items-center">
                            <div class="col-lg-3 col-md-6">
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-0 text-success ps-3"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="from" class="form-control border-0" value="{{ request('from') }}" placeholder="Dari tanggal" style="background: rgba(255,255,255,0.9);">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-0 text-success ps-3"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="to" class="form-control border-0" value="{{ request('to') }}" placeholder="Sampai tanggal" style="background: rgba(255,255,255,0.9);">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-0 text-success ps-3"><i class="bi bi-filter"></i></span>
                                    <select name="status" class="form-select border-0" style="background: rgba(255,255,255,0.9);">
                                        <option value="">Semua Status</option>
                                        @foreach(['negosiasi','dalam_proses','menunggu_pembayaran','disetujui','ditolak','completed'] as $st)
                                            <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <input type="text" name="produk" class="form-control border-0" placeholder="Cari produk..." value="{{ request('produk') }}" style="background: rgba(255,255,255,0.9);">
                                    <button type="submit" class="btn btn-light text-success fw-bold"><i class="bi bi-search"></i></button>
                                    <a href="{{ route('transaksi.index') }}" class="btn btn-outline-light fw-bold bg-white text-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if ($transactions->isEmpty())
                        <div class="text-center py-5 text-white">
                            <div class="mb-3 opacity-50">
                                <i class="bi bi-inbox display-1"></i>
                            </div>
                            <h4 class="fw-bold">Belum ada transaksi penjualan</h4>
                            <p class="text-white-50">Transaksi akan muncul setelah ada pembeli atau negosiasi berjalan.</p>
                        </div>
                    @else
                        <div class="table-responsive rounded-3 p-3" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(5px);">
                            <table class="table align-middle text-white mb-0 table-borderless table-hover">
                                <thead class="small text-uppercase fw-bold" style="border-bottom: 2px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.8);">
                                    <tr>
                                        <th class="ps-3 py-3">Produk</th>
                                        <th class="py-3">Harga</th>
                                        <th class="py-3">Kuantitas</th>
                                        <th class="py-3">Pembeli</th>
                                        <th class="py-3">Tanggal</th>
                                        <th class="py-3">Status</th>
                                        <th class="text-end pe-3 py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $trx)
                                    <tr class="transaction-row" style="border-bottom: 1px solid rgba(255,255,255,0.1); transition: all 0.2s;">
                                        <td class="ps-3 py-3 fw-bold">{{ $trx->produk }}</td>
                                        <td class="py-3 text-white">
                                            <span class="bg-white text-success rounded px-2 py-1 shadow-sm fw-bold">
                                                Rp {{ number_format($trx->harga, 0, ',', '.') }} <small class="text-muted fw-normal">/ kg</small>
                                            </span>
                                        </td>
                                        <td class="py-3 fw-bold">{{ number_format($trx->jumlah) }} kg</td>
                                        <td class="py-3">
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">{{ $trx->pembeli ?? '-' }}</span>
                                                <small class="text-white-50" style="font-size: 0.75rem;">{{ $trx->pembeli_kontak ?? '' }}</small>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center text-white-50">
                                                <i class="bi bi-calendar3 me-2"></i> {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @php
                                                $statusClass = 'bg-secondary';
                                                $statusIcon = 'bi-question-circle';
                                                
                                                if($trx->status=='disetujui' || $trx->status=='completed') { $statusClass = 'bg-success'; $statusIcon = 'bi-check-circle'; }
                                                elseif($trx->status=='dalam_proses' || $trx->status=='menunggu_pembayaran' || $trx->status=='negosiasi') { $statusClass = 'bg-warning text-dark'; $statusIcon = 'bi-hourglass-split'; }
                                                elseif($trx->status=='ditolak') { $statusClass = 'bg-danger'; $statusIcon = 'bi-x-circle'; }
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill py-2 px-3 shadow-sm border border-light border-opacity-25">
                                                <i class="bi {{ $statusIcon }} me-1"></i>
                                                {{ ['menunggu_pembayaran'=>'Menunggu','pending'=>'Menunggu'][$trx->status] ?? ucfirst(str_replace('_',' ', $trx->status)) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-end pe-3">
                                            <div class="d-flex justify-content-end gap-2">
                                                <form method="POST" action="{{ route('transaksi.approve', $trx->id) }}" onsubmit="return confirm('Setujui transaksi ini?')">
                                                    @csrf
                                                    <button class="btn btn-light text-success hover-scale rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Approve">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('transaksi.reject', $trx->id) }}" onsubmit="return confirm('Tolak transaksi ini?')">
                                                    @csrf
                                                    <button class="btn btn-light text-danger hover-scale rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Reject">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </form>
                                                <button class="btn btn-light text-primary hover-scale rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="openHistory({{ $trx->id }})" title="Riwayat">
                                                    <i class="bi bi-clock-history"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($transactions, 'links'))
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $transactions->withQueryString()->links() }}
                        </div>
                        @endif
                    @endif
                    
                    <div class="mt-4 border-top border-white border-opacity-25 pt-4">
                        <h5 class="text-white mb-3 fw-bold"><i class="bi bi-bell me-2"></i>Notifikasi</h5>
                        <ul id="notifList" class="list-group list-group-flush rounded-3 overflow-hidden"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-clock-history me-2"></i>Riwayat Status Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                <ul id="historyList" class="list-group list-group-flush rounded shadow-sm"></ul>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.1); }
    
    .transaction-row:hover {
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

<script>
    async function loadNotifications(){
        try{
            const res = await fetch('{{ route('transaksi.notifications') }}', { headers: { 'X-Requested-With':'XMLHttpRequest' } });
            if(!res.ok) return;
            const items = await res.json();
            const list = document.getElementById('notifList');
            if(items.length === 0) {
                list.innerHTML = '<li class="list-group-item bg-transparent text-white-50 text-center py-3">Tidak ada notifikasi baru</li>';
            } else {
                list.innerHTML = items.map(i => `
                    <li class="list-group-item bg-white bg-opacity-10 border-bottom border-white border-opacity-10 text-white d-flex justify-content-between align-items-center">
                        <span>${i.message}</span>
                        <small class="text-white-50 ms-2" style="font-size: 0.75rem;">${new Date(i.created_at).toLocaleString('id-ID')}</small>
                    </li>
                `).join('');
            }
        }catch(e){ /* ignore */ }
    }
    setInterval(loadNotifications, 15000);
    loadNotifications();

    async function openHistory(id){
        try{
            const res = await fetch(`{{ url('/app/transaksi') }}/${id}/history`, { headers: { 'X-Requested-With':'XMLHttpRequest' } });
            if(!res.ok) return;
            const items = await res.json();
            const list = document.getElementById('historyList');
            list.innerHTML = items.map(i => {
                if(i.kind==='status'){
                    // Use bootstrap icons for arrows
                    return `<li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-secondary rounded-pill me-2">${i.status_before ?? '-'}</span>
                            <i class="bi bi-arrow-right text-muted mx-1"></i>
                            <span class="badge bg-primary rounded-pill ms-2">${i.status_after}</span>
                        </div>
                        <small class="text-muted">${new Date(i.created_at).toLocaleString('id-ID')}</small>
                    </li>`;
                }
                return `<li class="list-group-item">
                    <span class="fw-bold text-success">Tawaran: Rp ${new Intl.NumberFormat('id-ID').format(Math.round(i.offer))} / kg</span>
                    <small class="text-muted ms-2 float-end">${new Date(i.created_at).toLocaleString('id-ID')}</small>
                </li>`;
            }).join('');
            new bootstrap.Modal(document.getElementById('historyModal')).show();
        }catch(e){/* ignore */}
    }
</script>
@endsection