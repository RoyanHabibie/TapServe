<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TapServe Admin')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    @stack('styles')
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark text-white p-3" style="min-width: 250px; min-height: 100vh;">
            <h4 class="text-center mb-4">TapServe</h4>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('admin.menus.index') }}">
                        <i class="bi bi-egg-fried me-2"></i> Menu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('admin.categories.index') }}">
                        <i class="bi bi-tags me-2"></i> Kategori
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('admin.tables.index') }}">
                        <i class="bi bi-table me-2"></i> Meja
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <i class="bi bi-receipt me-2"></i> Order
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <i class="bi bi-people me-2"></i> User
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <i class="bi bi-gear me-2"></i> Settings
                    </a>
                </li>
            </ul>
            <hr class="text-white-50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </div>

        <!-- Page Content -->
        <div class="flex-grow-1">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1">{{ Auth::user()->shop->name ?? 'TapServe' }}</span>
                    <div class="d-flex align-items-center">
                        <span class="me-3">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            <span class="badge bg-secondary ms-2">{{ ucfirst(Auth::user()->role) }}</span>
                        </span>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (diperlukan DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    @stack('scripts')
</body>

</html>
