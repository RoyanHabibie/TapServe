@extends('public.layout')

@section('title', 'Menu')

@section('content')
    @if ($token)
        <div class="alert alert-info">
            Meja: Scan QR berhasil. Silakan pilih menu.
        </div>
    @else
        <div class="alert alert-info">
            Takeaway - Pesan untuk dibawa pulang.
        </div>
    @endif

    @foreach ($categories as $category)
        <h4 class="mt-4">{{ $category->name }}</h4>
        <div class="row">
            @foreach ($category->menus as $menu)
                <div class="col-md-4 col-6 mb-3">
                    <div class="card menu-item h-100">
                        @if ($menu->image)
                            <img src="{{ asset('storage/' . $menu->image) }}" class="card-img-top" alt="{{ $menu->name }}"
                                style="height: 150px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h6 class="card-title">{{ $menu->name }}</h6>
                            <p class="card-text small text-muted">{{ Str::limit($menu->description, 50) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                <button class="btn btn-sm btn-primary add-to-cart" data-menu-id="{{ $menu->id }}"
                                    data-name="{{ $menu->name }}">
                                    <i class="bi bi-plus-circle"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.add-to-cart').click(function() {
                var menuId = $(this).data('menu-id');
                var btn = $(this);
                btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

                $.ajax({
                    url: '{{ route('public.cart.add', ['token' => $token]) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        menu_id: menuId,
                        quantity: 1
                    },
                    success: function(response) {
                        // Update badge cart count
                        var countElem = $('#cartCount');
                        if (response.totalItems > 0) {
                            countElem.text(response.totalItems).show();
                        } else {
                            countElem.hide();
                        }
                        btn.prop('disabled', false).html(
                            '<i class="bi bi-check-lg"></i> Ditambah');
                        setTimeout(() => btn.html('<i class="bi bi-plus-circle"></i> Tambah'),
                            1000);
                    },
                    error: function() {
                        alert('Gagal menambahkan item.');
                        btn.prop('disabled', false).html(
                            '<i class="bi bi-plus-circle"></i> Tambah');
                    }
                });
            });
        });
    </script>
@endpush
