<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TapServe')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .menu-item {
            transition: 0.2s;
        }

        .menu-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .cart-badge {
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -10px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('public.menu', ['token' => $token ?? null]) }}">
                {{ $shop->name ?? 'TapServe' }}
            </a>
            <div class="ms-auto d-flex gap-2">
                @if(isset($token) && $token)
                    <a href="{{ route('public.session', ['token' => $token]) }}"
                        class="btn btn-outline-light btn-sm">
                        <i class="bi bi-receipt"></i> Pesanan Saya
                    </a>
                @endif
                <a href="{{ route('public.cart', ['token' => $token ?? null]) }}"
                    class="btn btn-outline-light position-relative">
                    <i class="bi bi-cart"></i> Keranjang
                    <span class="cart-count" id="cartCount"
                        style="{{ ($totalItems ?? 0) > 0 ? '' : 'display:none;' }}">
                        {{ $totalItems ?? 0 }}
                    </span>
                </a>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>

</html>
