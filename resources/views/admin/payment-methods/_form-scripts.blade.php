<script>
(function () {
    /* ── Icon live preview ── */
    var iconInput   = document.getElementById('icon');
    var iconEl      = document.getElementById('iconPreview');
    var colorInput  = document.getElementById('color');

    function updateIconPreview() {
        var cls = (iconInput.value || 'bi-cash').trim();
        iconEl.className = 'bi ' + cls;
    }

    function updateColorPreview() {
        if (iconEl) iconEl.style.color = colorInput.value;
        if (window.refreshMethodPreview) refreshMethodPreview();
    }

    if (iconInput) {
        iconInput.addEventListener('input', function () {
            updateIconPreview();
            if (window.refreshMethodPreview) refreshMethodPreview();
        });
    }
    if (colorInput) {
        colorInput.addEventListener('input', updateColorPreview);
    }

    /* ── Shortcut: set icon from badge click ── */
    window.setIcon = function (cls) {
        if (iconInput) { iconInput.value = cls; updateIconPreview(); }
        if (window.refreshMethodPreview) refreshMethodPreview();
    };

    /* ── Shortcut: set color from swatch click ── */
    window.setColor = function (hex) {
        if (colorInput) { colorInput.value = hex; updateColorPreview(); }
    };

    /* ── Code auto-preview from name ── */
    var nameInput   = document.getElementById('name');
    var codePreview = document.getElementById('codePreview');

    function slugify(str) {
        return str.toLowerCase()
            .replace(/\s+/g, '_')
            .replace(/[^a-z0-9_]/g, '')
            .replace(/_+/g, '_')
            .replace(/^_|_$/g, '')
            .substring(0, 30);
    }

    if (nameInput && codePreview && !codePreview.value) {
        nameInput.addEventListener('input', function () {
            codePreview.value = slugify(this.value);
            if (window.refreshMethodPreview) refreshMethodPreview();
        });
    }

    /* ── Live preview update ── */
    window.refreshMethodPreview = function () {
        var name  = nameInput  ? nameInput.value  || 'Nama Metode' : '';
        var icon  = iconInput  ? iconInput.value  || 'bi-cash'     : 'bi-cash';
        var color = colorInput ? colorInput.value || '#64748b'      : '#64748b';
        var code  = codePreview ? codePreview.value || '—'         : '—';

        var pName  = document.getElementById('prev-name');
        var pIcon  = document.getElementById('prev-icon');
        var pCode  = document.getElementById('prev-code');
        var pCard  = document.getElementById('prev-card');
        var pBadge = document.getElementById('prev-badge');

        if (pName)  pName.textContent  = name || 'Nama Metode';
        if (pIcon)  { pIcon.className = 'bi ' + icon; pIcon.style.color = color; }
        if (pCode)  pCode.textContent  = code;
        if (pCard)  pCard.style.borderLeftColor = color;
        if (pBadge) {
            pBadge.style.background = color + '18';
            pBadge.style.color = color;
        }
    };

    /* Trigger once on page load */
    if (window.refreshMethodPreview) refreshMethodPreview();
})();
</script>
