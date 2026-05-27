@extends('layouts.admin')

@section('title', 'Proses Pembayaran')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>Pembayaran Session #{{ $session->id }}</h4>
            <table class="table table-bordered">
                <tr>
                    <th>Tipe</th>
                    <td>{{ $session->order_type == 'dine_in' ? 'Dine In' : 'Takeaway' }}</td>
                </tr>
                @if ($session->table)
                    <tr>
                        <th>Meja</th>
                        <td>{{ $session->table->name }}</td>
                    </tr>
                @endif
                <tr>
                    <th>Status Session</th>
                    <td>{{ ucfirst($session->status) }}</td>
                </tr>
                <tr>
                    <th>Total Pesanan</th>
                    <td><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
                </tr>
            </table>

            <h5>Detail Pesanan</h5>
            <ul class="list-group mb-3">
                @foreach ($session->orders as $order)
                    @if ($order->status != 'cancelled')
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            #{{ $order->order_number }} ({{ ucfirst($order->status) }})
                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </li>
                    @endif
                @endforeach
            </ul>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.payments.store', $session->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="amount" class="form-label">Jumlah Dibayar (Rp)</label>
                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount"
                        name="amount" value="{{ old('amount', $total) }}" required step="0.01" min="0">
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="method" class="form-label">Metode Pembayaran</label>
                    <select class="form-select @error('method') is-invalid @enderror" id="method" name="method"
                        required>
                        <option value="">Pilih Metode</option>
                        <option value="cash" {{ old('method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="qris" {{ old('method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                        <option value="transfer" {{ old('method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="ewallet" {{ old('method') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                    </select>
                    @error('method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">Bayar & Tutup Session</button>
                <a href="{{ route('cashier.dashboard') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection
