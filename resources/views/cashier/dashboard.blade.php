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
                <div class="col-12 col-lg-6 col-xl-4">
                    <div class="card h-100" style="border-top:3px solid {{ $isPending ? '#f59e0b' : '#3b82f6' }};">

                        {{-- ── Header ── --}}
                        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-600" style="font-weight:600;font-size:.9rem;">
                                    @if($session->table)
                                        <i class="bi bi-grid-3x3 me-1 text-muted"></i>Meja {{ $session->table->name }}
                                    @else
                                        <i class="bi bi-bag me-1 text-muted"></i>Takeaway
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size:.73rem;">
                                    {{ $session->payment_mode === 'instant' ? 'Instant Pay' : 'Open Table' }}
                                    · Session #{{ $session->id }}
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3"
                                style="background:{{ $isPending ? '#fef3c7' : '#dcfce7' }};
                                       color:{{ $isPending ? '#92400e' : '#166534' }};font-size:.75rem;">
                                {{ $isPending ? 'Minta Bayar' : 'Aktif' }}
                            </span>
                        </div>

                        {{-- ── Order detail ── --}}
                        <div class="card-body p-0" style="max-height:320px;overflow-y:auto;">
                            @forelse($session->orders as $order)
                                @php
                                    $statusLabel = match($order->status) {
                                        'pending'    => 'Menunggu',
                                        'processing' => 'Dimasak',
                                        'ready'      => 'Siap',
                                        'completed'  => 'Selesai',
                                        default      => ucfirst($order->status),
                                    };
                                    $statusBg = match($order->status) {
                                        'pending'    => '#f1f5f9',
                                        'processing' => '#fef3c7',
                                        'ready'      => '#dbeafe',
                                        'completed'  => '#dcfce7',
                                        default      => '#fee2e2',
                                    };
                                    $statusColor = match($order->status) {
                                        'pending'    => '#475569',
                                        'processing' => '#92400e',
                                        'ready'      => '#1e40af',
                                        'completed'  => '#166534',
                                        default      => '#991b1b',
                                    };
                                    $isManual = str_starts_with($order->order_number, 'MNL-');
                                @endphp
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
                                            <span class="badge"
                                                style="background:{{ $statusBg }};color:{{ $statusColor }};
                                                       font-size:.68rem;padding:.25em .55em;border-radius:6px;">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>
                                    </div>
                                    @foreach($order->orderItems as $item)
                                        @php $ot = $item->order_type ?? 'dine_in'; @endphp
                                        <div class="d-flex justify-content-between align-items-center px-4 py-2"
                                            style="border-bottom:1px solid #f8fafc;">
                                            <div class="d-flex align-items-center gap-2 flex-grow-1 me-2">
                                                @if($ot === 'takeaway')
                                                    <span style="font-size:.65rem;font-weight:600;padding:.1rem .4rem;
                                                                 border-radius:5px;background:#fffbeb;color:#92400e;
                                                                 border:1px solid #fcd34d;white-space:nowrap;flex-shrink:0;">
                                                        <i class="bi bi-bag" style="font-size:.6rem;"></i> Bawa Pulang
                                                    </span>
                                                @else
                                                    <span style="font-size:.65rem;font-weight:600;padding:.1rem .4rem;
                                                                 border-radius:5px;background:#eff6ff;color:#1d4ed8;
                                                                 border:1px solid #bfdbfe;white-space:nowrap;flex-shrink:0;">
                                                        <i class="bi bi-shop" style="font-size:.6rem;"></i> Dine In
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
                                        <div class="px-4 py-2 text-muted"
                                            style="font-size:.73rem;background:#fafafa;">
                                            <i class="bi bi-chat-left-text me-1"></i>{{ $order->notes }}
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="px-4 py-3 text-muted small">Belum ada pesanan.</div>
                            @endforelse
                        </div>

                        {{-- ── Footer ── --}}
                        <div class="card-footer bg-white px-4 py-3" style="border-top:1px solid #f1f5f9;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted" style="font-size:.82rem;font-weight:500;">Total Tagihan</span>
                                <span class="fw-700" style="font-weight:700;font-size:1rem;color:#1e293b;">
                                    Rp {{ number_format($totalAll, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                {{-- Tambah manual --}}
                                <button type="button"
                                    class="btn btn-sm btn-manual w-100"
                                    style="border:2px dashed #8b5cf6;background:#faf5ff;color:#6d28d9;
                                           border-radius:8px;font-weight:600;font-size:.8rem;"
                                    data-session-id="{{ $session->id }}"
                                    data-session-label="{{ $session->table ? 'Meja '.$session->table->name : 'Takeaway' }} — Session #{{ $session->id }}"
                                    data-bs-toggle="modal" data-bs-target="#manualOrderModal">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Pesanan Manual
                                </button>
                                {{-- Proses pembayaran --}}
                                <a href="{{ route('admin.payments.show', $session->id) }}"
                                    class="btn btn-sm w-100"
                                    style="background:{{ $isPending ? '#f59e0b' : '#3b82f6' }};
                                           color:#fff;border:none;border-radius:8px;font-weight:600;">
                                    <i class="bi bi-cash-coin me-1"></i>
                                    {{ $isPending ? 'Konfirmasi Pembayaran' : 'Proses Pembayaran' }}
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ══════════════════════════════════════
         Modal Tambah Pesanan Manual
    ══════════════════════════════════════ --}}
    <div class="modal fade" id="manualOrderModal" tabindex="-1" aria-labelledby="manualModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:16px;border:none;">
                <div class="modal-header px-4 py-3" style="border-bottom:1px solid #f1f5f9;">
                    <div>
                        <h5 class="modal-title fw-700 mb-0" id="manualModalLabel" style="font-weight:700;font-size:1rem;">
                            <i class="bi bi-plus-circle me-2" style="color:#8b5cf6;"></i>Tambah Pesanan Manual
                        </h5>
                        <div class="text-muted small mt-1" id="manualModalSubtitle"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4 py-4">
                    <p class="text-muted small mb-4" style="background:#f8fafc;border-radius:8px;padding:.75rem 1rem;">
                        <i class="bi bi-info-circle me-1 text-primary"></i>
                        Gunakan fitur ini untuk item yang diambil customer tanpa melalui aplikasi,
                        seperti air mineral, snack display, atau item tambahan lainnya.
                    </p>

                    <form id="manualModalForm" method="POST">
                        @csrf

                        <div id="modalRows" class="d-flex flex-column gap-3 mb-3"></div>

                        <button type="button" id="modalAddRow"
                            class="btn btn-sm btn-outline-secondary w-100 mb-4"
                            style="border-style:dashed;border-radius:8px;">
                            <i class="bi bi-plus me-1"></i>Tambah Item Lain
                        </button>

                        <div>
                            <label class="form-label" style="font-size:.875rem;">
                                Catatan <span class="text-muted fw-400">(opsional)</span>
                            </label>
                            <input type="text" name="manual_notes" class="form-control"
                                placeholder="Contoh: diambil dari kulkas display">
                        </div>
                    </form>
                </div>

                <div class="modal-footer px-4 py-3" style="border-top:1px solid #f1f5f9;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="modalSubmit" class="btn btn-success px-4">
                        <i class="bi bi-check2-circle me-1"></i>Tambahkan ke Tagihan
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    var menus    = @json($menus);
    var rowIndex = 0;

    function fmtRp(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    /* ── Build menu select ── */
    function buildMenuSelect(idx) {
        var html = '<select name="items[' + idx + '][menu_id]" class="form-select form-select-sm menu-sel" required>'
                 + '<option value="">— Pilih Menu —</option>';
        var lastCat = null;
        menus.forEach(function (m) {
            if (m.category !== lastCat) {
                if (lastCat !== null) html += '</optgroup>';
                html += '<optgroup label="' + m.category + '">';
                lastCat = m.category;
            }
            html += '<option value="' + m.id + '" data-price="' + m.price + '">'
                  + m.name + ' — ' + fmtRp(m.price) + '</option>';
        });
        if (lastCat !== null) html += '</optgroup>';
        return html + '</select>';
    }

    /* ── Add a row ── */
    function addModalRow() {
        var idx = rowIndex++;
        var row = $('<div class="p-3 rounded-3" data-row="' + idx + '"'
                  + ' style="background:#f8fafc;border:1px solid #e2e8f0;">');

        row.append(buildMenuSelect(idx));

        var controls = $('<div class="d-flex gap-2 align-items-center mt-2">');

        // Qty
        controls.append(
            '<div class="input-group input-group-sm" style="width:110px;flex-shrink:0;">'
          + '<span class="input-group-text" style="font-size:.78rem;">Qty</span>'
          + '<input type="number" name="items[' + idx + '][quantity]" class="form-control qty-input"'
          +        ' value="1" min="1" max="99" required>'
          + '</div>'
        );

        // Order type
        controls.append(
            '<div class="d-flex gap-1 flex-grow-1">'
          + '<label class="type-lbl flex-fill text-center" style="cursor:pointer;">'
          +   '<input type="radio" name="items[' + idx + '][order_type]" value="dine_in" class="d-none" checked>'
          +   '<span class="type-pill w-100 d-block" style="font-size:.75rem;font-weight:600;padding:.3rem .4rem;'
          +        'border-radius:6px;background:#eff6ff;color:#1d4ed8;border:2px solid #3b82f6;text-align:center;">'
          +   '<i class="bi bi-shop me-1" style="font-size:.65rem;"></i>Dine In</span>'
          + '</label>'
          + '<label class="type-lbl flex-fill text-center" style="cursor:pointer;">'
          +   '<input type="radio" name="items[' + idx + '][order_type]" value="takeaway" class="d-none">'
          +   '<span class="type-pill w-100 d-block" style="font-size:.75rem;font-weight:600;padding:.3rem .4rem;'
          +        'border-radius:6px;background:#fff;color:#94a3b8;border:2px solid #e2e8f0;text-align:center;">'
          +   '<i class="bi bi-bag me-1" style="font-size:.65rem;"></i>Bawa Pulang</span>'
          + '</label>'
          + '</div>'
        );

        // Delete
        controls.append(
            '<button type="button" class="btn btn-sm btn-outline-danger remove-row flex-shrink-0"'
          + ' style="width:34px;height:34px;padding:0;border-radius:8px;">'
          + '<i class="bi bi-trash" style="font-size:.8rem;"></i></button>'
        );

        row.append(controls);
        row.append('<div class="subtotal-preview text-muted mt-1" style="font-size:.73rem;text-align:right;"></div>');

        $('#modalRows').append(row);
    }

    /* ── Order type pill style ── */
    $(document).on('change', 'input[type="radio"]', function () {
        if (!$(this).attr('name') || !$(this).attr('name').includes('[order_type]')) return;
        var row = $(this).closest('[data-row]');
        row.find('.type-lbl').each(function () {
            var radio = $(this).find('input');
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

    /* ── Subtotal preview ── */
    $(document).on('change input', '.menu-sel, .qty-input', function () {
        var row   = $(this).closest('[data-row]');
        var price = parseFloat(row.find('.menu-sel option:selected').data('price') || 0);
        var qty   = parseInt(row.find('.qty-input').val()) || 0;
        row.find('.subtotal-preview').text(price > 0 && qty > 0 ? 'Subtotal: ' + fmtRp(price * qty) : '');
    });

    /* ── Remove row ── */
    $(document).on('click', '.remove-row', function () {
        var rows = $('#modalRows').children();
        if (rows.length <= 1) {
            $(this).closest('[data-row]').find('.menu-sel').val('');
            $(this).closest('[data-row]').find('.qty-input').val(1);
            $(this).closest('[data-row]').find('.subtotal-preview').text('');
        } else {
            $(this).closest('[data-row]').remove();
        }
    });

    /* ── Modal: set session when opened ── */
    var manualOrderBaseUrl = '{{ rtrim(url('/admin/payments'), '/') }}';

    $('#manualOrderModal').on('show.bs.modal', function (e) {
        var btn       = $(e.relatedTarget);
        var sessionId = btn.data('session-id');
        var label     = btn.data('session-label');

        $('#manualModalSubtitle').text(label);
        $('#manualModalForm').attr('action', manualOrderBaseUrl + '/' + sessionId + '/manual-order');

        // Reset rows
        $('#modalRows').empty();
        rowIndex = 0;
        addModalRow();
    });

    /* ── Add row button ── */
    $('#modalAddRow').on('click', addModalRow);

    /* ── Submit ── */
    $('#modalSubmit').on('click', function () {
        $('#manualModalForm').submit();
    });
</script>
@endpush
