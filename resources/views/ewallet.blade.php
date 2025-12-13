@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-wallet2 me-2"></i>Pengaturan E-Wallet</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4 text-white">
                        <div class="bg-white rounded-circle d-inline-flex p-3 mb-3 text-success shadow-sm">
                            <i class="bi bi-credit-card-2-front display-4"></i>
                        </div>
                        <h4 class="fw-bold">Rekening Bank Terhubung</h4>
                        <p class="text-white-50">Kelola akun bank untuk penarikan dana Anda.</p>
                    </div>

                    <form class="mt-4" action="{{ route('ewallet.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-white fw-bold">Nama Bank</label>
                            <select name="bank_name" class="form-select border-0 shadow-sm" style="background: rgba(255,255,255,0.9);">
                                <option disabled {{ !$user->bank_name ? 'selected' : '' }}>Pilih Bank...</option>
                                <option value="bri" {{ $user->bank_name == 'bri' ? 'selected' : '' }}>BRI</option>
                                <option value="bca" {{ $user->bank_name == 'bca' ? 'selected' : '' }}>BCA</option>
                                <option value="mandiri" {{ $user->bank_name == 'mandiri' ? 'selected' : '' }}>Mandiri</option>
                                <option value="bni" {{ $user->bank_name == 'bni' ? 'selected' : '' }}>BNI</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white fw-bold">Nomor Rekening</label>
                            <input name="account_number" value="{{ $user->account_number }}" type="number" class="form-control border-0 shadow-sm" placeholder="Contoh: 1234xxxxxx" style="background: rgba(255,255,255,0.9);">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-white fw-bold">Atas Nama</label>
                            <input name="account_name" value="{{ $user->account_name }}" type="text" class="form-control border-0 shadow-sm" placeholder="Nama Pemilik Rekening" style="background: rgba(255,255,255,0.9);">
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-light text-success fw-bold px-4 rounded-pill shadow-sm">
                                <i class="bi bi-save me-1"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Riwayat Penarikan</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-5 text-white-50">
                        <i class="bi bi-exclamation-circle fs-1 mb-2"></i>
                        <p>Belum ada riwayat penarikan dana.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection