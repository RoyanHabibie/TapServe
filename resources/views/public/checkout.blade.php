@extends('public.layout')

@section('title', 'Checkout')

@section('content')
    <h3>Konfirmasi Pesanan</h3>
    <div class="card mb-3">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Total</th>
                        <th>Rp {{ number_format($total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <form action="{{ route('public.place.order', ['token' => $token]) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="notes" class="form-label">Catatan (opsional)</label>
            <textarea class="form-control" id="notes" name="notes" rows="2"
                placeholder="Contoh: tidak pedas, es batu sedikit..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-100">Pesan Sekarang</button>
    </form>
    <a href="{{ route('public.cart', ['token' => $token]) }}" class="btn btn-outline-secondary mt-2 w-100">Kembali ke
        Keranjang</a>
@endsection
