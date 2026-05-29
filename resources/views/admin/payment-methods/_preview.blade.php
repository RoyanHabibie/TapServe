@php
    $m     = $method ?? null;
    $pName  = old('name',       $m?->name  ?? '');
    $pIcon  = old('icon',       $m?->icon  ?? 'bi-cash');
    $pColor = old('color',      $m?->color ?? '#64748b');
    $pCode  = old('name',       $m?->code  ?? '');
@endphp

<div class="card">
    <div class="card-body px-4 py-4">
        <h6 class="fw-600 mb-3" style="font-weight:600;font-size:.85rem;
            text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;">
            Pratinjau
        </h6>

        {{-- Card preview (mimics index card) --}}
        <div id="prev-card" class="card mb-4"
            style="border-left:4px solid {{ $pColor }};transition:border-color .2s;">
            <div class="card-body px-3 py-3">
                <div class="d-flex align-items-center gap-3">
                    <div id="prev-badge"
                        style="width:44px;height:44px;border-radius:10px;flex-shrink:0;
                               background:{{ $pColor }}18;
                               display:flex;align-items:center;justify-content:center;
                               transition:background .2s;">
                        <i id="prev-icon" class="bi {{ $pIcon }}"
                            style="font-size:1.3rem;color:{{ $pColor }};transition:color .2s;"></i>
                    </div>
                    <div>
                        <div id="prev-name" class="fw-600" style="font-weight:600;font-size:.9rem;">
                            {{ $pName ?: 'Nama Metode' }}
                        </div>
                        <div class="text-muted" style="font-size:.72rem;">
                            Kode: <code id="prev-code" style="font-size:.68rem;">{{ $pCode ?: '—' }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment form preview --}}
        <p class="text-muted mb-2" style="font-size:.78rem;text-transform:uppercase;
            letter-spacing:.05em;font-weight:600;">Di form pembayaran kasir:</p>
        <div class="border rounded p-3" style="background:#f8fafc;">
            <div class="d-flex align-items-center gap-2">
                <i id="prev-icon-2" class="bi {{ $pIcon }}"
                    style="font-size:1rem;color:{{ $pColor }};"></i>
                <span id="prev-name-2" style="font-size:.9rem;">
                    {{ $pName ?: 'Nama Metode' }}
                </span>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    /* Keep secondary preview icons in sync */
    var orig = window.refreshMethodPreview;
    window.refreshMethodPreview = function () {
        if (orig) orig();
        var iconInput  = document.getElementById('icon');
        var colorInput = document.getElementById('color');
        var nameInput  = document.getElementById('name');

        var icon  = iconInput  ? iconInput.value  || 'bi-cash' : 'bi-cash';
        var color = colorInput ? colorInput.value || '#64748b'  : '#64748b';
        var name  = nameInput  ? nameInput.value  || 'Nama Metode' : 'Nama Metode';

        var i2 = document.getElementById('prev-icon-2');
        var n2 = document.getElementById('prev-name-2');
        if (i2) { i2.className = 'bi ' + icon; i2.style.color = color; }
        if (n2) n2.textContent = name;
    };
})();
</script>
