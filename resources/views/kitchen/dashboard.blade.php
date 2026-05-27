@extends('layouts.admin')

@section('title', 'Dapur - Pesanan Masuk')

@section('content')
    <h3>Pesanan Masuk</h3>

    <div class="row" id="orderCards">
        @forelse($orders as $order)
            <div class="col-md-4 mb-3" id="order-{{ $order->id }}">
                <div
                    class="card border-{{ $order->status == 'pending' ? 'danger' : ($order->status == 'processing' ? 'warning' : ($order->status == 'ready' ? 'success' : 'secondary')) }}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>#{{ $order->order_number }}</strong>
                        <span
                            class="badge bg-{{ $order->status == 'pending' ? 'danger' : ($order->status == 'processing' ? 'warning' : ($order->status == 'ready' ? 'success' : 'secondary')) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            @if ($order->session->table)
                                Meja: {{ $order->session->table->name }}
                            @else
                                Takeaway
                            @endif
                        </small>
                        <ul class="list-group list-group-flush mt-2">
                            @foreach ($order->orderItems as $item)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ $item->menu->name }} x{{ $item->quantity }}</span>
                                    @if ($item->notes)
                                        <small class="text-muted">{{ $item->notes }}</small>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        @if ($order->notes)
                            <p class="mt-2 mb-0"><small>Catatan: {{ $order->notes }}</small></p>
                        @endif
                    </div>
                    <div class="card-footer">
                        @if ($order->status == 'pending')
                            <button class="btn btn-sm btn-warning update-status" data-id="{{ $order->id }}"
                                data-status="processing">Proses</button>
                            <button class="btn btn-sm btn-danger update-status" data-id="{{ $order->id }}"
                                data-status="cancelled">Tolak</button>
                        @elseif($order->status == 'processing')
                            <button class="btn btn-sm btn-success update-status" data-id="{{ $order->id }}"
                                data-status="ready">Siap</button>
                        @elseif($order->status == 'ready')
                            <button class="btn btn-sm btn-primary update-status" data-id="{{ $order->id }}"
                                data-status="completed">Selesai</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Belum ada pesanan.</div>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.update-status').click(function() {
                var orderId = $(this).data('id');
                var status = $(this).data('status');
                var btn = $(this);

                btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

                $.ajax({
                    url: '/kitchen/order/' + orderId + '/status',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            // Hapus kartu jika completed/cancelled, atau reload untuk tampilan sederhana
                            if (status == 'completed' || status == 'cancelled') {
                                $('#order-' + orderId).fadeOut();
                            } else {
                                location
                            .reload(); // sederhana, reload halaman untuk update warna
                            }
                        }
                    },
                    error: function(xhr) {
                        alert('Gagal: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan.'));
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(status == 'processing' ? 'Proses' : (
                            status == 'ready' ? 'Siap' : (status == 'completed' ?
                                'Selesai' : 'Tolak')));
                    }
                });
            });
        });
    </script>
@endpush
