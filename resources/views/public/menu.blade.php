@extends('public.layout')

@section('title', 'Menu')

@section('content')

    {{-- ── Session / scan banner ── --}}
    @if($activeSession)
        <div class="session-banner d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width:40px;height:40px;background:var(--ts-primary);flex-shrink:0;">
                    <i class="bi bi-receipt-cutoff text-white"></i>
                </div>
                <div>
                    <div class="fw-600 lh-sm" style="font-weight:600;">
                        {{ $activeSession->orders->count() }} pesanan aktif
                        @if($activeSession->status === 'payment_pending')
                            <span class="badge bg-warning text-dark ms-1" style="font-size:.7rem;">Menunggu Kasir</span>
                        @endif
                    </div>
                    <div class="small" style="color:#7c2d12;">
                        Total: <strong>Rp {{ number_format($sessionTotal, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
            <a href="{{ route('public.session', ['token' => $token]) }}"
                class="btn btn-sm btn-add rounded-pill px-3" style="height:auto;width:auto;border-radius:50px!important;">
                <i class="bi bi-eye me-1"></i>Lihat
            </a>
        </div>
    @elseif($token)
        <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-qr-code fs-5"></i>
            <span>Meja berhasil di-scan. Silakan pilih menu.</span>
        </div>
    @else
        <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-bag-check fs-5"></i>
            <span>Takeaway — Pesan untuk dibawa pulang.</span>
        </div>
    @endif

    {{-- ── Category sections ── --}}
    @foreach ($categories as $category)
        @if($category->menus->isNotEmpty())
            <div class="mb-4">
                <div class="section-label">{{ $category->name }}</div>
                <div class="row g-3">
                    @foreach ($category->menus as $menu)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card menu-card h-100">
                                @if ($menu->image)
                                    <img src="{{ asset('storage/' . $menu->image) }}"
                                        class="menu-img" alt="{{ $menu->name }}">
                                @else
                                    <div class="menu-img-placeholder">
                                        <i class="bi bi-image text-muted" style="font-size:2rem;"></i>
                                    </div>
                                @endif
                                <div class="card-body p-2 p-md-3 d-flex flex-column">
                                    <h6 class="mb-1" style="font-size:.88rem;font-weight:600;line-height:1.3;">
                                        {{ $menu->name }}
                                    </h6>
                                    @if($menu->description)
                                        <p class="text-muted mb-2"
                                            style="font-size:.75rem;line-height:1.4;flex:1;">
                                            {{ Str::limit($menu->description, 50) }}
                                        </p>
                                    @else
                                        <div class="flex-grow-1"></div>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <span class="price-tag" style="font-size:.88rem;">
                                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                                        </span>
                                        <button class="btn-add add-to-cart"
                                            data-menu-id="{{ $menu->id }}"
                                            data-name="{{ $menu->name }}"
                                            title="Tambah ke keranjang">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.add-to-cart').click(function () {
            var menuId = $(this).data('menu-id');
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

            $.ajax({
                url: '{{ route('public.cart.add', ['token' => $token]) }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', menu_id: menuId, quantity: 1 },
                success: function (response) {
                    var countElem = $('#cartCount');
                    if (response.totalItems > 0) {
                        countElem.text(response.totalItems).show();
                    } else {
                        countElem.hide();
                    }
                    btn.html('<i class="bi bi-check-lg"></i>');
                    setTimeout(function () {
                        btn.prop('disabled', false).html('<i class="bi bi-plus-lg"></i>');
                    }, 900);
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menambahkan item.', timer: 1500, showConfirmButton: false });
                    btn.prop('disabled', false).html('<i class="bi bi-plus-lg"></i>');
                }
            });
        });
    });
</script>
@endpush
