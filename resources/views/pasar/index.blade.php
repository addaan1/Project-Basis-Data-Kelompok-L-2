@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Pasar Digital Beras</h1>
        @if(auth()->user()->peran === 'petani')
            <a href="{{ route('pasar.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                Tambah Produk
            </a>
        @endif
    </div>

    <!-- Filter dan Pencarian -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form action="{{ route('pasar.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                    placeholder="Nama produk, varietas, dll">
            </div>
            
            <div>
                <label for="varietas" class="block text-sm font-medium text-gray-700 mb-1">Varietas</label>
                <input type="text" name="varietas" id="varietas" value="{{ request('varietas') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                    placeholder="Varietas beras">
            </div>
            
            <div>
                <label for="kualitas" class="block text-sm font-medium text-gray-700 mb-1">Kualitas</label>
                <input type="text" name="kualitas" id="kualitas" value="{{ request('kualitas') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                    placeholder="Kualitas beras">
            </div>
            
            <div>
                <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                <input type="text" name="lokasi" id="lokasi" value="{{ request('lokasi') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                    placeholder="Lokasi gudang">
            </div>
            
            <div>
                <label for="harga_min" class="block text-sm font-medium text-gray-700 mb-1">Harga Min (Rp)</label>
                <input type="number" name="harga_min" id="harga_min" value="{{ request('harga_min') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                    placeholder="Harga minimum">
            </div>
            
            <div>
                <label for="harga_max" class="block text-sm font-medium text-gray-700 mb-1">Harga Max (Rp)</label>
                <input type="number" name="harga_max" id="harga_max" value="{{ request('harga_max') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                    placeholder="Harga maksimum">
            </div>
            
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    Filter
                </button>
                <a href="{{ route('pasar.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Daftar Produk -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($produk as $item)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="h-48 bg-gray-200 overflow-hidden">
                    @if($item->foto)
                        <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama_produk }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-100">
                            <span class="text-gray-400">Tidak ada foto</span>
                        </div>
                    @endif
                </div>
                
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2">{{ $item->nama_produk }}</h2>
                    
                    <div class="mb-2">
                        <span class="text-sm text-gray-600">Varietas:</span>
                        <span class="font-medium">{{ $item->varietas }}</span>
                    </div>
                    
                    <div class="mb-2">
                        <span class="text-sm text-gray-600">Kualitas:</span>
                        <span class="font-medium">{{ $item->kualitas }}</span>
                    </div>
                    
                    <div class="mb-2">
                        <span class="text-sm text-gray-600">Harga:</span>
                        <span class="font-medium">Rp {{ number_format($item->harga_per_kg, 0, ',', '.') }}/kg</span>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('pasar.show', $item) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded inline-block">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-6 rounded-lg shadow text-center">
                <p class="text-gray-500">Tidak ada produk yang tersedia.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $produk->appends(request()->query())->links() }}
    </div>
</div>
@endsection