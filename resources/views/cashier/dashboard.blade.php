@extends('layouts.admin')

@section('title', 'Kasir - Pembayaran')

@section('content')
    <h3>Daftar Sesi Aktif</h3>

    <div class="row">
        @forelse($sessions as $session)
            <div class="col-md-4 mb-3">
                <div class="card border-primary">
                    <div class="card-header d-flex justify-content-between">
                        <strong>Session #{{ $session->id }}</strong>
                        <span class="badge bg-{{ $session->status == 'payment_pending' ? 'warning' : 'success' }}">
                            {{ $session->status == 'payment_pending' ? 'Menunggu Bayar' : 'Aktif' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <p>
                            Tipe: <strong>{{ $session->order_type == 'dine_in' ? 'Dine In' : 'Takeaway' }}</strong><br>
                            @if ($session->table)
                                Meja: {{ $session->table->name }}<br>
                            @endif
                            Pembayaran: {{ $session->payment_mode == 'instant' ? 'Instant' : 'Open Table' }}
                        </p>
                        <hr>
                        <h6>Pesanan:</h6>
                        <ul class="list-unstyled">
                            @foreach ($session->orders as $order)
                                <li>#{{ $order->order_number }}
                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                    @if ($order->status != 'cancelled')
                                        <br><small>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</small>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        @php
                            $totalAll = $session->orders->where('status', '!=', 'cancelled')->sum('total_amount');
                        @endphp
                        <p class="fw-bold">Total: Rp {{ number_format($totalAll, 0, ',', '.') }}</p>
                        <a href="#" class="btn btn-primary btn-sm w-100">Proses Pembayaran</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Tidak ada sesi aktif.</div>
            </div>
        @endforelse
    </div>
@endsection
