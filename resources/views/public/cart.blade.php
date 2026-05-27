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
        {{-- ── Cart items (card per row on mobile) ── --}}
        <div class="d-flex flex-column gap-3 mb-3" id="cartBody">
            @foreach ($cart as $item)
                <div class="card shadow-none border" style="border-radius:12px;" data-menu-id="{{ $item['menu_id'] }}">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="flex-grow-1">
                                <div class="fw-600" style="font-weight:600;font-size:.95rem;">{{ $item['name'] }}</div>
                                <div class="text-muted small">
                                    Rp {{ number_format($item['price'], 0, ',', '.') }} / pcs
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-danger btn-remove rounded-circle"
                                style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-trash" style="font-size:.8rem;"></i>
                            </button>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            {{-- Quantity control --}}
                            <div class="d-flex align-items-center gap-1 qty-group">
                                <button class="btn btn-outline-secondary btn-minus"
                                    style="width:30px;height:30px;padding:0;border-radius:8px;font-size:.9rem;">−</button>
                                <input type="number" class="form-control text-center qty-input"
                                    value="{{ $item['quantity'] }}" min="1" readonly
                                    style="width:48px;height:30px;padding:0;font-size:.9rem;border-radius:8px;font-weight:600;">
                                <button class="btn btn-outline-secondary btn-plus"
                                    style="width:30px;height:30px;padding:0;border-radius:8px;font-size:.9rem;">+</button>
                            </div>
                            {{-- Subtotal --}}
                            <span class="price-tag fw-700 subtotal" style="font-size:.95rem;">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Total ── --}}
        <div class="card mb-4" style="border-radius:12px;background:var(--ts-primary-light,#fff3ef);border:1px solid var(--ts-primary-border,#fbc4b0);">
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

@push('scripts')
<script>
    $(document).ready(function () {

        function fmtRp(num) {
            return 'Rp ' + num.toLocaleString('id-ID');
        }

        function updateCart(menuId, qty) {
            $.ajax({
                url: '{{ route('public.cart.update', ['token' => $token]) }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', menu_id: menuId, quantity: qty },
                success: function (response) {
                    $('#cartTotal').text('Rp ' + response.total);
                    var row = $('[data-menu-id="' + menuId + '"]');
                    var priceText = row.find('.text-muted.small').text().replace(/[^0-9]/g, '');
                    var price = parseInt(priceText);
                    row.find('.subtotal').text(fmtRp(price * qty));
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
                        data: { _token: '{{ csrf_token() }}', menu_id: row.data('menu-id') },
                        success: function (response) {
                            row.fadeOut(200, function () { $(this).remove(); });
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
    });
</script>
@endpush
