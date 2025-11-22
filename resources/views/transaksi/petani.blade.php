@extends('layouts.main')

@section('content')
<div class="activity-card animate-fade-in">
    <div class="card-header-custom d-flex align-items-center justify-content-between mb-4">
        <h3 class="text-white mb-0 font-weight-bold">
            <i class="fas fa-seedling me-2"></i>Aktivitas Penjualan (Petani)
        </h3>
        <div class="stats-summary d-flex gap-3">
            <span class="badge bg-light text-dark px-3 py-2">
                <i class="fas fa-chart-bar me-1"></i>Total: {{ $transactions->total() }}
            </span>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control" placeholder="Dari tanggal">
            </div>
            <div class="col-md-3">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control" placeholder="Sampai tanggal">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach(['negosiasi','dalam_proses','menunggu_pembayaran','disetujui','ditolak','completed'] as $st)
                        <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="produk" value="{{ request('produk') }}" class="form-control" placeholder="Cari produk/ket.">
            </div>
            <div class="col-md-12 d-flex justify-content-end gap-2">
                <button class="btn btn-outline-light">Filter</button>
                <a href="{{ route('transaksi.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>

        @if ($transactions->isEmpty())
            <div class="empty-state text-center py-5">
                <i class="fas fa-inbox fs-1 text-light mb-3"></i>
                <h5 class="text-white mb-2">Belum ada transaksi penjualan</h5>
                <p class="text-light">Transaksi akan muncul setelah ada pembeli atau negosiasi berjalan.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th class="text-white">Produk</th>
                            <th class="text-white">Harga</th>
                            <th class="text-white">Kuantitas</th>
                            <th class="text-white">Pembeli</th>
                            <th class="text-white">Tanggal</th>
                            <th class="text-white">Status</th>
                            <th class="text-white">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $trx)
                            <tr>
                                <td class="text-light">{{ $trx->produk }}</td>
                                <td class="text-success">Rp {{ number_format($trx->harga, 0, ',', '.') }} / kg</td>
                                <td class="text-light">{{ number_format($trx->jumlah) }} kg</td>
                                <td class="text-light">
                                    <div>{{ $trx->pembeli ?? '-' }}</div>
                                    <small class="text-muted">{{ $trx->pembeli_kontak ?? '' }}</small>
                                </td>
                                <td class="text-light">{{ \Carbon\Carbon::parse($trx->tanggal)->format('d-m-Y') }}</td>
                                <td>
                                    <span class="status-badge
                                        @if($trx->status=='disetujui' || $trx->status=='completed') badge-success
                                        @elseif($trx->status=='dalam_proses' || $trx->status=='menunggu_pembayaran' || $trx->status=='negosiasi') badge-warning
                                        @elseif($trx->status=='ditolak') badge-danger
                                        @else badge-secondary @endif">
                                        {{ ucfirst(str_replace('_',' ', $trx->status)) }}
                                    </span>
                                </td>
                                <td class="text-light">
                                    <div class="d-flex gap-2">
                                        <form method="POST" action="{{ route('transaksi.approve', $trx->id) }}" onsubmit="return confirm('Setujui transaksi ini?')">
                                            @csrf
                                            <button class="btn btn-success btn-sm" title="Approve">✓</button>
                                        </form>
                                        <form method="POST" action="{{ route('transaksi.reject', $trx->id) }}" onsubmit="return confirm('Tolak transaksi ini?')">
                                            @csrf
                                            <button class="btn btn-danger btn-sm" title="Reject">✗</button>
                                        </form>
                                        <button class="btn btn-secondary btn-sm" onclick="openHistory({{ $trx->id }})" title="Riwayat">Riwayat</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(method_exists($transactions, 'links'))
                <div class="pagination-wrapper mt-4">
                    {{ $transactions->withQueryString()->links() }}
                </div>
            @endif
        @endif

        <div class="mt-4">
            <h5 class="text-white mb-2"><i class="fas fa-bell me-2"></i>Notifikasi</h5>
            <ul id="notifList" class="list-group"></ul>
        </div>
    </div>
</div>
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Status Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul id="historyList" class="list-group"></ul>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    .activity-card { background: linear-gradient(135deg, #4CAF50, #81C784); backdrop-filter: blur(10px); border: 1px solid #FF9800; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); overflow: hidden; color: #fff; }
    .activity-card::before { content: ''; position: absolute; top:0; left:0; right:0; height:4px; background: linear-gradient(90deg,#FF9800,#4CAF50);} 
    .table-custom thead th, .table-custom tbody td { padding: 12px 8px; font-size: 0.9rem; }
    .status-badge { padding: 6px 12px; border-radius: 20px; font-weight: 600; border:1px solid rgba(255,255,255,0.3);} 
    .badge-success { background: rgba(40,167,69,.3); border-color:#28a745; }
    .badge-warning { background: rgba(255,193,7,.3); border-color:#ffc107; }
    .badge-danger { background: rgba(220,53,69,.3); border-color:#dc3545; }
    @media (max-width: 768px){ .table-responsive{font-size:.9rem;} .card-header-custom{flex-direction:column; gap:.5rem;} }
</style>

<script>
    async function loadNotifications(){
        try{
            const res = await fetch('{{ route('transaksi.notifications') }}', { headers: { 'X-Requested-With':'XMLHttpRequest' } });
            if(!res.ok) return;
            const items = await res.json();
            const list = document.getElementById('notifList');
            list.innerHTML = items.map(i => `<li class="list-group-item bg-white/20 border-white/30 text-white">${i.message} <small class="text-light ms-2">${new Date(i.created_at).toLocaleString('id-ID')}</small></li>`).join('');
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
                    return `<li class="list-group-item">${i.status_before ?? '-'} → ${i.status_after} <small class="text-muted ms-2">${new Date(i.created_at).toLocaleString('id-ID')}</small></li>`;
                }
                return `<li class="list-group-item">Tawaran: Rp ${new Intl.NumberFormat('id-ID').format(Math.round(i.offer))} / kg <small class="text-muted ms-2">${new Date(i.created_at).toLocaleString('id-ID')}</small></li>`;
            }).join('');
            new bootstrap.Modal(document.getElementById('historyModal')).show();
        }catch(e){/* ignore */}
    }
</script>