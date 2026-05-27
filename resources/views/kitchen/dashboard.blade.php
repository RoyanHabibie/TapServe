@extends('layouts.admin')

@section('title', 'Dapur — Pesanan Masuk')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-700 mb-1" style="font-weight:700;">Pesanan Masuk</h4>
            <p class="text-muted small mb-0">Update otomatis setiap 5 detik</p>
        </div>
        <span class="badge rounded-pill px-3 py-2"
            style="background:#f1f5f9;color:#475569;font-size:.8rem;">
            <i class="bi bi-circle-fill me-1" style="font-size:.45rem;color:#22c55e;vertical-align:middle;"></i>Live
        </span>
    </div>

    <div class="row g-3" id="orderCards">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Memuat pesanan...
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function buildStatusStyle(status) {
        var styles = {
            pending:    { bg: '#fee2e2', color: '#991b1b', border: '#f87171' },
            processing: { bg: '#fef3c7', color: '#92400e', border: '#fbbf24' },
            ready:      { bg: '#dcfce7', color: '#166534', border: '#4ade80' },
        };
        return styles[status] || { bg: '#f1f5f9', color: '#475569', border: '#cbd5e1' };
    }

    function buildLabel(status) {
        var map = { pending: 'Menunggu', processing: 'Dimasak', ready: 'Siap', completed: 'Selesai', cancelled: 'Dibatalkan' };
        return map[status] || status.charAt(0).toUpperCase() + status.slice(1);
    }

    function loadKitchenOrders() {
        $.get('/kitchen/orders-ajax', function (orders) {
            var html = '';
            if (orders.length === 0) {
                html = '<div class="col-12"><div class="card"><div class="card-body text-center py-5 text-muted">' +
                    '<i class="bi bi-inbox" style="font-size:3rem;color:#cbd5e1;"></i>' +
                    '<p class="mt-2 mb-0">Belum ada pesanan masuk.</p>' +
                    '</div></div></div>';
            } else {
                orders.forEach(function (order) {
                    var s = buildStatusStyle(order.status);
                    var label = buildLabel(order.status);
                    var buttons = '';
                    if (order.status === 'pending') {
                        buttons = `
                            <button class="btn btn-sm update-status flex-grow-1"
                                style="background:#fef3c7;color:#92400e;border:1px solid #fbbf24;"
                                data-id="${order.id}" data-status="processing">
                                <i class="bi bi-fire me-1"></i>Proses
                            </button>
                            <button class="btn btn-sm update-status"
                                style="background:#fee2e2;color:#991b1b;border:1px solid #f87171;width:36px;padding:0;"
                                data-id="${order.id}" data-status="cancelled"
                                title="Tolak">
                                <i class="bi bi-x-lg"></i>
                            </button>`;
                    } else if (order.status === 'processing') {
                        buttons = `
                            <button class="btn btn-sm update-status w-100"
                                style="background:#dcfce7;color:#166534;border:1px solid #4ade80;"
                                data-id="${order.id}" data-status="ready">
                                <i class="bi bi-bell me-1"></i>Tandai Siap
                            </button>`;
                    } else if (order.status === 'ready') {
                        buttons = `
                            <button class="btn btn-sm update-status w-100"
                                style="background:#dbeafe;color:#1e40af;border:1px solid #60a5fa;"
                                data-id="${order.id}" data-status="completed">
                                <i class="bi bi-check2-all me-1"></i>Selesai
                            </button>`;
                    }

                    var itemsHtml = order.items.map(function (item) {
                        return `<div class="d-flex justify-content-between align-items-center py-2"
                                    style="border-bottom:1px solid #f8fafc;">
                                    <span style="font-size:.85rem;font-weight:500;">${item.name}
                                        <span class="text-muted" style="font-weight:400;">×${item.quantity}</span>
                                    </span>
                                    ${item.notes ? '<span class="badge" style="background:#f1f5f9;color:#64748b;font-weight:400;font-size:.72rem;">' + item.notes + '</span>' : ''}
                                </div>`;
                    }).join('');

                    html += `
                        <div class="col-12 col-md-6 col-xl-4" id="order-${order.id}">
                            <div class="card h-100" style="border-top:3px solid ${s.border};">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 px-4">
                                    <div>
                                        <div class="fw-600" style="font-weight:600;font-size:.875rem;">#${order.order_number}</div>
                                        <div class="text-muted" style="font-size:.75rem;">${order.table}</div>
                                    </div>
                                    <span class="badge rounded-pill px-3"
                                        style="background:${s.bg};color:${s.color};font-size:.75rem;">
                                        ${label}
                                    </span>
                                </div>
                                <div class="card-body px-4 py-2">
                                    ${itemsHtml}
                                    ${order.notes ? '<div class="mt-2 pt-1 text-muted" style="font-size:.78rem;"><i class="bi bi-chat-left-text me-1"></i>' + order.notes + '</div>' : ''}
                                </div>
                                ${buttons ? '<div class="card-footer bg-white px-4 py-3" style="border-top:1px solid #f1f5f9;"><div class="d-flex gap-2">' + buttons + '</div></div>' : ''}
                            </div>
                        </div>`;
                });
            }
            $('#orderCards').html(html);
        });
    }

    setInterval(loadKitchenOrders, 5000);
    loadKitchenOrders();

    $(document).on('click', '.update-status', function () {
        var orderId = $(this).data('id');
        var status  = $(this).data('status');
        var btn     = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '/kitchen/order/' + orderId + '/status',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', status: status },
            success: function (response) {
                if (response.success) loadKitchenOrders();
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON?.message || 'Terjadi kesalahan.', timer: 2000, showConfirmButton: false });
                btn.prop('disabled', false);
                loadKitchenOrders();
            }
        });
    });
</script>
@endpush
