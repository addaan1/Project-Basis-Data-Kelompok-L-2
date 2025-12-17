@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Riwayat Top-up</span>
                    <a href="{{ route('topup.create') }}" class="btn btn-primary btn-sm">Top-up Baru</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($topUps->isEmpty())
                        <div class="alert alert-info">
                            Anda belum memiliki riwayat top-up.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kode Referensi</th>
                                        <th>Jumlah</th>
                                        <th>Metode</th>
                                        <th>Status</th>
                                        <th>Bukti</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topUps as $topUp)
                                        <tr>
                                            <td>{{ $topUp->created_at->format('d M Y H:i') }}</td>
                                            <td>{{ $topUp->reference_code }}</td>
                                            <td>Rp {{ number_format($topUp->amount, 0, ',', '.') }}</td>
                                            <td>{{ $topUp->payment_method == 'bank' ? 'Bank Transfer' : 'Mini Market' }}</td>
                                            <td>
                                                @if($topUp->bukti_transfer)
                                                    <a href="{{ asset('storage/' . $topUp->bukti_transfer) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($topUp->status == 'pending')
                                                    <span class="badge bg-warning">Menunggu Verifikasi</span>
                                                @elseif($topUp->status == 'completed')
                                                    <span class="badge bg-success">Selesai</span>
                                                @else
                                                    <span class="badge bg-danger">Gagal</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('topup.show', $topUp->id) }}" class="btn btn-sm btn-info">Detail</a>
                                                @if(auth()->user()->peran === 'admin' && $topUp->status == 'pending')
                                                    <form action="{{ route('topup.confirm', $topUp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Verifikasi Top Up ini?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection