@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
    <h3 class="mb-4">Laporan Penjualan</h3>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3 mb-4">
        <div class="col-auto">
            <label for="start_date" class="form-label">Dari Tanggal</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $start_date }}">
        </div>
        <div class="col-auto">
            <label for="end_date" class="form-label">Sampai Tanggal</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $end_date }}">
        </div>
        <div class="col-auto align-self-end">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body">
                    <div class="small text-white-50">Total Pendapatan (Paid)</div>
                    <div class="fw-bold fs-5">Rp {{ number_format($total_revenue, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <div class="small text-white-50">Transaksi Lunas</div>
                    <div class="fw-bold fs-5">{{ $total_transactions }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body">
                    <div class="small text-dark opacity-75">Total Pesanan</div>
                    <div class="fw-bold fs-5">{{ $total_orders }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body">
                    <div class="small text-white-50">Nilai Pesanan</div>
                    <div class="fw-bold fs-5">Rp {{ number_format($total_orders_amount, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#ordersPane"
                type="button" role="tab">
                Pesanan <span class="badge bg-warning text-dark ms-1">{{ $total_orders }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#paymentsPane"
                type="button" role="tab">
                Pembayaran <span class="badge bg-success ms-1">{{ $total_transactions }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Tab Pesanan --}}
        <div class="tab-pane fade show active" id="ordersPane" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-striped" id="ordersTable">
                    <thead>
                        <tr>
                            <th>No. Order</th>
                            <th>Meja / Tipe</th>
                            <th>Jumlah Item</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>
                                    @if ($order->session?->table)
                                        Meja {{ $order->session->table->name }}
                                    @else
                                        <span class="text-muted">Takeaway</span>
                                    @endif
                                </td>
                                <td>{{ $order->orderItems->sum('quantity') }}</td>
                                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $badge = match($order->status) {
                                            'pending'    => 'secondary',
                                            'processing' => 'warning',
                                            'ready'      => 'info',
                                            'completed'  => 'success',
                                            default      => 'dark',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab Pembayaran --}}
        <div class="tab-pane fade" id="paymentsPane" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-striped" id="paymentsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Session</th>
                            <th>Jumlah</th>
                            <th>Metode</th>
                            <th>Tanggal Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>#{{ $payment->session_id }}</td>
                                <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td>{{ ucfirst($payment->method) }}</td>
                                <td>{{ $payment->paid_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#ordersTable').DataTable({
                order: [[5, 'desc']],
                language: { url: '' },
                columnDefs: [{ orderable: false, targets: [] }]
            });
            $('#paymentsTable').DataTable({
                order: [[4, 'desc']],
                language: { url: '' }
            });
        });
    </script>
@endpush
