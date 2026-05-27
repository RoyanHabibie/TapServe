@extends('public.layout')

@section('title', 'Pesanan Saya — Meja ' . $table->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Meja {{ $table->name }}</h4>
            <small class="text-muted">{{ $session->orders->count() }} pesanan dalam sesi ini</small>
        </div>
        @php
            $statusBadge = $session->status === 'payment_pending' ? 'warning text-dark' : 'success';
            $statusLabel = $session->status === 'payment_pending' ? 'Menunggu Kasir' : 'Aktif';
        @endphp
        <span class="badge bg-{{ $statusBadge }} fs-6" id="sessionStatusBadge">{{ $statusLabel }}</span>
    </div>

    @if($session->orders->isEmpty())
        <div class="alert alert-info">Belum ada pesanan dalam sesi ini.</div>
    @else
        @foreach($session->orders as $order)
            @php
                $badge = match($order->status) {
                    'pending'    => 'secondary',
                    'processing' => 'warning',
                    'ready'      => 'info',
                    'completed'  => 'success',
                    default      => 'dark',
                };
            @endphp
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>#{{ $order->order_number }}</strong>
                    <span class="badge bg-{{ $badge }}">{{ ucfirst($order->status) }}</span>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($order->orderItems as $item)
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span>{{ $item->menu->name }}
                                <span class="text-muted">x{{ $item->quantity }}</span>
                            </span>
                            <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                @if($order->notes)
                    <div class="card-footer text-muted small">
                        <i class="bi bi-chat-left-text me-1"></i>{{ $order->notes }}
                    </div>
                @endif
            </div>
        @endforeach
    @endif

    <div class="card bg-light mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <strong class="fs-5">Total Tagihan</strong>
            <strong class="fs-5">Rp {{ number_format($total, 0, ',', '.') }}</strong>
        </div>
    </div>

    <div id="sessionActions">
        @if($session->status === 'open')
            <div class="d-grid gap-2">
                <form action="{{ route('public.session.request-payment', ['token' => $token]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-cash-coin me-2"></i>Minta Bayar Sekarang
                    </button>
                </form>
                <a href="{{ route('public.menu', ['token' => $token]) }}" class="btn btn-outline-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Pesanan
                </a>
            </div>
        @elseif($session->status === 'payment_pending')
            <div class="alert alert-warning text-center" id="waitingAlert">
                <i class="bi bi-hourglass-split me-2"></i>
                <strong>Menunggu kasir memproses pembayaran...</strong>
            </div>
            <div class="d-grid mt-2">
                <a href="{{ route('public.menu', ['token' => $token]) }}" class="btn btn-outline-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Pesanan
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
                    $('#sessionStatusBadge').attr('class', 'badge bg-primary fs-6').text('Lunas');
                    $('#sessionActions').html(
                        '<div class="alert alert-success text-center py-4">' +
                        '<i class="bi bi-check-circle-fill d-block mb-2" style="font-size:3rem"></i>' +
                        '<h5>Pembayaran Berhasil!</h5>' +
                        '<p class="mb-0 text-muted">Terima kasih sudah berkunjung.</p>' +
                        '</div>'
                    );
                }
            });
        }, 5000);
        @endif
    </script>
@endpush
