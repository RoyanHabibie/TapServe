@extends('layouts.admin')

@section('title', 'Meja')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-700 mb-1" style="font-weight:700;">Daftar Meja</h4>
            <p class="text-muted small mb-0">{{ $tables->count() }} meja terdaftar</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.tables.qrcodes') }}" class="btn btn-outline-secondary" target="_blank">
                <i class="bi bi-qr-code me-1"></i>Cetak Semua QR
            </a>
            <a href="{{ route('admin.tables.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Meja
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Meja</th>
                            <th>Kapasitas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tables as $index => $table)
                            @php
                                $statusBg = match($table->status) {
                                    'available' => ['#dcfce7','#166534'],
                                    'occupied'  => ['#fef3c7','#92400e'],
                                    default     => ['#f1f5f9','#475569'],
                                };
                                $statusLabel = match($table->status) {
                                    'available' => 'Tersedia',
                                    'occupied'  => 'Terisi',
                                    default     => 'Nonaktif',
                                };
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="fw-600" style="font-weight:600;">{{ $table->name }}</td>
                                <td>{{ $table->capacity }} orang</td>
                                <td>
                                    <span class="badge rounded-pill"
                                        style="background:{{ $statusBg[0] }};color:{{ $statusBg[1] }};">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.tables.qrcode', $table->id) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Cetak QR Meja" target="_blank">
                                            <i class="bi bi-qr-code"></i>
                                        </a>
                                        <a href="{{ route('admin.tables.edit', $table->id) }}"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.tables.destroy', $table->id) }}"
                                            method="POST" class="d-inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#tablesTable').DataTable({ order: [[0, 'asc']] });

        $(document).on('submit', '.delete-form', function (e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Hapus meja ini?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
        });
    });
</script>
@endpush
