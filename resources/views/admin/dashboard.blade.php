@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h3>Dashboard</h3>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pendapatan Hari Ini</h5>
                    <p class="card-text display-6">Rp {{ number_format($revenue_today, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pesanan Hari Ini</h5>
                    <p class="card-text display-6">{{ $orders_today }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Sesi Aktif</h5>
                    <p class="card-text display-6">{{ $active_sessions }}</p>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mt-4">Menu Terpopuler</h4>
    @if ($popular_menus->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Total Terjual</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($popular_menus as $menu)
                    <tr>
                        <td>{{ $menu->name }}</td>
                        <td>{{ $menu->total_qty }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Belum ada data penjualan.</p>
    @endif
@endsection
