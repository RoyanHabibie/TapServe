@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>Tambah Kategori</h4>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                        rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="sort_order" class="form-label">Urutan</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order"
                        value="{{ old('sort_order', 0) }}">
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection
