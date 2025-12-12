@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Edit Product</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product->id_produk) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Petani (Owner)</label>
                            <select name="id_petani" class="form-select @error('id_petani') is-invalid @enderror" required>
                                <option value="">Select Petani</option>
                                @foreach($petanis as $petani)
                                    <option value="{{ $petani->id_user }}" {{ old('id_petani', $product->id_petani) == $petani->id_user ? 'selected' : '' }}>{{ $petani->nama }}</option>
                                @endforeach
                            </select>
                            @error('id_petani') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror" value="{{ old('nama_produk', $product->nama_produk) }}" required>
                            @error('nama_produk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Varietas</label>
                                <input type="text" name="varietas" class="form-control @error('varietas') is-invalid @enderror" value="{{ old('varietas', $product->jenis_beras) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kualitas</label>
                                <select name="kualitas" class="form-select @error('kualitas') is-invalid @enderror" required>
                                    <option value="">Select Quality</option>
                                    <option value="Premium" {{ old('kualitas', $product->kualitas) == 'Premium' ? 'selected' : '' }}>Premium</option>
                                    <option value="Medium" {{ old('kualitas', $product->kualitas) == 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="Standard" {{ old('kualitas', $product->kualitas) == 'Standard' ? 'selected' : '' }}>Standard</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price per Kg (Rp)</label>
                                <input type="number" name="harga_per_kg" class="form-control @error('harga_per_kg') is-invalid @enderror" value="{{ old('harga_per_kg', $product->harga) }}" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock (Kg)</label>
                                <input type="number" name="stok_kg" class="form-control @error('stok_kg') is-invalid @enderror" value="{{ old('stok_kg', $product->stok) }}" min="1" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Warehouse Location</label>
                            <input type="text" name="lokasi_gudang" class="form-control @error('lokasi_gudang') is-invalid @enderror" value="{{ old('lokasi_gudang', $product->lokasi_gudang) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3">{{ old('deskripsi', $product->deskripsi) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            @if($product->foto)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $product->foto) }}" alt="Current Image" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Leave blank to keep current image</small>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-light">Back</a>
                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
