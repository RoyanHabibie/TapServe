@extends('layouts.admin')

@section('title', 'Tambah Meja')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>Tambah Meja</h4>
            <form action="{{ route('admin.tables.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Meja</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="capacity" class="form-label">Kapasitas</label>
                    <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity"
                        name="capacity" value="{{ old('capacity', 2) }}" required min="1">
                    @error('capacity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>
                            Available</option>
                        <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="disabled" {{ old('status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection
