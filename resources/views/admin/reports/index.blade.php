@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')

    <h4 class="fw-700 mb-1" style="font-weight:700;">Laporan Penjualan</h4>
    <p class="text-muted small mb-4">Analisis transaksi dan pesanan berdasarkan rentang tanggal.</p>

    {{-- ── Filter ── --}}
    <div class="card mb-4">
        <div class="card-body px-4 py-3">
            <form method="GET" action="{{ route('admin.reports.index') }}"
                class="row g-3 align-items-end">
                <div class="col-12 col-sm-auto">
                    <label class="form-label mb-1">Dari Tanggal</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $start_date }}">
                </div>
                <div class="col-12 col-sm-auto">
                    <label class="form-label mb-1">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $end_date }}">
                </div>
                <div class="col-12 col-sm-auto">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Summary cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dcfce7;">
                        <i class="bi bi-cash-stack" style="color:#16a34a;"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Pendapatan</div>
                        <div class="fw-700" style="font-size:1rem;font-weight:700;color:#1e293b;">
                            Rp {{ number_format($total_revenue, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;">
                        <i class="bi bi-check2-circle" style="color:#2563eb;"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Transaksi Lunas</div>
                        <div class="fw-700" style="font-size:1rem;font-weight:700;color:#1e293b;">
                            {{ $total_transactions }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fef3c7;">
                        <i class="bi bi-bag" style="color:#d97706;"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Total Pesanan</div>
                        <div class="fw-700" style="font-size:1rem;font-weight:700;color:#1e293b;">
                            {{ $total_orders }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#ede9fe;">
                        <i class="bi bi-receipt" style="color:#7c3aed;"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Nilai Pesanan</div>
                        <div class="fw-700" style="font-size:1rem;font-weight:700;color:#1e293b;">
                            Rp {{ number_format($total_orders_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div class="card">
        <div class="card-header px-4 pt-3 pb-0" style="border-bottom:none;">
            <ul class="nav nav-tabs border-0" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4 fw-500" id="orders-tab"
                        data-bs-toggle="tab" data-bs-target="#ordersPane" type="button" role="tab"
                        style="font-weight:500;border-radius:8px 8px 0 0;">
                        <i class="bi bi-bag me-1"></i>Pesanan
                        <span class="badge ms-1 rounded-pill" style="background:#fef3c7;color:#92400e;">{{ $total_orders }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 fw-500" id="payments-tab"
                        data-bs-toggle="tab" data-bs-target="#paymentsPane" type="button" role="tab"
                        style="font-weight:500;border-radius:8px 8px 0 0;">
                        <i class="bi bi-cash-coin me-1"></i>Pembayaran
                        <span class="badge ms-1 rounded-pill" style="background:#dcfce7;color:#166534;">{{ $total_transactions }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            {{-- Tab Pesanan --}}
            <div class="tab-pane fade show active" id="ordersPane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="ordersTable">
                        <thead>
                            <tr>
                                <th>No. Order</th>
                                <th>Meja / Tipe</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="fw-500" style="font-weight:500;">{{ $order->order_number }}</td>
                                    <td>
                                        @if ($order->session?->table)
                                            <span class="badge rounded-pill" style="background:#dbeafe;color:#1e40af;">Meja {{ $order->session->table->name }}</span>
                                        @else
                                            <span class="badge rounded-pill" style="background:#f1f5f9;color:#475569;">Takeaway</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->orderItems->sum('quantity') }}</td>
                                    <td class="fw-600" style="font-weight:600;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $bg = match($order->status) {
                                                'pending'    => ['#f1f5f9','#475569'],
                                                'processing' => ['#fef3c7','#92400e'],
                                                'ready'      => ['#dbeafe','#1e40af'],
                                                'completed'  => ['#dcfce7','#166534'],
                                                default      => ['#fee2e2','#991b1b'],
                                            };
                                        @endphp
                                        <span class="badge rounded-pill"
                                            style="background:{{ $bg[0] }};color:{{ $bg[1] }};">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted" style="font-size:.82rem;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab Pembayaran --}}
            <div class="tab-pane fade" id="paymentsPane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="paymentsTable">
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
                                    <td class="text-muted">#{{ $payment->id }}</td>
                                    <td>
                                        <span class="badge rounded-pill" style="background:#f1f5f9;color:#475569;">Sesi #{{ $payment->session_id }}</span>
                                    </td>
                                    <td class="fw-600" style="font-weight:600;color:#16a34a;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge rounded-pill" style="background:#ede9fe;color:#7c3aed;">{{ ucfirst($payment->method) }}</span>
                                    </td>
                                    <td class="text-muted" style="font-size:.82rem;">{{ $payment->paid_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
        });
        $('#paymentsTable').DataTable({
            order: [[4, 'desc']],
            language: { url: '' }
        });
    });
</script>
@endpush
