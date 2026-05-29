@extends('public.layout')

@section('title', 'Keranjang')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('public.menu', ['token' => $token]) }}"
            class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-700" style="font-weight:700;">Keranjang Belanja</h5>
    </div>

    @if (empty($cart))
        <div class="text-center py-5">
            <i class="bi bi-cart-x" style="font-size:4rem;color:#cbd5e1;"></i>
            <p class="mt-3 text-muted">Keranjang kamu masih kosong.</p>
            <a href="{{ route('public.menu', ['token' => $token]) }}"
                class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i>Lihat Menu
            </a>
        </div>
    @else

        {{-- ── Bulk order-type toggle ── --}}
        <div class="card mb-3 shadow-none" style="border-radius:12px;border:1px solid #e2e8f0;">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small" style="flex-shrink:0;">Atur semua:</span>
                    <div class="d-flex gap-2 flex-grow-1">
                        <button id="bulkDineIn" onclick="setAllType('dine_in')"
                            class="btn btn-sm flex-fill bulk-btn"
                            style="border-radius:8px;font-size:.8rem;font-weight:600;
                                   border:2px solid #3b82f6;background:#eff6ff;color:#1d4ed8;">
                            <i class="bi bi-shop me-1"></i>Semua Dine In
                        </button>
                        <button id="bulkTakeaway" onclick="setAllType('takeaway')"
                            class="btn btn-sm flex-fill bulk-btn"
                            style="border-radius:8px;font-size:.8rem;font-weight:600;
                                   border:2px solid #e2e8f0;background:#fff;color:#64748b;">
                            <i class="bi bi-bag me-1"></i>Semua Bawa Pulang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Cart items ── --}}
        <div class="d-flex flex-column gap-3 mb-3" id="cartBody">
            @foreach ($cart as $item)
                @php $ot = $item['order_type'] ?? 'dine_in'; @endphp
                <div class="card shadow-none" data-menu-id="{{ $item['menu_id'] }}"
                    data-order-type="{{ $ot }}"
                    style="border-radius:12px;border:2px solid {{ $ot === 'takeaway' ? '#f59e0b' : '#e2e8f0' }};
                           transition:border-color .2s;">
                    <div class="card-body py-3 px-3">

                        {{-- Name + remove --}}
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="flex-grow-1">
                                <div class="fw-600" style="font-weight:600;font-size:.95rem;">{{ $item['name'] }}</div>
                                <div class="text-muted small">
                                    Rp {{ number_format($item['price'], 0, ',', '.') }} / pcs
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-danger btn-remove rounded-circle"
                                style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-trash" style="font-size:.8rem;"></i>
                            </button>
                        </div>

                        {{-- Qty + subtotal --}}
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="d-flex align-items-center gap-1 qty-group">
                                <button class="btn btn-outline-secondary btn-minus"
                                    style="width:30px;height:30px;padding:0;border-radius:8px;font-size:.9rem;">−</button>
                                <input type="number" class="form-control text-center qty-input"
                                    value="{{ $item['quantity'] }}" min="1" readonly
                                    style="width:48px;height:30px;padding:0;font-size:.9rem;border-radius:8px;font-weight:600;">
                                <button class="btn btn-outline-secondary btn-plus"
                                    style="width:30px;height:30px;padding:0;border-radius:8px;font-size:.9rem;">+</button>
                            </div>
                            <span class="price-tag fw-700 subtotal" style="font-size:.95rem;">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Per-item order-type toggle --}}
                        <div class="d-flex gap-2 mt-3 item-type-row">
                            <button class="type-btn flex-fill btn btn-sm {{ $ot === 'dine_in' ? 'type-active-dine' : 'type-inactive' }}"
                                data-type="dine_in"
                                style="border-radius:8px;font-size:.78rem;font-weight:600;padding:.3rem .5rem;">
                                <i class="bi bi-shop me-1"></i>Dine In
                            </button>
                            <button class="type-btn flex-fill btn btn-sm {{ $ot === 'takeaway' ? 'type-active-away' : 'type-inactive' }}"
                                data-type="takeaway"
                                style="border-radius:8px;font-size:.78rem;font-weight:600;padding:.3rem .5rem;">
                                <i class="bi bi-bag me-1"></i>Bawa Pulang
                            </button>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Legend ── --}}
        <div class="d-flex gap-3 mb-3 px-1">
            <div class="d-flex align-items-center gap-1" style="font-size:.75rem;color:#64748b;">
                <span style="display:inline-block;width:10px;height:10px;border-radius:3px;border:2px solid #e2e8f0;"></span>
                Dine In
            </div>
            <div class="d-flex align-items-center gap-1" style="font-size:.75rem;color:#64748b;">
                <span style="display:inline-block;width:10px;height:10px;border-radius:3px;border:2px solid #f59e0b;"></span>
                Bawa Pulang
            </div>
        </div>

        {{-- ── Total ── --}}
        <div class="card mb-4"
            style="border-radius:12px;background:var(--ts-primary-light,#fff3ef);border:1px solid var(--ts-primary-border,#fbc4b0);">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <span class="fw-600" style="font-weight:600;color:#7c2d12;">Total</span>
                <span class="price-tag fw-700" style="font-size:1.1rem;" id="cartTotal">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <div class="d-grid gap-2">
            <a href="{{ route('public.checkout', ['token' => $token]) }}"
                class="btn btn-primary btn-lg rounded-pill">
                <i class="bi bi-bag-check me-2"></i>Lanjut ke Checkout
            </a>
            <a href="{{ route('public.menu', ['token' => $token]) }}"
                class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-plus-circle me-1"></i>Tambah Menu Lain
            </a>
        </div>
    @endif

