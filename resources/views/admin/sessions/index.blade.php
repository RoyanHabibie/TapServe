@extends('layouts.admin')

@section('title', 'Sesi Aktif')

@section('content')
    <h3>Sesi Meja Aktif</h3>
    <table class="table table-striped" id="sessionsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipe</th>
                <th>Meja</th>
                <th>Status</th>
                <th>Pembayaran</th>
                <th>Dibuka</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sessions as $session)
                <tr>
                    <td>{{ $session->id }}</td>
                    <td>{{ $session->order_type == 'dine_in' ? 'Dine In' : 'Takeaway' }}</td>
                    <td>{{ $session->table->name ?? '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $session->status == 'open' ? 'success' : 'warning' }}">
                            {{ $session->status }}
                        </span>
                    </td>
                    <td>{{ $session->payment_mode }}</td>
                    <td>{{ $session->opened_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($session->status == 'open')
                            <a href="{{ route('admin.sessions.close', $session->id) }}"
                                class="btn btn-sm btn-warning">Tutup</a>
                        @endif
                        <a href="{{ route('admin.sessions.cancel', $session->id) }}" class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin?')">Batal</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#sessionsTable').DataTable();
        });
    </script>
@endpush
