@extends('public.layout')

@section('title', 'Status Pesanan')

@section('content')

    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
            style="width:56px;height:56px;background:var(--ts-primary-light,#fff3ef);">
            <i class="bi bi-receipt" style="font-size:1.6rem;color:var(--ts-primary,#e8410a);"></i>
        </div>
        <h5 class="fw-700 mb-0" style="font-weight:700;">
            Pesanan #<span id="orderNumber">{{ $order->order_number }}</span>
        </h5>
        <p class="text-muted small mb-0">Terima kasih! Pesanan kamu sedang diproses.</p>
    </div>

    {{-- ── Status timeline ── --}}
    @php
        $steps = [
            ['key' => 'pending',    'label' => 'Diterima',  'icon' => 'bi-clock'],
            ['key' => 'processing', 'label' => 'Dimasak',   'icon' => 'bi-fire'],
            ['key' => 'ready',      'label' => 'Siap',      'icon' => 'bi-bell'],
            ['key' => 'completed',  'label' => 'Selesai',   'icon' => 'bi-check-lg'],
        ];
        $order_seq = ['pending' => 0, 'processing' => 1, 'ready' => 2, 'completed' => 3, 'cancelled' => -1];
        $current_seq = $order_seq[$order->status] ?? 0;
    @endphp

    <div class="card mb-4">
        <div class="card-body px-3 py-4" id="timelineWrap">
            @if($order->status === 'cancelled')
                <div class="text-center py-2">
                    <i class="bi bi-x-circle-fill text-danger" style="font-size:2.5rem;"></i>
                    <p class="mt-2 mb-0 fw-600 text-danger" style="font-weight:600;">Pesanan Dibatalkan</p>
                </div>
            @else
                <div class="d-flex align-items-center position-relative" id="statusTimeline">
                    @foreach ($steps as $i => $step)
                        @php
                            $seq = $order_seq[$step['key']];
                            $stepClass = $seq < $current_seq ? 'done' : ($seq === $current_seq ? 'active' : '');
                        @endphp
                        <div class="status-step {{ $stepClass }}" id="step-{{ $step['key'] }}">
                            <div class="dot">
                                <i class="{{ $step['icon'] }}"></i>
                            </div>
                            <div class="label">{{ $step['label'] }}</div>
                        </div>
                        @if($i < count($steps) - 1)
                            <div class="status-line {{ $seq < $current_seq ? 'done' : '' }}"
                                id="line-{{ $step['key'] }}"></div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Order detail ── --}}
    <div class="card mb-4">
        <div class="card-header bg-white py-3 px-4"
            style="border-bottom:1px solid #f1f5f9;border-radius:14px 14px 0 0;">
            <span class="fw-600" style="font-weight:600;font-size:.9rem;color:#64748b;">
                <i class="bi bi-list-ul me-2"></i>Detail Pesanan
            </span>
        </div>
        <div class="card-body p-0">
            @foreach ($order->orderItems as $item)
                <div class="d-flex justify-content-between align-items-center px-4 py-3"
                    style="border-bottom:1px solid #f8fafc;">
                    <span style="font-size:.9rem;">
                        {{ $item->menu->name }}
                        <span class="text-muted">×{{ $item->quantity }}</span>
                    </span>
                    <span class="price-tag fw-600" style="font-size:.9rem;font-weight:600;">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </span>
                </div>
            @endforeach
            <div class="d-flex justify-content-between align-items-center px-4 py-3"
                style="background:#f8f9fa;border-radius:0 0 14px 14px;">
                <span class="fw-700" style="font-weight:700;">Total</span>
                <span class="price-tag fw-700">
                    Rp <span id="orderTotal">{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </span>
            </div>
        </div>
    </div>

    <div class="d-grid">
        <a href="{{ route('public.menu', ['token' => $token ?? '']) }}"
            class="btn btn-primary rounded-pill">
            <i class="bi bi-plus-circle me-2"></i>Pesan Lagi
        </a>
    </div>

@endsection

@push('scripts')
<script>
    var orderId = {{ $order->id }};
    var orderSeq = { pending: 0, processing: 1, ready: 2, completed: 3, cancelled: -1 };

    function applyTimeline(status) {
        var cur = orderSeq[status] ?? 0;
        if (status === 'cancelled') {
            $('#timelineWrap').html(
                '<div class="text-center py-2">' +
                '<i class="bi bi-x-circle-fill text-danger" style="font-size:2.5rem;"></i>' +
                '<p class="mt-2 mb-0 fw-600 text-danger" style="font-weight:600;">Pesanan Dibatalkan</p>' +
                '</div>'
            );
            return;
        }
        var steps = ['pending', 'processing', 'ready', 'completed'];
        steps.forEach(function (key) {
            var seq = orderSeq[key];
            var el = $('#step-' + key);
            el.removeClass('done active');
            if (seq < cur) el.addClass('done');
            else if (seq === cur) el.addClass('active');
            if (key !== 'completed') {
                var line = $('#line-' + key);
                line.removeClass('done');
                if (seq < cur) line.addClass('done');
            }
        });
    }

    function fetchStatus() {
        $.get('/order-status-ajax/' + orderId, function (data) {
            applyTimeline(data.status);
            if (data.status === 'completed' || data.status === 'cancelled') {
                clearInterval(pollInterval);
            }
        });
    }

    var pollInterval = setInterval(fetchStatus, 5000);
    fetchStatus();
</script>
@endpush
