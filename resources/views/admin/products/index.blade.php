@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Products</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Add New Product</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Petani</th>
                            <th>Price/Kg</th>
                            <th>Stock</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->foto)
                                    <img src="{{ asset('storage/' . $product->foto) }}" alt="{{ $product->nama_produk }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $product->nama_produk }}</div>
                                <small class="text-muted">{{ $product->jenis_beras }} - {{ $product->kualitas }}</small>
                            </td>
                            <td>{{ $product->petani->nama ?? $product->nama_petani ?? 'Unknown' }}</td>
                            <td>Rp {{ number_format($product->harga, 0, ',', '.') }}</td>
                            <td>{{ $product->stok }} Kg</td>
                            <td>{{ $product->lokasi_gudang }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.products.edit', $product->id_produk) }}" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.products.destroy', $product->id_produk) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No products found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
