@extends('layouts.admin')

@section('title', 'Tambah Metode Pembayaran')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.payment-methods.index') }}"
            class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-700 mb-0" style="font-weight:700;">Tambah Metode Pembayaran</h4>
            <p class="text-muted small mb-0">Metode baru akan langsung tersedia di form kasir.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-body px-4 py-4">
                    <form action="{{ route('admin.payment-methods.store') }}" method="POST">
                        @csrf

                        @include('admin.payment-methods._form')

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-floppy me-1"></i>Simpan
                            </button>
                            <a href="{{ route('admin.payment-methods.index') }}"
                                class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            @include('admin.payment-methods._preview')
        </div>
    </div>

@endsection

@push('scripts')
@include('admin.payment-methods._form-scripts')
@endpush
