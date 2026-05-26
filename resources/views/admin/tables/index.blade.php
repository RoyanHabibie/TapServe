@extends('layouts.admin')

@section('title', 'Meja')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Daftar Meja</h3>
        <a href="{{ route('admin.tables.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Meja</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="tablesTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                        <th>Token (QR)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tables as $index => $table)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $table->name }}</td>
                            <td>{{ $table->capacity }} orang</td>
                            <td>
                                <span
                                    class="badge bg-{{ $table->status == 'available' ? 'success' : ($table->status == 'occupied' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($table->status) }}
                                </span>
                            </td>
                            <td><small class="text-monospace">{{ substr($table->token, 0, 20) }}...</small></td>
                            <td>
                                <a href="{{ route('admin.tables.edit', $table->id) }}" class="btn btn-sm btn-warning"><i
                                        class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST"
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
            $('#tablesTable').DataTable();
            $('.delete-form').on('submit', function(e) {
                if (!confirm('Yakin ingin menghapus?')) e.preventDefault();
            });
        });
    </script>
@endpush
