@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Top-up Saldo</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('topup.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="amount">Jumlah Top-up (Rp)</label>
                            <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required autofocus>
                            @error('amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="payment_method">Metode Pembayaran</label>
                            <select id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" name="payment_method" required>
                                <option value="bank">Bank Transfer</option>
                                <option value="mini_market">Mini Market</option>
                            </select>
                            @error('payment_method')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="bukti_transfer">Bukti Transfer</label>
                            <input id="bukti_transfer" type="file" class="form-control @error('bukti_transfer') is-invalid @enderror" name="bukti_transfer" required>
                             @error('bukti_transfer')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                Lanjutkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection