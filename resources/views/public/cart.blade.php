@extends('public.layout')

@section('title', 'Keranjang')

@section('content')
    <h3>Keranjang Belanja</h3>
    @if (empty($cart))
        <div class="alert alert-warning">Keranjang kosong. <a href="{{ route('public.menu', ['token' => $token]) }}">Lihat
                menu</a></div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="cartBody">
                    @foreach ($cart as $item)
                        <tr data-menu-id="{{ $item['menu_id'] }}">
                            <td>{{ $item['name'] }}</td>
                            <td>Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                            <td>
                                <div class="input-group input-group-sm" style="width: 120px;">
                                    <button class="btn btn-outline-secondary btn-minus" type="button">-</button>
                                    <input type="number" class="form-control text-center qty-input"
                                        value="{{ $item['quantity'] }}" min="1" readonly>
                                    <button class="btn btn-outline-secondary btn-plus" type="button">+</button>
                                </div>
                            </td>
                            <td class="subtotal">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger btn-remove"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td colspan="3" class="text-end fw-bold">Total</td>
                        <td colspan="2" class="fw-bold" id="cartTotal">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('public.menu', ['token' => $token]) }}" class="btn btn-secondary">Lanjut Belanja</a>
            <a href="{{ route('public.checkout', ['token' => $token]) }}" class="btn btn-success">Checkout</a>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Update cart quantity
            function updateCart(menuId, qty) {
                $.ajax({
                    url: '{{ route('public.cart.update', ['token' => $token]) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        menu_id: menuId,
                        quantity: qty
                    },
                    success: function(response) {
                        $('#cartTotal').text('Rp ' + response.total);
                        if (response.totalItems > 0) {
                            $('#cartCount').text(response.totalItems).show();
                        } else {
                            $('#cartCount').hide();
                        }
                        // Update subtotal baris
                        var row = $('tr[data-menu-id="' + menuId + '"]');
                        var price = parseFloat(row.find('.subtotal').text().replace(/[^0-9]/g, '')) /
                            parseInt(row.find('.qty-input').val());
                        var newSub = price * qty;
                        row.find('.subtotal').text('Rp ' + newSub.toLocaleString('id-ID'));
                    }
                });
            }

            $('.btn-minus').click(function() {
                var row = $(this).closest('tr');
                var input = row.find('.qty-input');
                var val = parseInt(input.val()) - 1;
                if (val < 1) return;
                input.val(val);
                updateCart(row.data('menu-id'), val);
            });

            $('.btn-plus').click(function() {
                var row = $(this).closest('tr');
                var input = row.find('.qty-input');
                var val = parseInt(input.val()) + 1;
                input.val(val);
                updateCart(row.data('menu-id'), val);
            });

            $('.btn-remove').click(function() {
                var row = $(this).closest('tr');
                if (confirm('Hapus item ini?')) {
                    $.ajax({
                        url: '{{ route('public.cart.remove', ['token' => $token]) }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            menu_id: row.data('menu-id')
                        },
                        success: function(response) {
                            row.remove();
                            $('#cartTotal').text('Rp ' + response.total);
                            if (response.totalItems > 0) {
                                $('#cartCount').text(response.totalItems).show();
                            } else {
                                $('#cartCount').hide();
                                location.reload();
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
