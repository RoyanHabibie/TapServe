@extends('layouts.admin')

@section('title', 'Metode Pembayaran')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-700 mb-1" style="font-weight:700;">Metode Pembayaran</h4>
            <p class="text-muted small mb-0">Kelola jenis pembayaran yang tersedia di kasir.</p>
        </div>
        <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Metode
        </a>
    </div>

    <div class="row g-3">
        @forelse($methods as $method)
            <div class="col-12 col-md-6 col-xl-4" id="method-row-{{ $method->id }}">
                <div class="card h-100" style="border-left:4px solid {{ $method->color }};
                     opacity:{{ $method->is_active ? '1' : '.55' }};transition:opacity .2s;">
                    <div class="card-body px-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            {{-- Icon preview --}}
                            <div style="width:48px;height:48px;border-radius:12px;flex-shrink:0;
                                        background:{{ $method->color }}18;
                                        display:flex;align-items:center;justify-content:center;">
                                <i class="bi {{ $method->icon }}"
                                    style="font-size:1.4rem;color:{{ $method->color }};"></i>
                            </div>

                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-600" style="font-weight:600;font-size:.95rem;">
                                    {{ $method->name }}
                                </div>
                                <div class="text-muted" style="font-size:.75rem;">
                                    Kode: <code style="font-size:.72rem;">{{ $method->code }}</code>
                                    · Urutan: {{ $method->sort_order }}
                                </div>
                            </div>

                            {{-- Active toggle --}}
                            <div class="form-check form-switch mb-0 flex-shrink-0" style="padding-left:2.5rem;">
                                <input class="form-check-input toggle-active" type="checkbox"
                                    role="switch"
                                    id="toggle-{{ $method->id }}"
                                    data-id="{{ $method->id }}"
                                    {{ $method->is_active ? 'checked' : '' }}
                                    style="width:2.2rem;height:1.2rem;cursor:pointer;">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white px-4 py-2 d-flex gap-2"
                        style="border-top:1px solid #f1f5f9;">
                        <a href="{{ route('admin.payment-methods.edit', $method->id) }}"
                            class="btn btn-sm btn-outline-secondary flex-fill">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete flex-fill"
                            data-id="{{ $method->id }}" data-name="{{ $method->name }}">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="bi bi-credit-card" style="font-size:3rem;color:#cbd5e1;"></i>
                        <p class="mt-3 mb-3">Belum ada metode pembayaran.</p>
                        <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Sekarang
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Delete forms (hidden) --}}
    @foreach($methods as $method)
        <form id="delete-form-{{ $method->id }}"
            action="{{ route('admin.payment-methods.destroy', $method->id) }}"
            method="POST" class="d-none">
            @csrf @method('DELETE')
        </form>
    @endforeach

@endsection

@push('scripts')
<script>
    var toggleUrl = '{{ rtrim(url('/admin/payment-methods'), '/') }}';

    /* ── Toggle active/inactive ── */
    document.querySelectorAll('.toggle-active').forEach(function (el) {
        el.addEventListener('change', function () {
            var id   = this.dataset.id;
            var card = document.getElementById('method-row-' + id);
            fetch(toggleUrl + '/' + id + '/toggle', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                card.querySelector('.card').style.opacity = data.is_active ? '1' : '.55';
            });
        });
    });

    /* ── Delete with confirm ── */
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id   = this.dataset.id;
            var name = this.dataset.name;
            Swal.fire({
                title: 'Hapus "' + name + '"?',
                text: 'Metode pembayaran ini akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then(function (result) {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    });
</script>
@endpush
