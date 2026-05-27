@extends('public.layout')

@section('title', 'Status Pesanan')

@section('content')
    <div class="card">
        <div class="card-body text-center" id="orderStatusCard">
            <h4>Pesanan #<span id="orderNumber">{{ $order->order_number }}</span></h4>
            <p>Status: <span id="orderStatus" class="badge bg-warning text-dark">{{ ucfirst($order->status) }}</span></p>
            <p>Total: Rp <span id="orderTotal">{{ number_format($order->total_amount, 0, ',', '.') }}</span></p>
            <hr>
            <h5>Detail Pesanan</h5>
            <ul class="list-group list-group-flush text-start" id="orderItems">
                @foreach ($order->orderItems as $item)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $item->menu->name }} x{{ $item->quantity }}</span>
                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            <p class="mt-3 text-muted">Pesanan kamu sedang diproses.</p>
            <a href="{{ route('public.menu', ['token' => $token ?? '']) }}" class="btn btn-primary">Pesan Lagi</a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var orderId = {{ $order->id }};

        function fetchStatus() {
            $.get('/order-status-ajax/' + orderId, function(data) {
                // Update status badge
                var status = data.status;
                var badgeClass = 'bg-warning text-dark';
                if (status == 'processing') badgeClass = 'bg-info';
                else if (status == 'ready') badgeClass = 'bg-success';
                else if (status == 'completed') badgeClass = 'bg-primary';
                else if (status == 'cancelled') badgeClass = 'bg-danger';

                $('#orderStatus').text(status.charAt(0).toUpperCase() + status.slice(1))
                    .attr('class', 'badge ' + badgeClass);

                // Optional: hentikan polling jika sudah final
                if (status == 'completed' || status == 'cancelled') {
                    clearInterval(pollInterval);
                }
            });
        }

        var pollInterval = setInterval(fetchStatus, 5000); // polling setiap 5 detik
        fetchStatus(); // panggil langsung
    </script>
@endpush
