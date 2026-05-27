<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Meja {{ $table->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f1f5f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }

        /* ── Controls (hidden on print) ── */
        .controls {
            display: flex; gap: .75rem; margin-bottom: 1.5rem;
        }
        .btn-back {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .5rem 1rem; border-radius: 8px;
            background: #fff; border: 1px solid #e2e8f0;
            color: #475569; font-size: .875rem; font-weight: 500;
            text-decoration: none; cursor: pointer;
        }
        .btn-print {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .5rem 1.25rem; border-radius: 8px;
            background: #3b82f6; border: none;
            color: #fff; font-size: .875rem; font-weight: 600;
            cursor: pointer;
        }
        .btn-print:hover { background: #2563eb; }

        /* ── QR Card ── */
        .qr-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            padding: 2.5rem 2rem;
            text-align: center;
            width: 320px;
        }
        .shop-name {
            font-size: .75rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .1em;
            color: #94a3b8; margin-bottom: 1.25rem;
        }
        .table-name {
            font-size: 1.75rem; font-weight: 700;
            color: #1e293b; margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        #qrcode {
            display: flex; justify-content: center;
            margin-bottom: 1.5rem;
        }
        #qrcode canvas, #qrcode img { border-radius: 8px; }
        .scan-hint {
            font-size: .8rem; color: #64748b;
            margin-bottom: .75rem;
        }
        .table-url {
            font-size: .65rem; color: #94a3b8;
            word-break: break-all;
            background: #f8fafc; border-radius: 6px;
            padding: .4rem .6rem;
        }
        .divider {
            border: none; border-top: 1px dashed #e2e8f0;
            margin: 1.25rem 0;
        }
        .capacity-badge {
            display: inline-flex; align-items: center; gap: .3rem;
            font-size: .75rem; font-weight: 600;
            color: #475569; background: #f1f5f9;
            border-radius: 50px; padding: .3rem .75rem;
        }

        /* ── Print styles ── */
        @media print {
            body { background: #fff; padding: 0; min-height: unset; justify-content: flex-start; }
            .controls { display: none !important; }
            .qr-card { box-shadow: none; border: 1px solid #e2e8f0; margin: 0 auto; }
            @page { margin: 1cm; size: A5 portrait; }
        }
    </style>
</head>
<body>

    <div class="controls">
        <a href="{{ route('admin.tables.index') }}" class="btn-back">
            ← Kembali
        </a>
        <a href="{{ route('admin.tables.qrcodes') }}" class="btn-back">
            Cetak Semua
        </a>
        <button class="btn-print" onclick="window.print()">
            🖨 Cetak QR
        </button>
    </div>

    <div class="qr-card">
        <div class="shop-name">{{ Auth::user()->shop->name ?? 'TapServe' }}</div>
        <div class="table-name">Meja {{ $table->name }}</div>

        <div id="qrcode"></div>

        <p class="scan-hint">Scan QR untuk memesan</p>
        <div class="table-url">{{ $url }}</div>

        <hr class="divider">
        <span class="capacity-badge">
            👥 Kapasitas {{ $table->capacity }} orang
        </span>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById('qrcode'), {
            text: '{{ $url }}',
            width: 200,
            height: 200,
            colorDark: '#1e293b',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>
