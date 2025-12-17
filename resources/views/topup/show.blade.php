@extends('layouts.main')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <!-- Header with Gradient -->
                <div class="card-header text-white py-4" style="background: linear-gradient(135deg, #FF9800, #FFB74D);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-receipt fs-3 me-3"></i>
                            <div>
                                <h3 class="mb-0 fw-bold">Detail Top-up</h3>
                                <small class="text-white-50">ID: {{ $topUp->id ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <a href="{{ route('topup.index') }}" class="btn btn-light btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body p-4" style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.95), rgba(129, 199, 132, 0.95));">
                    <!-- Status Alert -->
                    @if($topUp->status == 'completed')
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-2 me-3"></i>
                                <div>
                                    <h5 class="mb-1 fw-bold">Top-up Berhasil!</h5>
                                    <p class="mb-0">Saldo telah ditambahkan ke akun Anda.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($topUp->status == 'pending')
                        <div class="alert alert-info border-0 shadow-sm mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock-history fs-2 me-3"></i>
                                <div>
                                    <h5 class="mb-1 fw-bold">Menunggu Pembayaran</h5>
                                    <p class="mb-0">Silakan lakukan pembayaran dengan detail berikut.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-x-circle-fill fs-2 me-3"></i>
                                <div>
                                    <h5 class="mb-1 fw-bold">Top-up Gagal</h5>
                                    <p class="mb-0">Silakan hubungi admin untuk informasi lebih lanjut.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Detail Cards -->
                    <div class="row g-3 mb-4">
                        <!-- Reference Code -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                                <div class="card-body">
                                    <h6 class="text-white-50 mb-2 text-uppercase small fw-bold">
                                        <i class="bi bi-hash me-1"></i> Kode Referensi
                                    </h6>
                                    <div class="bg-white rounded p-3 text-center mb-2">
                                        <h3 class="mb-0 fw-bold font-monospace" style="color: #000000 !important;">
                                            {{ $topUp->reference_code ?? '-' }}
                                        </h3>
                                    </div>
                                    <small class="text-white-50 d-block text-center">
                                        Gunakan kode ini saat pembayaran
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                                <div class="card-body">
                                    <h6 class="text-white-50 mb-2 text-uppercase small fw-bold">
                                        <i class="bi bi-cash-stack me-1"></i> Jumlah
                                    </h6>
                                    <h2 class="mb-1 fw-bold text-white">
                                        Rp {{ number_format($topUp->amount ?? 0, 0, ',', '.') }}
                                    </h2>
                                    <small class="text-white-50">
                                        Jumlah yang diminta
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                                <div class="card-body">
                                    <h6 class="text-white-50 mb-2 text-uppercase small fw-bold">
                                        <i class="bi bi-credit-card me-1"></i> Metode Pembayaran
                                    </h6>
                                    <h4 class="mb-0 fw-bold text-white">
                                        {{ ($topUp->payment_method ?? 'N/A') == 'bank' ? 'Bank Transfer' : 'Mini Market' }}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                                <div class="card-body">
                                    <h6 class="text-white-50 mb-2 text-uppercase small fw-bold">
                                        <i class="bi bi-info-circle me-1"></i> Status
                                    </h6>
                                    <div>
                                        @if(($topUp->status ?? '') == 'pending')
                                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                                <i class="bi bi-clock me-1"></i> Menunggu Pembayaran
                                            </span>
                                        @elseif(($topUp->status ?? '') == 'completed')
                                            <span class="badge bg-success fs-6 px-3 py-2">
                                                <i class="bi bi-check-lg me-1"></i> Berhasil
                                            </span>
                                        @else
                                            <span class="badge bg-danger fs-6 px-3 py-2">
                                                <i class="bi bi-x-lg me-1"></i> Gagal
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timestamp -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                                <div class="card-body">
                                    <h6 class="text-white-50 mb-2 text-uppercase small fw-bold">
                                        <i class="bi bi-calendar-event me-1"></i> Waktu Transaksi
                                    </h6>
                                    <p class="mb-0 text-white">
                                        {{ $topUp->created_at ? $topUp->created_at->format('d M Y, H:i') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    @if(($topUp->status ?? '') == 'pending')
                        <form action="{{ route('topup.confirm', $topUp->id) }}" method="POST" class="d-grid mb-3">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-lg fw-bold shadow" onclick="return confirm('Sudah melakukan pembayaran?')">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                Konfirmasi Pembayaran
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-monospace {
        font-family: 'Courier New', Courier, monospace;
        letter-spacing: 0.1em;
    }
</style>
@endsection