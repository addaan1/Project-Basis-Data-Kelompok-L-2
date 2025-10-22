@extends('layouts.guest')

@section('content')
<div class="container" style="margin-top: 10vh;">
    <div class="row">
        <div class="col-md-7 text-white">
            <!-- Header - Sesuaikan teks -->
            <h1 class="display-4 fw-bold text-black">Jual Panen Lebih <span style="color: #8BC34A;">Mudah</span>, Raih Keuntungan <span style="color: #8BC34A;">Maksimal</span>
        </h1>
            <p class="lead my-4 text-black">
            Kami bantu Anda menemukan pembeli yang tepat dan mengelola transaksi dengan mudah, sehingga Anda bisa fokus pada panen.
        </p>
            
            <!-- Tombol-->
            <div class="d-flex">
                <a href="{{ route('register') }}" class="btn btn-success btn-lg me-3" style="background-color: #4CAF50; border-color: #4CAF50; border-radius: 20px; padding: 10px 30px;">Daftar Sekarang!</a>
                <a href="{{ route('how-it-works') }}" class="btn btn-warning btn-lg" style="background-color: #FFC107; border-color: #FFC107; color: #333; border-radius: 20px; padding: 10px 30px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-play-circle-fill me-2" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814l-3.5-2.5z"/>
                    </svg>
                    How It Works yaa
                </a>
            </div>
        </div>
    </div>
</div>
@endsection