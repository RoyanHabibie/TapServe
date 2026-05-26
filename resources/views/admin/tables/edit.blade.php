@extends('layouts.admin')

@section('title', 'Edit Meja')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>Edit Meja</h4>
            <form action="{{ route('admin.tables.update', $table->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Meja</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name', $table->name) }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="capacity" class="form-label">Kapasitas</label>
                    <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity"
                        name="capacity" value="{{ old('capacity', $table->capacity) }}" required min="1">
                    @error('capacity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="available" {{ old('status', $table->status) == 'available' ? 'selected' : '' }}>
                            Available</option>
                        <option value="occupied" {{ old('status', $table->status) == 'occupied' ? 'selected' : '' }}>
                            Occupied</option>
                        <option value="disabled" {{ old('status', $table->status) == 'disabled' ? 'selected' : '' }}>
                            Disabled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection
