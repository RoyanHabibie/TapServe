@extends('layouts.admin')

@section('title', 'Profil Toko')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-700 mb-1" style="font-weight:700;">Profil Toko</h4>
            <p class="text-muted small mb-0">Kelola informasi dan identitas toko Anda.</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.shop.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">

            {{-- ── Info toko ── --}}
            <div class="col-12 col-lg-7">
                <div class="card h-100">
                    <div class="card-header py-3 px-4">
                        <span class="fw-600" style="font-weight:600;font-size:.875rem;">
                            <i class="bi bi-shop me-2" style="color:#3b82f6;"></i>Informasi Toko
                        </span>
                    </div>
                    <div class="card-body px-4 py-4">

                        {{-- Nama --}}
                        <div class="mb-4">
                            <label class="form-label" for="name">Nama Toko <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $shop->name) }}"
                                placeholder="Contoh: Kopi Santai" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Slug (read-only, info) --}}
                        <div class="mb-4">
                            <label class="form-label">Slug / URL Toko</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted"
                                    style="font-size:.8rem;background:#f8fafc;border-right:none;">
                                    tapserve.id/
                                </span>
                                <input type="text" class="form-control" id="slugPreview"
                                    value="{{ $shop->slug }}" disabled
                                    style="background:#f8fafc;color:#64748b;border-left:none;">
                            </div>
                            <div class="form-text">Diperbarui otomatis saat nama toko diubah.</div>
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-4">
                            <label class="form-label" for="address">Alamat</label>
                            <textarea id="address" name="address" rows="3"
                                class="form-control @error('address') is-invalid @enderror"
                                placeholder="Jl. Contoh No. 123, Kota">{{ old('address', $shop->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            {{-- Telepon --}}
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="phone">Nomor Telepon</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f8fafc;">
                                        <i class="bi bi-telephone text-muted" style="font-size:.85rem;"></i>
                                    </span>
                                    <input type="text" id="phone" name="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', $shop->phone) }}"
                                        placeholder="08xxxxxxxxxx">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="email">Email Toko</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f8fafc;">
                                        <i class="bi bi-envelope text-muted" style="font-size:.85rem;"></i>
                                    </span>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $shop->email) }}"
                                        placeholder="toko@email.com">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-white px-4 py-3" style="border-top:1px solid #f1f5f9;">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-floppy me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── Logo toko ── --}}
            <div class="col-12 col-lg-5">
                <div class="card">
                    <div class="card-header py-3 px-4">
                        <span class="fw-600" style="font-weight:600;font-size:.875rem;">
                            <i class="bi bi-image me-2" style="color:#16a34a;"></i>Logo Toko
                        </span>
                    </div>
                    <div class="card-body px-4 py-4">

                        {{-- Current logo preview --}}
                        @if($shop->logo)
                            <div class="text-center mb-3">
                                <img src="{{ asset('storage/' . $shop->logo) }}"
                                    alt="Logo {{ $shop->name }}" id="currentLogo"
                                    style="max-width:160px;max-height:160px;object-fit:contain;
                                           border-radius:12px;border:1px solid #e2e8f0;
                                           box-shadow:0 2px 8px rgba(0,0,0,.06);">
                            </div>
                        @endif

                        {{-- Drop zone --}}
                        <div id="dropZone"
                            class="border-2 border-dashed rounded-3 text-center p-4 mb-3"
                            style="border:2px dashed #cbd5e1;cursor:pointer;transition:border-color .2s,background .2s;"
                            onclick="document.getElementById('logo').click()">
                            <div id="dropContent">
                                <i class="bi bi-image" style="font-size:2.2rem;color:#94a3b8;"></i>
                                <p class="mt-2 mb-1 fw-600" style="font-weight:600;color:#475569;font-size:.875rem;">
                                    {{ $shop->logo ? 'Ganti logo' : 'Upload logo' }}
                                </p>
                                <p class="text-muted small mb-0">PNG, JPG, WEBP — maks. 2 MB</p>
                            </div>
                            <img id="previewImg" src="" alt="Preview"
                                style="display:none;max-width:100%;max-height:200px;
                                       border-radius:8px;object-fit:contain;">
                        </div>

                        <input type="file" id="logo" name="logo"
                            accept="image/png,image/jpeg,image/webp"
                            class="d-none" onchange="previewLogo(this)">

                        @error('logo')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror

                        {{-- Delete current logo --}}
                        @if($shop->logo)
                            <form action="{{ route('admin.settings.shop.logo.destroy') }}" method="POST"
                                id="deleteLogoForm">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="bi bi-trash me-1"></i>Hapus Logo
                                </button>
                            </form>
                        @endif

                    </div>
                </div>

                {{-- ── Info card ── --}}
                <div class="card mt-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                    <div class="card-body px-4 py-3">
                        <div class="d-flex gap-3">
                            <i class="bi bi-info-circle-fill"
                                style="color:#16a34a;font-size:1.1rem;flex-shrink:0;margin-top:.1rem;"></i>
                            <div class="small" style="color:#166534;">
                                <strong>Informasi ini ditampilkan di:</strong><br>
                                • Halaman menu publik (nama toko)<br>
                                • Topbar dashboard admin<br>
                                • Laporan dan struk pembayaran
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

@endsection

@push('scripts')
<script>
    // Slug auto-preview dari nama
    document.getElementById('name').addEventListener('input', function () {
        var slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-');
        document.getElementById('slugPreview').value = slug;
    });

    // Logo preview
    function previewLogo(input) {
        if (!input.files[0]) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('dropContent').style.display = 'none';
            var img = document.getElementById('previewImg');
            img.src = e.target.result;
            img.style.display = 'block';
            var cur = document.getElementById('currentLogo');
            if (cur) cur.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }

    // Drag & drop
    var zone = document.getElementById('dropZone');
    zone.addEventListener('dragover', function (e) {
        e.preventDefault();
        zone.style.borderColor = '#3b82f6';
        zone.style.background  = '#eff6ff';
    });
    zone.addEventListener('dragleave', function () {
        zone.style.borderColor = '#cbd5e1';
        zone.style.background  = '';
    });
    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.style.borderColor = '#cbd5e1';
        zone.style.background  = '';
        var file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            var dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('logo').files = dt.files;
            previewLogo(document.getElementById('logo'));
        }
    });

    // Confirm hapus logo
    document.getElementById('deleteLogoForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Hapus Logo?',
            text: 'Logo toko akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
        }).then(function (result) {
            if (result.isConfirmed) form.submit();
        });
    });
</script>
@endpush
