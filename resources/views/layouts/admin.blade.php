<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TapServe') — Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --sb-bg:      #1e293b;
            --sb-hover:   #334155;
            --sb-active:  #3b82f6;
            --sb-text:    #94a3b8;
            --sb-width:   240px;
            --topbar-h:   60px;
            --bs-body-font-family: 'Inter', system-ui, sans-serif;
        }

        * { box-sizing: border-box; }
        body { background: #f1f5f9; margin: 0; font-family: 'Inter', system-ui, sans-serif; }

        /* ── Sidebar ── */
        #sidebar {
            width: var(--sb-width);
            min-height: 100vh;
            background: var(--sb-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0; top: 0;
            z-index: 1040;
            transition: transform .25s;
        }
        .sb-brand {
            height: var(--topbar-h);
            display: flex; align-items: center;
            padding: 0 1.25rem;
            font-size: 1.1rem; font-weight: 700; color: #fff;
            border-bottom: 1px solid #334155;
            text-decoration: none;
            gap: .5rem;
        }
        .sb-brand span { color: #3b82f6; }
        .sb-section-label {
            font-size: .65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .1em;
            color: #475569; padding: .9rem 1.25rem .3rem;
        }
        .sb-nav-link {
            display: flex; align-items: center; gap: .65rem;
            padding: .55rem 1rem;
            margin: 1px .5rem;
            border-radius: 8px;
            color: var(--sb-text); font-size: .85rem; font-weight: 500;
            text-decoration: none;
            transition: background .15s, color .15s;
        }
        .sb-nav-link i { font-size: .95rem; width: 18px; flex-shrink: 0; }
        .sb-nav-link:hover  { background: var(--sb-hover); color: #e2e8f0; }
        .sb-nav-link.active { background: var(--sb-active); color: #fff; }
        .sb-footer {
            padding: 1rem;
            border-top: 1px solid #334155;
            margin-top: auto;
        }

        /* ── Topbar ── */
        #topbar {
            position: fixed;
            top: 0; left: var(--sb-width); right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            z-index: 1030;
            display: flex; align-items: center;
            padding: 0 1.5rem;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .topbar-title { font-weight: 600; font-size: .95rem; color: #1e293b; }
        .avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: #3b82f6; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .85rem; flex-shrink: 0;
        }

        /* ── Main ── */
        #main-wrap {
            margin-left: var(--sb-width);
            padding-top: var(--topbar-h);
            min-height: 100vh;
        }
        .content-inner { padding: 1.75rem; }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; }

        /* ── Stat card ── */
        .stat-icon {
            width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem; flex-shrink: 0;
        }

        /* ── Buttons ── */
        .btn { border-radius: 8px; font-weight: 500; font-size: .875rem; }
        .btn-sm { font-size: .8rem; }

        /* ── Tables ── */
        .table > :not(caption) > * > * { padding: .65rem 1rem; vertical-align: middle; }
        .table thead th { font-size: .78rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #64748b; border-bottom: 2px solid #e2e8f0; }

        /* ── Form ── */
        .form-control, .form-select { border-radius: 8px; font-size: .875rem; }
        .form-label { font-size: .875rem; font-weight: 600; color: #374151; margin-bottom: .35rem; }

        /* ── DataTables overrides ── */
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select { border-radius: 8px; font-size: .85rem; }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate { font-size: .82rem; }

        /* ── Mobile ── */
        @media (max-width: 767.98px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #topbar { left: 0; padding: 0 1rem; }
            #main-wrap { margin-left: 0; }
            .content-inner { padding: 1rem; }
            .sb-overlay {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,.4);
                z-index: 1039;
            }
            .sb-overlay.open { display: block; }
        }
    </style>
    @stack('styles')
</head>

<body>

    {{-- Mobile overlay --}}
    <div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

    {{-- ── Sidebar ── --}}
    <div id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sb-brand">
            <i class="bi bi-cup-hot-fill" style="color:#3b82f6;"></i>
            <span>Tap</span>Serve
        </a>

        <nav class="flex-grow-1 py-2" style="overflow-y:auto;">
            <div class="sb-section-label">Utama</div>
            <a href="{{ route('admin.dashboard') }}"
                class="sb-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.reports.index') }}"
                class="sb-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i> Laporan
            </a>

            <div class="sb-section-label mt-2">Katalog</div>
            <a href="{{ route('admin.menus.index') }}"
                class="sb-nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                <i class="bi bi-egg-fried"></i> Menu
            </a>
            <a href="{{ route('admin.categories.index') }}"
                class="sb-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Kategori
            </a>
            <a href="{{ route('admin.tables.index') }}"
                class="sb-nav-link {{ request()->routeIs('admin.tables.*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3"></i> Meja
            </a>

            <div class="sb-section-label mt-2">Operasional</div>
            <a href="{{ route('admin.sessions.index') }}"
                class="sb-nav-link {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i> Sesi Meja
            </a>

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'owner')
            <div class="sb-section-label mt-2">Pengaturan</div>
            <a href="{{ route('admin.users.index') }}"
                class="sb-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Kelola User
            </a>
            @endif
        </nav>

        <div class="sb-footer">
            <div class="d-flex align-items-center gap-2 mb-2 px-1">
                <div class="avatar" style="width:28px;height:28px;font-size:.75rem;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div style="line-height:1.2;min-width:0;">
                    <div class="text-white" style="font-size:.78rem;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ Auth::user()->name }}
                    </div>
                    <div style="font-size:.7rem;color:#64748b;">{{ ucfirst(Auth::user()->role) }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm w-100"
                    style="background:#334155;color:#94a3b8;border:none;">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </button>
            </form>
        </div>
    </div>

    {{-- ── Topbar ── --}}
    <div id="topbar">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm d-md-none" onclick="openSidebar()"
                style="border:1px solid #e2e8f0;padding:.3rem .5rem;">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="topbar-title">{{ Auth::user()->shop->name ?? 'TapServe' }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-secondary" style="border-radius:6px;">{{ ucfirst(Auth::user()->role) }}</span>
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <span class="d-none d-sm-block text-muted" style="font-size:.82rem;">{{ Auth::user()->name }}</span>
        </div>
    </div>

    {{-- ── Page Content ── --}}
    <div id="main-wrap">
        <div class="content-inner">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert"
                    style="border-radius:10px;">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert"
                    style="border-radius:10px;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sbOverlay').classList.add('open');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sbOverlay').classList.remove('open');
        }
    </script>
    @stack('scripts')
</body>

</html>
