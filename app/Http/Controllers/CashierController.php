<?php

namespace App\Http\Controllers;

use App\Models\TableSession;

class CashierController extends Controller
{
    /**
     * Cashier dashboard: daftar session yang perlu pembayaran.
     */
    public function index()
    {
        $shopId = auth()->user()->shop_id;

        $sessions = TableSession::with([
            'orders' => function ($q) {
                $q->whereNotIn('status', ['cancelled']);
            },
            'table'
        ])
            ->where('shop_id', $shopId)
            ->whereIn('status', ['open', 'payment_pending'])
            ->latest()
            ->get();

        return view('cashier.dashboard', compact('sessions'));
    }
}
