@extends('layouts.admin')

@section('title', 'Kelola User')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Kelola User</h3>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah User
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped" id="usersTable">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="badge bg-secondary ms-1">Saya</span>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php
                                $roleBadge = match($user->role) {
                                    'owner'   => 'dark',
                                    'admin'   => 'primary',
                                    'cashier' => 'success',
                                    'kitchen' => 'warning',
                                    default   => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $roleBadge }}">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#usersTable').DataTable({ order: [[0, 'asc']] });

            $(document).on('submit', '.delete-form', function (e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    title: 'Hapus user ini?',
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
