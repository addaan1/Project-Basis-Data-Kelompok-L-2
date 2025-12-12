@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Edit User</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user->id_user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $user->nama) }}" required>
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password (Leave blank to keep current)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="peran" class="form-select @error('peran') is-invalid @enderror" required>
                                <option value="admin" {{ $user->peran == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="petani" {{ $user->peran == 'petani' ? 'selected' : '' }}>Petani</option>
                                <option value="pengepul" {{ $user->peran == 'pengepul' ? 'selected' : '' }}>Pengepul</option>
                                <option value="distributor" {{ $user->peran == 'distributor' ? 'selected' : '' }}>Distributor</option>
                            </select>
                            @error('peran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light">Back</a>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
