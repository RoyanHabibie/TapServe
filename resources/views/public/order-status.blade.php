@extends('public.layout')

@section('title', 'Status Pesanan')

@section('content')
    <div class="card">
        <div class="card-body text-center">
            <h4>Pesanan #{{ $order->order_number }}</h4>
            <p>Status: <span class="badge bg-warning text-dark">{{ ucfirst($order->status) }}</span></p>
            <p>Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            <hr>
            <h5>Detail Pesanan</h5>
            <ul class="list-group list-group-flush text-start">
                @foreach ($order->orderItems as $item)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $item->menu->name }} x{{ $item->quantity }}</span>
                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            <p class="mt-3 text-muted">Pesanan kamu sedang diproses. Silakan tunggu.</p>
            <a href="{{ route('public.menu', ['token' => $token ?? '']) }}" class="btn btn-primary">Pesan Lagi</a>
        </div>
    </div>
@endsection
