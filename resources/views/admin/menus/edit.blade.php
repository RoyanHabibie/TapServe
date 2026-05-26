@extends('layouts.admin')

@section('title', 'Edit Menu')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>Edit Menu</h4>
            <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                        name="category_id">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id', $menu->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Menu</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name', $menu->name) }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Harga</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control @error('price') is-invalid @enderror" id="price"
                            name="price" value="{{ old('price', $menu->price) }}" step="0.01" min="0">
                    </div>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                        rows="3">{{ old('description', $menu->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Gambar</label>
                    @if ($menu->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" width="100">
                        </div>
                    @endif
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                        name="image" accept="image/*">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                </div>
                <div class="mb-3">
                    <label for="sort_order" class="form-label">Urutan</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order"
                        value="{{ old('sort_order', $menu->sort_order) }}">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_available" name="is_available" value="1"
                        {{ old('is_available', $menu->is_available) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_available">Tersedia</label>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection
