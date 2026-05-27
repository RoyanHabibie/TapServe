<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Semua Meja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f1f5f9;
            padding: 1.5rem;
        }

        /* ── Controls ── */
        .controls {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.5rem; flex-wrap: wrap; gap: .75rem;
        }
        .controls-left { display: flex; gap: .75rem; align-items: center; }
        .page-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
        .page-sub   { font-size: .82rem; color: #64748b; }
        .btn-back {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .5rem 1rem; border-radius: 8px;
            background: #fff; border: 1px solid #e2e8f0;
            color: #475569; font-size: .875rem; font-weight: 500;
            text-decoration: none;
        }
        .btn-print {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .5rem 1.5rem; border-radius: 8px;
            background: #3b82f6; border: none;
            color: #fff; font-size: .875rem; font-weight: 600;
            cursor: pointer;
        }
        .btn-print:hover { background: #2563eb; }

        /* ── Grid ── */
        .qr-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.25rem;
        }

        /* ── QR Card ── */
        .qr-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
            padding: 1.5rem 1.25rem;
            text-align: center;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .shop-name {
            font-size: .65rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .1em;
            color: #94a3b8; margin-bottom: .75rem;
        }
        .table-name {
            font-size: 1.25rem; font-weight: 700;
            color: #1e293b; margin-bottom: 1rem;
        }
        .qr-wrap {
            display: flex; justify-content: center;
            margin-bottom: 1rem;
        }
        .qr-wrap canvas, .qr-wrap img { border-radius: 6px; }
        .scan-hint {
            font-size: .72rem; color: #64748b; margin-bottom: .5rem;
        }
        .table-url {
            font-size: .58rem; color: #94a3b8;
            word-break: break-all;
            background: #f8fafc; border-radius: 4px;
            padding: .3rem .5rem;
        }
        .divider {
            border: none; border-top: 1px dashed #e2e8f0;
            margin: .9rem 0;
        }
        .capacity-badge {
            font-size: .68rem; font-weight: 600;
            color: #475569;
        }

        /* ── Print ── */
        @media print {
            body { background: #fff; padding: 0; }
            .controls { display: none !important; }
            .qr-grid { grid-template-columns: repeat(3, 1fr); gap: .75rem; }
            .qr-card { box-shadow: none; border: 1px solid #e2e8f0; border-radius: 8px; }
            @page { margin: 1cm; size: A4 portrait; }
        }
    </style>
</head>
<body>

    <div class="controls">
        <div class="controls-left">
            <a href="{{ route('admin.tables.index') }}" class="btn-back">← Kembali</a>
            <div>
                <div class="page-title">Cetak QR Semua Meja</div>
                <div class="page-sub">{{ $tables->count() }} meja · {{ Auth::user()->shop->name ?? 'TapServe' }}</div>
            </div>
        </div>
        <button class="btn-print" onclick="window.print()">
            🖨 Cetak Semua
        </button>
    </div>

    <div class="qr-grid" id="qrGrid">
        @foreach($tables as $table)
            @php $url = route('public.menu', ['token' => $table->token]); @endphp
            <div class="qr-card">
                <div class="shop-name">{{ Auth::user()->shop->name ?? 'TapServe' }}</div>
                <div class="table-name">Meja {{ $table->name }}</div>
                <div class="qr-wrap" id="qr-{{ $table->id }}"></div>
                <p class="scan-hint">Scan untuk memesan</p>
                <div class="table-url">{{ $url }}</div>
                <hr class="divider">
                <span class="capacity-badge">👥 {{ $table->capacity }} orang</span>
            </div>
        @endforeach
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        var tables = @json($tables->map(fn($t) => [
            'id'    => $t->id,
            'url'   => route('public.menu', ['token' => $t->token]),
        ]));

        tables.forEach(function (table) {
            new QRCode(document.getElementById('qr-' + table.id), {
                text: table.url,
                width: 160,
                height: 160,
                colorDark: '#1e293b',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        });
    </script>
</body>
</html>
