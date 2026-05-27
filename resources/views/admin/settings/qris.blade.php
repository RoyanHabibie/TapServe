@extends('layouts.admin')

@section('title', 'Pengaturan QRIS')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-700 mb-1" style="font-weight:700;">Pengaturan QRIS</h4>
            <p class="text-muted small mb-0">
                Gambar QRIS ini akan ditampilkan ke customer saat melakukan pembayaran mandiri.
            </p>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── Upload Form ── --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header py-3 px-4">
                    <span class="fw-600" style="font-weight:600;font-size:.875rem;">
                        <i class="bi bi-upload me-2" style="color:#3b82f6;"></i>Upload Gambar QRIS
                    </span>
                </div>
                <div class="card-body px-4 py-4">
                    <form action="{{ route('admin.settings.qris.update') }}" method="POST"
                        enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        {{-- Drop zone --}}
                        <div id="dropZone"
                            class="border-2 border-dashed rounded-3 text-center p-4 mb-3"
                            style="border: 2px dashed #cbd5e1; cursor:pointer; transition: border-color .2s, background .2s;"
                            onclick="document.getElementById('qris_image').click()">
                            <div id="dropContent">
                                <i class="bi bi-qr-code" style="font-size:2.5rem;color:#94a3b8;"></i>
                                <p class="mt-2 mb-1 fw-600" style="font-weight:600;color:#475569;">
                                    Klik atau seret gambar ke sini
                                </p>
                                <p class="text-muted small mb-0">PNG, JPG, WEBP — maks. 2 MB</p>
                            </div>
                            <img id="previewImg" src="" alt="Preview"
                                style="display:none;max-width:100%;max-height:280px;border-radius:8px;object-fit:contain;">
                        </div>

                        <input type="file" id="qris_image" name="qris_image"
                            accept="image/png,image/jpeg,image/webp" class="d-none"
                            onchange="previewImage(this)">

                        @error('qris_image')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <i class="bi bi-cloud-upload me-1"></i>Simpan QRIS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Current QRIS ── --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header py-3 px-4">
                    <span class="fw-600" style="font-weight:600;font-size:.875rem;">
                        <i class="bi bi-eye me-2" style="color:#16a34a;"></i>QRIS Saat Ini
                    </span>
                </div>
                <div class="card-body px-4 py-4 d-flex flex-column align-items-center justify-content-center">
                    @if($shop->qris_image)
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $shop->qris_image) }}"
                                alt="QRIS {{ $shop->name }}"
                                style="max-width:100%;max-height:300px;border-radius:12px;object-fit:contain;
                                       box-shadow:0 4px 16px rgba(0,0,0,.1);">
                            <p class="text-muted small mt-3 mb-3">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                QRIS aktif — customer dapat scan untuk bayar mandiri
                            </p>
                            <form action="{{ route('admin.settings.qris.destroy') }}" method="POST"
                                id="deleteQrisForm">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash me-1"></i>Hapus QRIS
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-qr-code" style="font-size:3.5rem;color:#e2e8f0;"></i>
                            <p class="mt-3 text-muted small mb-0">
                                Belum ada gambar QRIS yang diunggah.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ── Info panel ── --}}
    <div class="card mt-4" style="background:#eff6ff;border:1px solid #bfdbfe;">
        <div class="card-body px-4 py-3">
            <div class="d-flex gap-3">
                <i class="bi bi-info-circle-fill" style="color:#3b82f6;font-size:1.1rem;flex-shrink:0;margin-top:.1rem;"></i>
                <div class="small" style="color:#1e40af;">
                    <strong>Cara kerja QRIS mandiri:</strong><br>
                    1. Customer menekan <em>"Minta Bayar"</em> di halaman ringkasan pesanan.<br>
                    2. Gambar QRIS ini otomatis ditampilkan ke customer — customer scan & bayar langsung.<br>
                    3. Kasir mengkonfirmasi pembayaran di dashboard kasir.
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function previewImage(input) {
        var file = input.files[0];
        if (!file) return;

        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('dropContent').style.display = 'none';
            var img = document.getElementById('previewImg');
            img.src = e.target.result;
            img.style.display = 'block';
            document.getElementById('submitBtn').disabled = false;
        };
        reader.readAsDataURL(file);
    }

    // Drag & drop
    var zone = document.getElementById('dropZone');
    zone.addEventListener('dragover', function (e) {
        e.preventDefault();
        zone.style.borderColor = '#3b82f6';
        zone.style.background = '#eff6ff';
    });
    zone.addEventListener('dragleave', function () {
        zone.style.borderColor = '#cbd5e1';
        zone.style.background = '';
    });
    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.style.borderColor = '#cbd5e1';
        zone.style.background = '';
        var file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            var dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('qris_image').files = dt.files;
            previewImage(document.getElementById('qris_image'));
        }
    });

    // Confirm delete
    document.getElementById('deleteQrisForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Hapus QRIS?',
            text: 'Gambar QRIS akan dihapus dan tidak bisa digunakan customer.',
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
