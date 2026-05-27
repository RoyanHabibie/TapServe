@extends('layouts.admin')

@section('title', 'Proses Pembayaran')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('cashier.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-700 mb-0" style="font-weight:700;">Proses Pembayaran</h4>
            <p class="text-muted small mb-0">Session #{{ $session->id }}
                @if($session->table) · Meja {{ $session->table->name }} @endif
            </p>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── Order summary ── --}}
        <div class="col-12 col-lg-5">
            <div class="card">
                <div class="card-header py-3 px-4">
                    <span class="fw-600" style="font-weight:600;font-size:.875rem;">
                        <i class="bi bi-receipt me-2" style="color:#3b82f6;"></i>Ringkasan Pesanan
                    </span>
                </div>
                <div class="card-body p-0">
                    @foreach ($session->orders as $order)
                        @if($order->status !== 'cancelled')
                            <div class="d-flex justify-content-between align-items-center px-4 py-3"
                                style="border-bottom:1px solid #f8fafc;">
                                <div>
                                    <div class="fw-500" style="font-weight:500;font-size:.875rem;">
                                        #{{ $order->order_number }}
                                    </div>
                                    <div class="text-muted" style="font-size:.75rem;">{{ ucfirst($order->status) }}</div>
                                </div>
                                <span class="fw-600" style="font-weight:600;">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    @endforeach

                    <div class="d-flex justify-content-between align-items-center px-4 py-3"
                        style="background:#f8f9fa;border-radius:0 0 12px 12px;">
                        <span class="fw-700" style="font-weight:700;">Total</span>
                        <span class="fw-700" style="font-weight:700;font-size:1.15rem;color:#1e293b;">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── QRIS panel (shown when QRIS method selected) ── --}}
            @php $qrisImage = auth()->user()->shop->qris_image ?? null; @endphp
            @if($qrisImage)
                <div class="card mt-3" id="qrisPanel" style="display:none!important;">
                    <div class="card-header py-3 px-4">
                        <span class="fw-600" style="font-weight:600;font-size:.875rem;">
                            <i class="bi bi-qr-code-scan me-2" style="color:#16a34a;"></i>QRIS Pembayaran
                        </span>
                    </div>
                    <div class="card-body text-center px-4 py-4">
                        <img src="{{ asset('storage/' . $qrisImage) }}"
                            alt="QRIS" style="max-width:240px;border-radius:10px;
                            box-shadow:0 4px 16px rgba(0,0,0,.1);">
                        <p class="text-muted small mt-3 mb-0">
                            Tunjukkan ke customer untuk scan & bayar
                        </p>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Payment form ── --}}
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-header py-3 px-4">
                    <span class="fw-600" style="font-weight:600;font-size:.875rem;">
                        <i class="bi bi-cash-coin me-2" style="color:#16a34a;"></i>Form Pembayaran
                    </span>
                </div>
                <div class="card-body px-4 py-4">

                    @if (session('error'))
                        <div class="alert alert-danger rounded-3 mb-3">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('admin.payments.store', $session->id) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Metode Pembayaran</label>
                            <div class="row g-2" id="methodOptions">
                                @php
                                    $methods = [
                                        ['value' => 'cash',     'label' => 'Tunai',    'icon' => 'bi-cash',           'color' => '#16a34a'],
                                        ['value' => 'qris',     'label' => 'QRIS',     'icon' => 'bi-qr-code-scan',   'color' => '#7c3aed'],
                                        ['value' => 'transfer', 'label' => 'Transfer', 'icon' => 'bi-bank',           'color' => '#2563eb'],
                                        ['value' => 'ewallet',  'label' => 'E-Wallet', 'icon' => 'bi-wallet2',        'color' => '#d97706'],
                                    ];
                                @endphp
                                @foreach($methods as $m)
                                    <div class="col-6">
                                        <label class="method-card d-flex align-items-center gap-2 p-3 rounded-3 w-100"
                                            style="border:2px solid #e2e8f0;cursor:pointer;transition:border-color .15s,background .15s;">
                                            <input type="radio" name="method" value="{{ $m['value'] }}"
                                                class="d-none method-radio"
                                                {{ old('method') === $m['value'] ? 'checked' : '' }}>
                                            <i class="bi {{ $m['icon'] }}" style="color:{{ $m['color'] }};font-size:1.2rem;flex-shrink:0;"></i>
                                            <span class="fw-600" style="font-weight:600;font-size:.875rem;">{{ $m['label'] }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="method" id="methodHidden" value="{{ old('method') }}">
                            @error('method')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="form-label">Jumlah Dibayar (Rp)</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                id="amount" name="amount"
                                value="{{ old('amount', $total) }}"
                                required step="1" min="0">
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- Kembalian --}}
                            <div class="mt-2 p-3 rounded-3" id="changeBox"
                                style="background:#f0fdf4;display:none;">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Kembalian</span>
                                    <span class="fw-700" style="font-weight:700;color:#16a34a;" id="changeAmount">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check2-circle me-2"></i>Bayar & Tutup Session
                            </button>
                            <a href="{{ route('cashier.dashboard') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    var total = {{ $total }};
    var hasQris = {{ $qrisImage ? 'true' : 'false' }};

    // Method card selection
    document.querySelectorAll('.method-radio').forEach(function (radio) {
        radio.closest('label').addEventListener('click', function () {
            // Reset all
            document.querySelectorAll('.method-card').forEach(function (c) {
                c.style.borderColor = '#e2e8f0';
                c.style.background  = '#fff';
            });
            // Activate selected
            this.style.borderColor = '#3b82f6';
            this.style.background  = '#eff6ff';
            radio.checked = true;
            document.getElementById('methodHidden').value = radio.value;

            // Show/hide QRIS panel
            var qrisPanel = document.getElementById('qrisPanel');
            if (qrisPanel) {
                qrisPanel.style.display = (radio.value === 'qris' && hasQris) ? 'block' : 'none';
            }
        });

        // Restore state on load
        if (radio.checked) {
            radio.closest('label').style.borderColor = '#3b82f6';
            radio.closest('label').style.background  = '#eff6ff';
            if (radio.value === 'qris' && hasQris) {
                var qrisPanel = document.getElementById('qrisPanel');
                if (qrisPanel) qrisPanel.style.removeProperty('display');
            }
        }
    });

    // Kembalian calculator
    document.getElementById('amount').addEventListener('input', function () {
        var paid = parseFloat(this.value) || 0;
        var change = paid - total;
        var box = document.getElementById('changeBox');
        var method = document.getElementById('methodHidden').value;

        if (method === 'cash' && paid >= total) {
            document.getElementById('changeAmount').textContent =
                'Rp ' + Math.round(change).toLocaleString('id-ID');
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    });

    // Watch method change to toggle kembalian
    document.getElementById('methodHidden').addEventListener('change', function () {
        document.getElementById('amount').dispatchEvent(new Event('input'));
    });
</script>
@endpush
