@extends('layouts.admin')

@section('title', 'Kasir — Pembayaran')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-700 mb-1" style="font-weight:700;">Sesi Aktif</h4>
            <p class="text-muted small mb-0">{{ count($sessions) }} sesi sedang berjalan</p>
        </div>
        <span class="badge rounded-pill px-3 py-2"
            style="background:#dbeafe;color:#1e40af;font-size:.8rem;">
            {{ now()->format('d M Y') }}
        </span>
    </div>

    @if($sessions->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size:3rem;color:#cbd5e1;"></i>
                <p class="mt-3 mb-0">Tidak ada sesi aktif saat ini.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($sessions as $session)
                @php
                    $isPending = $session->status === 'payment_pending';
                    $totalAll  = $session->orders->where('status', '!=', 'cancelled')->sum('total_amount');
                @endphp
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100" style="border-top:3px solid {{ $isPending ? '#f59e0b' : '#3b82f6' }};">
                        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-600" style="font-weight:600;font-size:.875rem;">
                                    @if($session->table)
                                        <i class="bi bi-grid-3x3 me-1 text-muted"></i>Meja {{ $session->table->name }}
                                    @else
                                        <i class="bi bi-bag me-1 text-muted"></i>Takeaway
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size:.75rem;">
                                    {{ $session->order_type === 'dine_in' ? 'Dine In' : 'Takeaway' }}
                                    · {{ $session->payment_mode === 'instant' ? 'Instant Pay' : 'Open Table' }}
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3"
                                style="background:{{ $isPending ? '#fef3c7' : '#dcfce7' }};color:{{ $isPending ? '#92400e' : '#166534' }};font-size:.75rem;">
                                {{ $isPending ? 'Minta Bayar' : 'Aktif' }}
                            </span>
                        </div>

                        <div class="card-body px-4 py-3">
                            @forelse($session->orders as $order)
                                @php
                                    $oBadgeBg = match($order->status) {
                                        'pending'    => '#f1f5f9',
                                        'processing' => '#fef3c7',
                                        'ready'      => '#dbeafe',
                                        'completed'  => '#dcfce7',
                                        default      => '#fee2e2',
                                    };
                                    $oBadgeColor = match($order->status) {
                                        'pending'    => '#475569',
                                        'processing' => '#92400e',
                                        'ready'      => '#1e40af',
                                        'completed'  => '#166534',
                                        default      => '#991b1b',
                                    };
                                @endphp
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span style="font-size:.8rem;">#{{ $order->order_number }}</span>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($order->status !== 'cancelled')
                                            <span class="text-muted" style="font-size:.78rem;">
                                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                            </span>
                                        @endif
                                        <span class="badge rounded-pill"
                                            style="background:{{ $oBadgeBg }};color:{{ $oBadgeColor }};font-size:.7rem;padding:.25em .6em;">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Belum ada pesanan.</p>
                            @endforelse
                        </div>

                        <div class="card-footer bg-white px-4 py-3"
                            style="border-top:1px solid #f1f5f9;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted" style="font-size:.82rem;font-weight:500;">Total</span>
                                <span class="fw-700" style="font-weight:700;color:#1e293b;">
                                    Rp {{ number_format($totalAll, 0, ',', '.') }}
                                </span>
                            </div>
                            <a href="{{ route('admin.payments.show', $session->id) }}"
                                class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-cash-coin me-1"></i>Proses Pembayaran
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
