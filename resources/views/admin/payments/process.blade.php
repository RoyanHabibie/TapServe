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

        {{-- ══════════════════════════════════════
             Kolom kiri: ringkasan + tambah manual
        ══════════════════════════════════════ --}}
        <div class="col-12 col-lg-5">

            {{-- ── Ringkasan pesanan ── --}}
            <div class="card">
                <div class="card-header py-3 px-4">
                    <span class="fw-600" style="font-weight:600;font-size:.875rem;">
                        <i class="bi bi-receipt me-2" style="color:#3b82f6;"></i>Ringkasan Pesanan
                    </span>
                </div>
                <div class="card-body p-0" style="max-height:360px;overflow-y:auto;">
                    @foreach ($session->orders as $order)
                        @if($order->status === 'cancelled') @continue @endif
                        @php
                            $statusLabel = match($order->status) {
                                'pending'    => 'Menunggu',
                                'processing' => 'Dimasak',
                                'ready'      => 'Siap',
                                'completed'  => 'Selesai',
                                default      => ucfirst($order->status),
                            };
                            $statusColor = match($order->status) {
                                'pending'    => '#475569',
                                'processing' => '#92400e',
                                'ready'      => '#1e40af',
                                'completed'  => '#166534',
                                default      => '#475569',
                            };
                            $statusBg = match($order->status) {
                                'pending'    => '#f1f5f9',
                                'processing' => '#fef3c7',
                                'ready'      => '#dbeafe',
                                'completed'  => '#dcfce7',
                                default      => '#f1f5f9',
                            };
                            $isManual = str_starts_with($order->order_number, 'MNL-');
                        @endphp

                        {{-- Sub-header order --}}
                        <div style="border-bottom:1px solid #f1f5f9;">
                            <div class="d-flex justify-content-between align-items-center px-4 py-2"
                                style="background:#f8fafc;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-600" style="font-size:.78rem;font-weight:600;color:#334155;">
                                        #{{ $order->order_number }}
                                    </span>
                                    @if($isManual)
                                        <span style="font-size:.65rem;font-weight:600;padding:.1rem .4rem;
                                                     border-radius:5px;background:#f5f3ff;color:#6d28d9;
                                                     border:1px solid #c4b5fd;">
                                            <i class="bi bi-person-fill-gear" style="font-size:.6rem;"></i> Manual
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-600" style="font-size:.78rem;color:#64748b;">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </span>
                                    <span style="font-size:.68rem;font-weight:600;padding:.2rem .5rem;
                                                 border-radius:6px;background:{{ $statusBg }};color:{{ $statusColor }};">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            </div>

                            {{-- Item rows --}}
                            @foreach($order->orderItems as $item)
                                @php $ot = $item->order_type ?? 'dine_in'; @endphp
                                <div class="d-flex justify-content-between align-items-center px-4 py-2"
                                    style="border-bottom:1px solid #f8fafc;">
                                    <div class="d-flex align-items-center gap-2 flex-grow-1 me-2">
                                        @if($ot === 'takeaway')
                                            <span style="font-size:.63rem;font-weight:600;padding:.1rem .35rem;
                                                         border-radius:4px;background:#fffbeb;color:#92400e;
                                                         border:1px solid #fcd34d;white-space:nowrap;flex-shrink:0;">
                                                <i class="bi bi-bag" style="font-size:.55rem;"></i> Bawa
                                            </span>
                                        @else
                                            <span style="font-size:.63rem;font-weight:600;padding:.1rem .35rem;
                                                         border-radius:4px;background:#eff6ff;color:#1d4ed8;
                                                         border:1px solid #bfdbfe;white-space:nowrap;flex-shrink:0;">
                                                <i class="bi bi-shop" style="font-size:.55rem;"></i> DI
                                            </span>
                                        @endif
                                        <span style="font-size:.82rem;">
                                            {{ $item->menu->name }}
                                            <span class="text-muted">×{{ $item->quantity }}</span>
                                        </span>
                                    </div>
                                    <span style="font-size:.82rem;font-weight:600;color:#1e293b;flex-shrink:0;">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach

                            @if($order->notes)
                                <div class="px-4 py-2 text-muted" style="font-size:.72rem;background:#fafafa;">
                                    <i class="bi bi-chat-left-text me-1"></i>{{ $order->notes }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Total row --}}
                <div class="d-flex justify-content-between align-items-center px-4 py-3"
                    style="background:#f8f9fa;border-top:1px solid #f1f5f9;border-radius:0 0 12px 12px;">
                    <span class="fw-700" style="font-weight:700;">Total</span>
                    <span class="fw-700" id="grandTotal"
                        style="font-weight:700;font-size:1.15rem;color:#1e293b;">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- ── Tambah Pesanan Manual ── --}}
            <div class="card mt-3">
                <div class="card-header py-3 px-4" style="cursor:pointer;"
                    data-bs-toggle="collapse" data-bs-target="#manualPanel" aria-expanded="false">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-600" style="font-weight:600;font-size:.875rem;">
                            <i class="bi bi-plus-circle me-2" style="color:#8b5cf6;"></i>Tambah Pesanan Manual
                        </span>
                        <i class="bi bi-chevron-down text-muted" style="font-size:.8rem;transition:transform .2s;"
                            id="manualChevron"></i>
                    </div>
                </div>
                <div class="collapse" id="manualPanel">
                    <div class="card-body px-4 py-4">

                        <p class="text-muted small mb-3">
                            Tambahkan item yang diambil customer tanpa melalui aplikasi (contoh: air mineral, snack dari display).
                        </p>

                        <form action="{{ route('admin.payments.manual-order', $session->id) }}"
                            method="POST" id="manualForm">
                            @csrf

                            <div id="manualRows" class="d-flex flex-column gap-2 mb-3"></div>

                            <button type="button" id="addRowBtn"
                                class="btn btn-sm btn-outline-secondary w-100 mb-3"
                                style="border-style:dashed;">
                                <i class="bi bi-plus me-1"></i>Tambah Item Lain
                            </button>

                            <div class="mb-3">
                                <label class="form-label" style="font-size:.8rem;">Catatan <span class="text-muted">(opsional)</span></label>
                                <input type="text" name="manual_notes" class="form-control form-control-sm"
                                    placeholder="Contoh: diambil dari kulkas">
                            </div>

                            <button type="submit" class="btn btn-sm btn-success w-100">
                                <i class="bi bi-check2-circle me-1"></i>Tambahkan ke Tagihan
                            </button>
                        </form>

                    </div>
                </div>
            </div>

            {{-- ── QRIS panel ── --}}
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

        {{-- ══════════════════════════════════════
             Kolom kanan: form pembayaran
        ══════════════════════════════════════ --}}
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-header py-3 px-4">
                    <span class="fw-600" style="font-weight:600;font-size:.875rem;">
                        <i class="bi bi-cash-coin me-2" style="color:#16a34a;"></i>Form Pembayaran
                    </span>
                </div>
                <div class="card-body px-4 py-4">

                    <form action="{{ route('admin.payments.store', $session->id) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Metode Pembayaran</label>
                            @if($paymentMethods->isEmpty())
                                <div class="alert alert-warning rounded-3 py-2 small">
                                    Belum ada metode pembayaran aktif.
                                    <a href="{{ route('admin.payment-methods.index') }}">Kelola di sini</a>.
                                </div>
                            @else
                            <div class="row g-2" id="methodOptions">
                                @foreach($paymentMethods as $m)
                                    <div class="col-6">
                                        <label class="method-card d-flex align-items-center gap-2 p-3 rounded-3 w-100"
                                            style="border:2px solid #e2e8f0;cursor:pointer;transition:border-color .15s,background .15s;">
                                            <input type="radio" name="method" value="{{ $m->code }}"
                                                class="d-none method-radio"
                                                {{ old('method') === $m->code ? 'checked' : '' }}>
                                            <i class="bi {{ $m->icon }}" style="color:{{ $m->color }};font-size:1.2rem;flex-shrink:0;"></i>
                                            <span class="fw-600" style="font-weight:600;font-size:.875rem;">{{ $m->name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @endif
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

                            <div class="mt-2 p-3 rounded-3" id="changeBox" style="background:#f0fdf4;display:none;">
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
    var total     = {{ $total }};
    var hasQris   = {{ $qrisImage ? 'true' : 'false' }};
    var menus     = @json($menus);
    var rowIndex  = 0;

    /* ── Format rupiah ── */
    function fmtRp(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    /* ── Build <select> options from menus JSON ── */
    function buildMenuSelect(name, selectedId) {
        var html = '<select name="' + name + '" class="form-select form-select-sm menu-sel" required>';
        html += '<option value="">— Pilih Menu —</option>';
        var lastCat = null;
        menus.forEach(function (m) {
            if (m.category !== lastCat) {
                if (lastCat !== null) html += '</optgroup>';
                html += '<optgroup label="' + m.category + '">';
                lastCat = m.category;
            }
            var sel = (selectedId && selectedId == m.id) ? ' selected' : '';
            html += '<option value="' + m.id + '" data-price="' + m.price + '"' + sel + '>'
                 + m.name + ' — ' + fmtRp(m.price)
                 + '</option>';
        });
        if (lastCat !== null) html += '</optgroup>';
        html += '</select>';
        return html;
    }

    /* ── Add a new item row ── */
    function addRow() {
        var idx = rowIndex++;
        var row = $('<div class="d-flex flex-column gap-2 p-3 rounded-3" data-row="' + idx + '"'
            + ' style="background:#f8fafc;border:1px solid #e2e8f0;position:relative;">');

        // Menu select
        row.append(buildMenuSelect('items[' + idx + '][menu_id]', null));

        // Qty + type + remove button row
        var controls = $('<div class="d-flex gap-2 align-items-center">');

        // Qty
        controls.append(
            '<div class="input-group input-group-sm" style="width:110px;flex-shrink:0;">'
          + '<span class="input-group-text" style="font-size:.78rem;">Qty</span>'
          + '<input type="number" name="items[' + idx + '][quantity]" '
          +        'class="form-control form-control-sm qty-input" '
          +        'value="1" min="1" max="99" required style="width:50px;">'
          + '</div>'
        );

        // Order type toggle
        controls.append(
            '<div class="d-flex gap-1 flex-grow-1">'
          + '<label class="type-lbl flex-fill text-center" style="cursor:pointer;">'
          + '<input type="radio" name="items[' + idx + '][order_type]" value="dine_in" class="d-none" checked>'
          + '<span class="type-pill type-dine w-100 d-block" style="font-size:.72rem;font-weight:600;'
          +       'padding:.25rem .4rem;border-radius:6px;background:#eff6ff;color:#1d4ed8;'
          +       'border:2px solid #3b82f6;text-align:center;">'
          + '<i class="bi bi-shop me-1" style="font-size:.65rem;"></i>Dine In</span>'
          + '</label>'
          + '<label class="type-lbl flex-fill text-center" style="cursor:pointer;">'
          + '<input type="radio" name="items[' + idx + '][order_type]" value="takeaway" class="d-none">'
          + '<span class="type-pill type-away w-100 d-block" style="font-size:.72rem;font-weight:600;'
          +       'padding:.25rem .4rem;border-radius:6px;background:#fff;color:#94a3b8;'
          +       'border:2px solid #e2e8f0;text-align:center;">'
          + '<i class="bi bi-bag me-1" style="font-size:.65rem;"></i>Bawa</span>'
          + '</label>'
          + '</div>'
        );

        // Delete row button
        controls.append(
            '<button type="button" class="btn btn-sm btn-outline-danger remove-row flex-shrink-0"'
          + ' style="width:30px;height:30px;padding:0;border-radius:8px;">'
          + '<i class="bi bi-trash" style="font-size:.75rem;"></i></button>'
        );

        row.append(controls);

        // Subtotal preview
        row.append('<div class="subtotal-preview text-muted" style="font-size:.72rem;text-align:right;"></div>');

        $('#manualRows').append(row);
    }

    /* ── Init: add first row on open ── */
    $('#manualPanel').on('show.bs.collapse', function () {
        if ($('#manualRows').children().length === 0) addRow();
    });

    /* ── Add row button ── */
    $('#addRowBtn').on('click', addRow);

    /* ── Remove row ── */
    $(document).on('click', '.remove-row', function () {
        var rows = $('#manualRows').children();
        if (rows.length <= 1) {
            $(this).closest('[data-row]').find('.menu-sel').val('');
            $(this).closest('[data-row]').find('.qty-input').val(1);
            $(this).closest('[data-row]').find('.subtotal-preview').text('');
        } else {
            $(this).closest('[data-row]').remove();
        }
    });

    /* ── Order type toggle style ── */
    $(document).on('change', 'input[type="radio"][name^="items["]', function () {
        if (!$(this).attr('name').includes('[order_type]')) return;
        var row = $(this).closest('[data-row]');
        row.find('.type-lbl').each(function () {
            var radio = $(this).find('input[type="radio"]');
            var pill  = $(this).find('.type-pill');
            if (radio.is(':checked')) {
                if (radio.val() === 'dine_in') {
                    pill.css({ background: '#eff6ff', color: '#1d4ed8', borderColor: '#3b82f6' });
                } else {
                    pill.css({ background: '#fffbeb', color: '#92400e', borderColor: '#f59e0b' });
                }
            } else {
                pill.css({ background: '#fff', color: '#94a3b8', borderColor: '#e2e8f0' });
            }
        });
    });

    /* ── Subtotal preview per row ── */
    $(document).on('change input', '.menu-sel, .qty-input', function () {
        var row     = $(this).closest('[data-row]');
        var price   = parseFloat(row.find('.menu-sel option:selected').data('price') || 0);
        var qty     = parseInt(row.find('.qty-input').val()) || 0;
        var preview = row.find('.subtotal-preview');
        if (price > 0 && qty > 0) {
            preview.text('Subtotal: ' + fmtRp(price * qty));
        } else {
            preview.text('');
        }
    });

    /* ── Chevron rotate on collapse ── */
    $('#manualPanel').on('show.bs.collapse', function () {
        $('#manualChevron').css('transform', 'rotate(180deg)');
    }).on('hide.bs.collapse', function () {
        $('#manualChevron').css('transform', 'rotate(0deg)');
    });

    /* ══════════════════════════════════
       Existing: payment method + kembalian
    ══════════════════════════════════ */
    document.querySelectorAll('.method-radio').forEach(function (radio) {
        radio.closest('label').addEventListener('click', function () {
            document.querySelectorAll('.method-card').forEach(function (c) {
                c.style.borderColor = '#e2e8f0';
                c.style.background  = '#fff';
            });
            this.style.borderColor = '#3b82f6';
            this.style.background  = '#eff6ff';
            radio.checked = true;
            document.getElementById('methodHidden').value = radio.value;

            var qrisPanel = document.getElementById('qrisPanel');
            if (qrisPanel) {
                qrisPanel.style.display = (radio.value === 'qris' && hasQris) ? 'block' : 'none';
            }
            document.getElementById('amount').dispatchEvent(new Event('input'));
        });

        if (radio.checked) {
            radio.closest('label').style.borderColor = '#3b82f6';
            radio.closest('label').style.background  = '#eff6ff';
            if (radio.value === 'qris' && hasQris) {
                var qrisPanel = document.getElementById('qrisPanel');
                if (qrisPanel) qrisPanel.style.removeProperty('display');
            }
        }
    });

    document.getElementById('amount').addEventListener('input', function () {
        var paid   = parseFloat(this.value) || 0;
        var change = paid - total;
        var box    = document.getElementById('changeBox');
        var method = document.getElementById('methodHidden').value;

        if (method === 'cash' && paid >= total) {
            document.getElementById('changeAmount').textContent = fmtRp(change);
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    });

    document.getElementById('methodHidden').addEventListener('change', function () {
        document.getElementById('amount').dispatchEvent(new Event('input'));
    });
</script>
@endpush
