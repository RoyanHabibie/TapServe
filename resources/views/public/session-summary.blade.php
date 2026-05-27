@extends('public.layout')

@section('title', 'Pesanan Saya — Meja ' . $table->name)

@section('content')

    {{-- ── Header ── --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('public.menu', ['token' => $token]) }}"
            class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <h5 class="mb-0 fw-700" style="font-weight:700;">Meja {{ $table->name }}</h5>
            <div class="text-muted small">{{ $session->orders->count() }} pesanan dalam sesi ini</div>
        </div>
        @php
            $statusBg    = $session->status === 'payment_pending' ? '#fef3c7' : '#dcfce7';
            $statusColor = $session->status === 'payment_pending' ? '#92400e' : '#166534';
            $statusLabel = $session->status === 'payment_pending' ? 'Menunggu Kasir' : 'Aktif';
        @endphp
        <span class="badge rounded-pill px-3 py-2 fs-6" id="sessionStatusBadge"
            style="background:{{ $statusBg }};color:{{ $statusColor }};font-size:.8rem!important;">
            {{ $statusLabel }}
        </span>
    </div>

    {{-- ── Orders ── --}}
    @if($session->orders->isEmpty())
        <div class="alert alert-info d-flex align-items-center gap-2">
            <i class="bi bi-info-circle fs-5"></i>
            <span>Belum ada pesanan dalam sesi ini.</span>
        </div>
    @else
        <div class="d-flex flex-column gap-3 mb-3">
            @foreach($session->orders as $order)
                @if($order->status === 'cancelled') @continue @endif
                @php
                    $badgeBg = match($order->status) {
                        'pending'    => '#f1f5f9',
                        'processing' => '#fef3c7',
                        'ready'      => '#dbeafe',
                        'completed'  => '#dcfce7',
                        default      => '#f1f5f9',
                    };
                    $badgeColor = match($order->status) {
                        'pending'    => '#475569',
                        'processing' => '#92400e',
                        'ready'      => '#1e40af',
                        'completed'  => '#166534',
                        default      => '#475569',
                    };
                    $statusLabel = match($order->status) {
                        'pending'    => 'Menunggu',
                        'processing' => 'Dimasak',
                        'ready'      => 'Siap',
                        'completed'  => 'Selesai',
                        default      => ucfirst($order->status),
                    };
                @endphp
                <div class="card" style="border-radius:14px;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 px-4"
                        style="border-bottom:1px solid #f1f5f9;border-radius:14px 14px 0 0;">
                        <span class="fw-600" style="font-weight:600;font-size:.875rem;">
                            #{{ $order->order_number }}
                        </span>
                        <span class="badge rounded-pill px-3"
                            style="background:{{ $badgeBg }};color:{{ $badgeColor }};font-size:.75rem;">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div class="card-body p-0">
                        @foreach($order->orderItems as $item)
                            <div class="d-flex justify-content-between align-items-center px-4 py-2"
                                style="border-bottom:1px solid #f8fafc;">
                                <span style="font-size:.875rem;">
                                    {{ $item->menu->name }}
                                    <span class="text-muted">×{{ $item->quantity }}</span>
                                </span>
                                <span class="price-tag" style="font-size:.875rem;font-weight:600;">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                        @if($order->notes)
                            <div class="px-4 py-2 text-muted small"
                                style="background:#f8fafc;border-radius:0 0 14px 14px;">
                                <i class="bi bi-chat-left-text me-1"></i>{{ $order->notes }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Total ── --}}
    <div class="card mb-4"
        style="border-radius:12px;background:var(--ts-primary-light,#fff3ef);border:1px solid var(--ts-primary-border,#fbc4b0);">
        <div class="card-body d-flex justify-content-between align-items-center py-3">
            <span class="fw-600" style="font-weight:600;color:#7c2d12;">Total Tagihan</span>
            <span class="price-tag fw-700" style="font-size:1.15rem;">
                Rp {{ number_format($total, 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- ── Actions ── --}}
    <div id="sessionActions">
        @if($session->status === 'open')
            <div class="d-grid gap-2">
                <form action="{{ route('public.session.request-payment', ['token' => $token]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill">
                        <i class="bi bi-cash-coin me-2"></i>Minta Bayar Sekarang
                    </button>
                </form>
                <a href="{{ route('public.menu', ['token' => $token]) }}"
                    class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Pesanan
                </a>
            </div>
        @elseif($session->status === 'payment_pending')
            {{-- ── QRIS self-payment ── --}}
            @if($shop->qris_image)
                <div class="card mb-3 text-center"
                    style="border-radius:14px;border:1px solid #e0e7ff;">
                    <div class="card-header bg-white py-3 px-4 text-start"
                        style="border-radius:14px 14px 0 0;border-bottom:1px solid #f1f5f9;">
                        <span class="fw-600" style="font-weight:600;font-size:.875rem;color:#1e293b;">
                            <i class="bi bi-qr-code-scan me-2" style="color:#7c3aed;"></i>Bayar Mandiri via QRIS
                        </span>
                    </div>
                    <div class="card-body px-4 py-4">
                        <img src="{{ asset('storage/' . $shop->qris_image) }}"
                            alt="QRIS"
                            style="max-width:240px;width:100%;border-radius:10px;
                                   box-shadow:0 4px 20px rgba(0,0,0,.1);">
                        <p class="mt-3 mb-1 fw-600" style="font-weight:600;font-size:.875rem;">
                            Scan QR di atas untuk membayar
                        </p>
                        <p class="text-muted small mb-0">
                            Total:
                            <strong style="color:#1e293b;">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </strong>
                        </p>
                    </div>
                </div>
            @endif

            <div class="card text-center py-4 mb-3"
                style="background:#fefce8;border:1px solid #fde68a;border-radius:14px;">
                <div class="card-body">
                    <i class="bi bi-hourglass-split mb-2 d-block"
                        style="font-size:1.75rem;color:#d97706;"></i>
                    <div class="fw-600" style="font-weight:600;color:#92400e;" id="waitingAlert">
                        Menunggu kasir mengkonfirmasi pembayaran...
                    </div>
                    <div class="text-muted small mt-1">Halaman ini akan otomatis terupdate.</div>
                </div>
            </div>
            <div class="d-grid">
                <a href="{{ route('public.menu', ['token' => $token]) }}"
                    class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Pesanan
                </a>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    @if(in_array($session->status, ['open', 'payment_pending']))
    var pollSession = setInterval(function () {
        $.get('{{ route('public.session.status', ['token' => $token]) }}', function (data) {
            if (data.status === 'closed' || data.status === 'paid') {
                clearInterval(pollSession);
                $('#sessionStatusBadge')
                    .css({ background: '#dcfce7', color: '#166534' })
                    .text('Lunas');
                $('#sessionActions').html(
                    '<div class="card text-center py-5" style="border-radius:14px;border:none;background:#f0fdf4;">' +
                        '<div class="card-body">' +
                            '<i class="bi bi-check-circle-fill d-block mb-3" style="font-size:3.5rem;color:#22c55e;"></i>' +
                            '<h5 class="fw-700" style="font-weight:700;">Pembayaran Berhasil!</h5>' +
                            '<p class="text-muted mb-0">Terima kasih sudah berkunjung.</p>' +
                        '</div>' +
                    '</div>'
                );
            }
        });
    }, 5000);
    @endif
</script>
@endpush
