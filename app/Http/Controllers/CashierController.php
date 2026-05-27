<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\SessionStatus;
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
                $q->whereNotIn('status', [OrderStatus::Cancelled->value]);
            },
            'table'
        ])
            ->where('shop_id', $shopId)
            ->whereIn('status', [SessionStatus::Open->value, SessionStatus::PaymentPending->value])
            ->latest()
            ->get();

        return view('cashier.dashboard', compact('sessions'));
    }
}
