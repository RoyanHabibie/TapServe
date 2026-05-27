<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Kitchen dashboard: tampilkan semua order aktif (tidak completed/cancelled).
     */
    public function index()
    {
        $shopId = auth()->user()->shop_id;

        $orders = Order::with('orderItems.menu', 'session.table')
            ->where('shop_id', $shopId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest()
            ->get();

        return view('kitchen.dashboard', compact('orders'));
    }

    /**
     * Update status order (AJAX).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:processing,ready,completed,cancelled',
        ]);

        try {
            $this->orderService->updateStatus($order, $request->status);

            return response()->json(['success' => true, 'newStatus' => $order->status]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
