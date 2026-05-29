@php $m = $method ?? null; @endphp

{{-- Nama --}}
<div class="mb-4">
    <label class="form-label" for="name">Nama Metode <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $m?->name) }}"
        placeholder="Contoh: Kartu Debit, Dana, OVO"
        required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Kode (read-only, auto) --}}
<div class="mb-4">
    <label class="form-label">Kode Internal</label>
    <input type="text" id="codePreview" class="form-control"
        value="{{ old('name', $m?->code) }}"
        disabled style="background:#f8fafc;color:#64748b;">
    <div class="form-text">Dibuat otomatis dari nama, digunakan di laporan pembayaran.</div>
</div>

{{-- Icon --}}
<div class="mb-4">
    <label class="form-label" for="icon">
        Ikon Bootstrap Icons <span class="text-danger">*</span>
    </label>
    <div class="input-group">
        <span class="input-group-text" id="iconPreviewBox"
            style="width:42px;justify-content:center;background:#f8fafc;">
            <i id="iconPreview" class="bi {{ old('icon', $m?->icon ?? 'bi-cash') }}"
                style="font-size:1.2rem;color:{{ old('color', $m?->color ?? '#64748b') }};"></i>
        </span>
        <input type="text" id="icon" name="icon"
            class="form-control @error('icon') is-invalid @enderror"
            value="{{ old('icon', $m?->icon ?? 'bi-cash') }}"
            placeholder="bi-cash" required>
        @error('icon')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-text">
        Cari nama ikon di
        <a href="https://icons.getbootstrap.com" target="_blank" rel="noopener">icons.getbootstrap.com</a>.
        Contoh populer:
        <span class="badge bg-light text-dark" style="cursor:pointer;" onclick="setIcon('bi-cash')">bi-cash</span>
        <span class="badge bg-light text-dark" style="cursor:pointer;" onclick="setIcon('bi-qr-code-scan')">bi-qr-code-scan</span>
        <span class="badge bg-light text-dark" style="cursor:pointer;" onclick="setIcon('bi-bank')">bi-bank</span>
        <span class="badge bg-light text-dark" style="cursor:pointer;" onclick="setIcon('bi-wallet2')">bi-wallet2</span>
        <span class="badge bg-light text-dark" style="cursor:pointer;" onclick="setIcon('bi-credit-card')">bi-credit-card</span>
        <span class="badge bg-light text-dark" style="cursor:pointer;" onclick="setIcon('bi-phone')">bi-phone</span>
    </div>
</div>

{{-- Warna --}}
<div class="mb-4">
    <label class="form-label" for="color">Warna Ikon <span class="text-danger">*</span></label>
    <div class="d-flex align-items-center gap-3">
        <input type="color" id="color" name="color"
            class="form-control form-control-color @error('color') is-invalid @enderror"
            value="{{ old('color', $m?->color ?? '#64748b') }}"
            style="width:56px;height:38px;padding:.2rem;cursor:pointer;"
            required>
        <div class="d-flex gap-2 flex-wrap">
            @foreach(['#16a34a','#7c3aed','#2563eb','#d97706','#e11d48','#0891b2','#64748b','#1e293b'] as $c)
                <span onclick="setColor('{{ $c }}')"
                    style="display:inline-block;width:28px;height:28px;border-radius:8px;
                           background:{{ $c }};cursor:pointer;border:2px solid rgba(0,0,0,.1);"
                    title="{{ $c }}"></span>
            @endforeach
        </div>
        @error('color')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Urutan --}}
<div class="mb-2">
    <label class="form-label" for="sort_order">Urutan Tampil</label>
    <input type="number" id="sort_order" name="sort_order"
        class="form-control @error('sort_order') is-invalid @enderror"
        value="{{ old('sort_order', $m?->sort_order ?? 0) }}"
        min="0" max="255" style="width:100px;">
    <div class="form-text">Angka lebih kecil tampil lebih awal.</div>
    @error('sort_order')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
