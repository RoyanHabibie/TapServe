@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    <h4 class="fw-700 mb-1" style="font-weight:700;">Dashboard</h4>
    <p class="text-muted small mb-4">Selamat datang, {{ Auth::user()->name }}.</p>

    {{-- ── Stat cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dcfce7;">
                        <i class="bi bi-currency-dollar" style="color:#16a34a;"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Pendapatan Hari Ini</div>
                        <div class="fw-700" style="font-size:1.2rem;font-weight:700;color:#1e293b;">
                            Rp {{ number_format($revenue_today, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;">
                        <i class="bi bi-bag-check" style="color:#2563eb;"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Pesanan Hari Ini</div>
                        <div class="fw-700" style="font-size:1.2rem;font-weight:700;color:#1e293b;">
                            {{ $orders_today }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fef3c7;">
                        <i class="bi bi-grid-3x3" style="color:#d97706;"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Sesi Aktif</div>
                        <div class="fw-700" style="font-size:1.2rem;font-weight:700;color:#1e293b;">
                            {{ $active_sessions }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Popular menus ── --}}
    <div class="card">
        <div class="card-header py-3 px-4">
            <span class="fw-600" style="font-weight:600;font-size:.875rem;">
                <i class="bi bi-trophy me-2" style="color:#d97706;"></i>Menu Terpopuler
            </span>
        </div>
        <div class="card-body p-0">
            @if ($popular_menus->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Menu</th>
                                <th class="text-end">Total Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($popular_menus as $i => $menu)
                                <tr>
                                    <td class="text-muted" style="width:40px;">{{ $i + 1 }}</td>
                                    <td class="fw-500" style="font-weight:500;">{{ $menu->name }}</td>
                                    <td class="text-end">
                                        <span class="badge rounded-pill px-3"
                                            style="background:#dbeafe;color:#1e40af;">
                                            {{ $menu->total_qty }} pcs
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size:2.5rem;color:#cbd5e1;"></i>
                    <p class="mt-2 mb-0 small">Belum ada data penjualan.</p>
                </div>
            @endif
        </div>
    </div>

@endsection
