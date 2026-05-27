<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\SessionStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TableSession;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function placeOrder(TableSession $session, ?string $notes = null): Order
    {
        if (!app(SessionService::class)->canAddOrder($session)) {
            throw new \Exception('Session sudah tidak aktif. Tidak dapat menambah pesanan.');
        }

        $cart = $this->cartService->getCart();

        if (empty($cart)) {
            throw new \Exception('Keranjang kosong.');
        }

        $order = DB::transaction(function () use ($session, $notes, $cart) {
            $order = Order::create([
                'shop_id'      => $session->shop_id,
                'session_id'   => $session->id,
                'order_number' => $this->generateOrderNumber(),
                'status'       => OrderStatus::Pending->value,
                'total_amount' => $this->cartService->getTotal(),
                'notes'        => $notes,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id'  => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'price'    => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'notes'    => $item['notes'] ?? null,
                ]);
            }

            return $order;
        });

        app(ActivityLogService::class)->log(
            'order_created',
            "Order #{$order->order_number} dibuat untuk session #{$session->id}.",
            $order
        );

        $this->cartService->clear();

        return $order;
    }

    public function updateStatus(Order $order, string $newStatus): Order
    {
        $allowedTransitions = [
            OrderStatus::Pending->value    => [OrderStatus::Processing->value, OrderStatus::Cancelled->value],
            OrderStatus::Processing->value => [OrderStatus::Ready->value, OrderStatus::Cancelled->value],
            OrderStatus::Ready->value      => [OrderStatus::Completed->value, OrderStatus::Cancelled->value],
            OrderStatus::Completed->value  => [],
            OrderStatus::Cancelled->value  => [],
        ];

        $current = $order->status;

        if (!isset($allowedTransitions[$current]) || !in_array($newStatus, $allowedTransitions[$current])) {
            throw new \Exception("Perubahan status dari {$current} ke {$newStatus} tidak diizinkan.");
        }

        $order->update(['status' => $newStatus]);

        app(ActivityLogService::class)->log(
            'order_status_changed',
            "Order #{$order->order_number} status berubah menjadi '{$newStatus}'.",
            $order,
            ['old_status' => $current, 'new_status' => $newStatus]
        );

        return $order;
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(uniqid()) . '-' . now()->format('His');
    }
}
