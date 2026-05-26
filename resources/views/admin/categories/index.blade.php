@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Daftar Kategori</h3>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah
            Kategori</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="categoriesTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Urutan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $index => $category)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->description }}</td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                    class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                    class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i
                                            class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#categoriesTable').DataTable();
            $('.delete-form').on('submit', function(e) {
                if (!confirm('Yakin ingin menghapus?')) e.preventDefault();
            });
        });
    </script>
@endpush
