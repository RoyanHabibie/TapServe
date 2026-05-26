@extends('layouts.admin')

@section('title', 'Menu')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Daftar Menu</h3>
        <a href="{{ route('admin.menus.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Menu</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="menusTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($menus as $index => $menu)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if ($menu->image)
                                    <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}"
                                        width="60">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $menu->name }}</td>
                            <td>{{ $menu->category->name ?? '-' }}</td>
                            <td>Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $menu->is_available ? 'success' : 'secondary' }}">
                                    {{ $menu->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.menus.edit', $menu->id) }}" class="btn btn-sm btn-warning"><i
                                        class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST"
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
            $('#menusTable').DataTable();
            $('.delete-form').on('submit', function(e) {
                if (!confirm('Yakin ingin menghapus?')) e.preventDefault();
            });
        });
    </script>
@endpush
