@extends('public.layout')

@section('title', 'Checkout')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('public.cart', ['token' => $token]) }}"
            class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-700" style="font-weight:700;">Konfirmasi Pesanan</h5>
    </div>

    {{-- ── Order summary card ── --}}
    <div class="card mb-3" style="border-radius:14px;">
        <div class="card-header bg-white py-3 px-4"
            style="border-bottom:1px solid #f1f5f9;border-radius:14px 14px 0 0;">
            <span class="fw-600" style="font-weight:600;font-size:.9rem;color:#64748b;">
                <i class="bi bi-list-ul me-2"></i>Ringkasan Pesanan
            </span>
        </div>
        <div class="card-body p-0">
            @foreach ($cart as $item)
                @php $ot = $item['order_type'] ?? 'dine_in'; @endphp
                <div class="d-flex justify-content-between align-items-center px-4 py-3"
                    style="border-bottom:1px solid #f8fafc;">
                    <div>
                        <div class="fw-600" style="font-weight:600;font-size:.9rem;">{{ $item['name'] }}</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="text-muted small">
                                {{ $item['quantity'] }} ×
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </span>
                            @if ($ot === 'takeaway')
                                <span style="font-size:.7rem;font-weight:600;padding:.1rem .5rem;
                                             border-radius:6px;background:#fffbeb;color:#92400e;
                                             border:1px solid #fcd34d;">
                                    <i class="bi bi-bag" style="font-size:.65rem;"></i> Bawa Pulang
                                </span>
                            @else
                                <span style="font-size:.7rem;font-weight:600;padding:.1rem .5rem;
                                             border-radius:6px;background:#eff6ff;color:#1d4ed8;
                                             border:1px solid #bfdbfe;">
                                    <i class="bi bi-shop" style="font-size:.65rem;"></i> Dine In
                                </span>
                            @endif
                        </div>
                    </div>
                    <span class="price-tag fw-700" style="font-size:.9rem;">
                        Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                    </span>
                </div>
            @endforeach

            <div class="d-flex justify-content-between align-items-center px-4 py-3"
                style="background:#f8f9fa;border-radius:0 0 14px 14px;">
                <span class="fw-700" style="font-weight:700;">Total</span>
                <span class="price-tag fw-700" style="font-size:1.1rem;">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Place order form ── --}}
    <form action="{{ route('public.place.order', ['token' => $token]) }}" method="POST">
        @csrf
        <div class="card mb-3" style="border-radius:14px;">
            <div class="card-body px-4 py-3">
                <label for="notes" class="form-label fw-600 mb-1"
                    style="font-size:.875rem;font-weight:600;">
                    <i class="bi bi-chat-left-text me-1"></i>Catatan
                    <span class="text-muted fw-400" style="font-weight:400;">(opsional)</span>
                </label>
                <textarea class="form-control" id="notes" name="notes" rows="2"
                    style="border-radius:10px;font-size:.9rem;resize:none;"
                    placeholder="Contoh: tidak pedas, es batu sedikit..."></textarea>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                <i class="bi bi-bag-check-fill me-2"></i>Pesan Sekarang
            </button>
        </div>
    </form>

@endsection
