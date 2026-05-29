@extends('layouts.admin')

@section('title', 'Edit Metode Pembayaran')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.payment-methods.index') }}"
            class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-700 mb-0" style="font-weight:700;">Edit Metode Pembayaran</h4>
            <p class="text-muted small mb-0">{{ $paymentMethod->name }}</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-body px-4 py-4">
                    <form action="{{ route('admin.payment-methods.update', $paymentMethod->id) }}"
                        method="POST">
                        @csrf @method('PUT')

                        @include('admin.payment-methods._form', ['method' => $paymentMethod])

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-floppy me-1"></i>Simpan Perubahan
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
