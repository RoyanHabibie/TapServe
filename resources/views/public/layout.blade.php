<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TapServe') — {{ $shop->name ?? 'TapServe' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --ts-primary:       #e8410a;
            --ts-primary-dark:  #c93508;
            --ts-primary-light: #fff3ef;
            --ts-primary-border:#fbc4b0;
            --bs-body-font-family: 'Inter', system-ui, sans-serif;
            --bs-body-bg: #f8f9fa;
        }

        body { background: #f8f9fa; color: #1e293b; }

        /* ── Navbar ── */
        .ts-navbar {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: .7rem 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .ts-brand {
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--ts-primary) !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .4rem;
        }
        .ts-brand i { font-size: 1.3rem; }

        /* ── Cart button ── */
        .btn-cart {
            background: var(--ts-primary);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: .4rem 1.1rem;
            font-weight: 600;
            font-size: .875rem;
            transition: background .15s;
        }
        .btn-cart:hover, .btn-cart:focus { background: var(--ts-primary-dark); color: #fff; }
        .cart-count {
            position: absolute;
            top: -7px; right: -7px;
            background: #fff;
            color: var(--ts-primary);
            border: 2px solid var(--ts-primary);
            border-radius: 50%;
            width: 20px; height: 20px;
            font-size: 10px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            line-height: 1;
        }

        /* ── Menu card ── */
        .menu-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
            transition: transform .15s, box-shadow .15s;
            overflow: hidden;
            background: #fff;
        }
        .menu-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,.11);
        }
        .menu-img { height: 140px; object-fit: cover; width: 100%; }
        .menu-img-placeholder {
            height: 140px;
            background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
        }

        /* ── Add to cart button (on card) ── */
        .btn-add {
            background: var(--ts-primary);
            color: #fff; border: none;
            border-radius: 50px;
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
            transition: background .15s;
        }
        .btn-add:hover { background: var(--ts-primary-dark); color: #fff; }

        /* ── Section heading ── */
        .section-label {
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--ts-primary);
            margin-bottom: .75rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }

        /* ── Session banner ── */
        .session-banner {
            background: var(--ts-primary-light);
            border: 1px solid var(--ts-primary-border);
            border-radius: 12px;
            padding: .85rem 1rem;
        }

        /* ── Alerts ── */
        .alert { border-radius: 10px; }

        /* ── Tables / Cards ── */
        .card { border-radius: 12px; border: none; box-shadow: 0 1px 4px rgba(0,0,0,.07); }
        .card-header { border-radius: 12px 12px 0 0 !important; }

        /* ── Buttons ── */
        .btn { border-radius: 8px; font-weight: 500; }
        .btn-primary { background: var(--ts-primary); border-color: var(--ts-primary); }
        .btn-primary:hover, .btn-primary:focus { background: var(--ts-primary-dark); border-color: var(--ts-primary-dark); }
        .btn-outline-primary { color: var(--ts-primary); border-color: var(--ts-primary); }
        .btn-outline-primary:hover { background: var(--ts-primary); border-color: var(--ts-primary); }

        /* ── Price ── */
        .price-tag { color: var(--ts-primary); font-weight: 700; }

        /* ── Quantity controls ── */
        .qty-group .btn { border-radius: 6px; padding: .2rem .55rem; }

        /* ── Status badge pill ── */
        .status-pill { border-radius: 50px; font-size: .75rem; padding: .25em .75em; font-weight: 600; }

        /* ── Order status timeline ── */
        .status-step {
            display: flex; align-items: center; flex-direction: column; flex: 1; text-align: center;
        }
        .status-step .dot {
            width: 32px; height: 32px; border-radius: 50%;
            background: #e2e8f0; color: #94a3b8;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem; margin-bottom: .35rem;
            transition: background .3s, color .3s;
        }
        .status-step.done .dot   { background: #22c55e; color: #fff; }
        .status-step.active .dot { background: var(--ts-primary); color: #fff; }
        .status-step .label { font-size: .72rem; color: #64748b; font-weight: 500; }
        .status-step.done .label, .status-step.active .label { color: #1e293b; font-weight: 600; }
        .status-line { flex: 1; height: 2px; background: #e2e8f0; margin-top: -30px; }
        .status-line.done { background: #22c55e; }

        @media (max-width: 575.98px) {
            .menu-img, .menu-img-placeholder { height: 110px; }
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="ts-navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a class="ts-brand" href="{{ route('public.menu', ['token' => $token ?? null]) }}">
                    <i class="bi bi-cup-hot-fill"></i>
                    {{ $shop->name ?? 'TapServe' }}
                </a>
                <div class="d-flex align-items-center gap-2">
                    @if(isset($token) && $token)
                        <a href="{{ route('public.session', ['token' => $token]) }}"
                            class="btn btn-sm btn-outline-secondary rounded-pill d-flex align-items-center gap-1">
                            <i class="bi bi-receipt"></i>
                            <span class="d-none d-sm-inline">Pesanan Saya</span>
                        </a>
                    @endif
                    <a href="{{ route('public.cart', ['token' => $token ?? null]) }}"
                        class="btn btn-cart position-relative d-flex align-items-center gap-1">
                        <i class="bi bi-cart2"></i>
                        <span class="d-none d-sm-inline">Keranjang</span>
                        <span class="cart-count" id="cartCount"
                            style="{{ ($totalItems ?? 0) > 0 ? '' : 'display:none;' }}">{{ $totalItems ?? 0 }}</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-3">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </main>

    <div style="height: 2rem;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>

</html>
