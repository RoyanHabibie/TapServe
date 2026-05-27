@extends('layouts.admin')

@section('title', 'Dapur - Pesanan Masuk')

@section('content')
    <h3>Pesanan Masuk</h3>

    <div class="row" id="orderCards">
        <!-- Diisi oleh polling -->
    </div>

@endsection

@push('scripts')
    <script>
        function loadKitchenOrders() {
            $.get('/kitchen/orders-ajax', function(orders) {
                var html = '';
                if (orders.length === 0) {
                    html = '<div class="col-12"><div class="alert alert-info">Belum ada pesanan.</div></div>';
                } else {
                    orders.forEach(function(order) {
                        var status = order.status;
                        var borderClass = status == 'pending' ? 'danger' : (status == 'processing' ?
                            'warning' : 'success');
                        var badgeClass = status == 'pending' ? 'danger' : (status == 'processing' ?
                            'warning' : 'success');
                        var buttons = '';
                        if (status == 'pending') {
                            buttons =
                                `
                            <button class="btn btn-sm btn-warning update-status" data-id="${order.id}" data-status="processing">Proses</button>
                            <button class="btn btn-sm btn-danger update-status" data-id="${order.id}" data-status="cancelled">Tolak</button>`;
                        } else if (status == 'processing') {
                            buttons =
                                `<button class="btn btn-sm btn-success update-status" data-id="${order.id}" data-status="ready">Siap</button>`;
                        } else if (status == 'ready') {
                            buttons =
                                `<button class="btn btn-sm btn-primary update-status" data-id="${order.id}" data-status="completed">Selesai</button>`;
                        }
                        var itemsHtml = order.items.map(item => {
                            return `<li class="list-group-item d-flex justify-content-between">
                                    <span>${item.name} x${item.quantity}</span>
                                    ${item.notes ? '<small class="text-muted">'+item.notes+'</small>' : ''}
                                </li>`;
                        }).join('');

                        html += `
                    <div class="col-md-4 mb-3" id="order-${order.id}">
                        <div class="card border-${borderClass}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>#${order.order_number}</strong>
                                <span class="badge bg-${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">${order.table}</small>
                                <ul class="list-group list-group-flush mt-2">${itemsHtml}</ul>
                                ${order.notes ? `<p class="mt-2 mb-0"><small>Catatan: ${order.notes}</small></p>` : ''}
                            </div>
                            <div class="card-footer">${buttons}</div>
                        </div>
                    </div>`;
                    });
                }
                $('#orderCards').html(html);
            });
        }

        // Polling setiap 5 detik
        setInterval(loadKitchenOrders, 5000);
        loadKitchenOrders();

        // Event delegation untuk tombol update status
        $(document).on('click', '.update-status', function() {
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
                        loadKitchenOrders(); // refresh list
                    }
                },
                error: function(xhr) {
                    alert('Gagal: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan.'));
                    btn.prop('disabled', false).html(status == 'processing' ? 'Proses' : (status ==
                        'ready' ? 'Siap' : (status == 'completed' ? 'Selesai' : 'Tolak')));
                }
            });
        });
    </script>
@endpush
