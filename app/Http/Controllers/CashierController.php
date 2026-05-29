<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\SessionStatus;
use App\Models\Menu;
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
                $q->whereNotIn('status', [OrderStatus::Cancelled->value])
                  ->with('orderItems.menu');
            },
            'table'
        ])
            ->where('shop_id', $shopId)
            ->whereIn('status', [SessionStatus::Open->value, SessionStatus::PaymentPending->value])
            ->latest()
            ->get();

        $menus = Menu::where('shop_id', $shopId)
            ->where('is_available', true)
            ->with('category:id,name')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get(['id', 'category_id', 'name', 'price'])
            ->map(fn($m) => [
                'id'       => $m->id,
                'name'     => $m->name,
                'price'    => (float) $m->price,
                'category' => $m->category->name ?? 'Lainnya',
            ]);

        return view('cashier.dashboard', compact('sessions', 'menus'));
    }
}