@endsection

@push('styles')
<style>
    .type-active-dine {
        background: #eff6ff;
        color: #1d4ed8;
        border: 2px solid #3b82f6;
    }
    .type-active-away {
        background: #fffbeb;
        color: #92400e;
        border: 2px solid #f59e0b;
    }
    .type-inactive {
        background: #fff;
        color: #94a3b8;
        border: 2px solid #e2e8f0;
    }
    .type-btn { transition: background .15s, color .15s, border-color .15s; }
</style>
@endpush

@push('scripts')
<script>
    var itemTypeUrl = '{{ route('public.cart.item-type', ['token' => $token]) }}';
    var csrf = '{{ csrf_token() }}';

    function fmtRp(num) {
        return 'Rp ' + num.toLocaleString('id-ID');
    }

    /* ── Per-item order type ── */
    function applyItemType(card, type) {
        card.attr('data-order-type', type);
        // Border
        card.css('border-color', type === 'takeaway' ? '#f59e0b' : '#e2e8f0');
        // Toggle buttons
        card.find('.type-btn').each(function () {
            var t = $(this).data('type');
            $(this).removeClass('type-active-dine type-active-away type-inactive');
            if (t === type) {
                $(this).addClass(type === 'dine_in' ? 'type-active-dine' : 'type-active-away');
            } else {
                $(this).addClass('type-inactive');
            }
        });
    }

    $(document).on('click', '.type-btn', function () {
        var card    = $(this).closest('[data-menu-id]');
        var menuId  = card.data('menu-id');
        var type    = $(this).data('type');
        if (card.attr('data-order-type') === type) return; // no change

        applyItemType(card, type);

        $.post(itemTypeUrl, { _token: csrf, menu_id: menuId, order_type: type });
        syncBulkState();
    });

    /* ── Bulk set all ── */
    function setAllType(type) {
        $('[data-menu-id]').each(function () { applyItemType($(this), type); });
        $.post(itemTypeUrl, { _token: csrf, all: 1, order_type: type });
        syncBulkState();
    }

    function syncBulkState() {
        var types = $('[data-menu-id]').map(function () {
            return $(this).attr('data-order-type');
        }).get();
        var allDine    = types.every(function (t) { return t === 'dine_in'; });
        var allTakeaway = types.every(function (t) { return t === 'takeaway'; });

        $('#bulkDineIn').css({
            background: allDine ? '#eff6ff' : '#fff',
            color: allDine ? '#1d4ed8' : '#64748b',
            borderColor: allDine ? '#3b82f6' : '#e2e8f0',
        });
        $('#bulkTakeaway').css({
            background: allTakeaway ? '#fffbeb' : '#fff',
            color: allTakeaway ? '#92400e' : '#64748b',
            borderColor: allTakeaway ? '#f59e0b' : '#e2e8f0',
        });
    }

    /* ── Qty controls ── */
    function updateCart(menuId, qty) {
        $.ajax({
            url: '{{ route('public.cart.update', ['token' => $token]) }}',
            method: 'POST',
            data: { _token: csrf, menu_id: menuId, quantity: qty },
            success: function (response) {
                $('#cartTotal').text('Rp ' + response.total);
                var row = $('[data-menu-id="' + menuId + '"]');
                var priceText = row.find('.text-muted.small').text().replace(/[^0-9]/g, '');
                row.find('.subtotal').text(fmtRp(parseInt(priceText) * qty));
                if (response.totalItems > 0) {
                    $('#cartCount').text(response.totalItems).show();
                } else {
                    $('#cartCount').hide();
                }
            }
        });
    }

    $(document).on('click', '.btn-minus', function () {
        var row = $(this).closest('[data-menu-id]');
        var input = row.find('.qty-input');
        var val = parseInt(input.val()) - 1;
        if (val < 1) return;
        input.val(val);
        updateCart(row.data('menu-id'), val);
    });

    $(document).on('click', '.btn-plus', function () {
        var row = $(this).closest('[data-menu-id]');
        var input = row.find('.qty-input');
        var val = parseInt(input.val()) + 1;
        input.val(val);
        updateCart(row.data('menu-id'), val);
    });

    /* ── Remove item ── */
    $(document).on('click', '.btn-remove', function () {
        var row = $(this).closest('[data-menu-id]');
        Swal.fire({
            title: 'Hapus item ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e8410a',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('public.cart.remove', ['token' => $token]) }}',
                    method: 'POST',
                    data: { _token: csrf, menu_id: row.data('menu-id') },
                    success: function (response) {
                        row.fadeOut(200, function () {
                            $(this).remove();
                            syncBulkState();
                        });
                        $('#cartTotal').text('Rp ' + response.total);
                        if (response.totalItems > 0) {
                            $('#cartCount').text(response.totalItems).show();
                        } else {
                            $('#cartCount').hide();
                            setTimeout(function () { location.reload(); }, 250);
                        }
                    }
                });
            }
        });
    });

    /* ── Init bulk state on load ── */
    $(function () { syncBulkState(); });
</script>
@endpush
