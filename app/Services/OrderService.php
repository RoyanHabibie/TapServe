<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TableSession;

class OrderService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function placeOrder(TableSession $session, ?string $notes = null): Order
    {
        // Cek apakah session bisa menerima order
        if (!app(SessionService::class)->canAddOrder($session)) {
            throw new \Exception('Session sudah tidak aktif. Tidak dapat menambah pesanan.');
        }

        $cart = $this->cartService->getCart();

        if (empty($cart)) {
            throw new \Exception('Keranjang kosong.');
        }

        $cart = $this->cartService->getCart();

        if (empty($cart)) {
            throw new \Exception('Keranjang kosong.');
        }

        $order = Order::create([
            'shop_id' => $session->shop_id,
            'session_id' => $session->id,
            'order_number' => $this->generateOrderNumber(),
            'status' => 'pending',
            'total_amount' => $this->cartService->getTotal(),
            'notes' => $notes,
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        // Kosongkan cart
        $this->cartService->clear();

        return $order;
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(uniqid()) . '-' . now()->format('His');
    }

    /**
     * Update status order dengan validasi urutan.
     */
    public function updateStatus(Order $order, string $newStatus): Order
    {
        $allowedTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['ready', 'cancelled'],
            'ready' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        $current = $order->status;

        if (!isset($allowedTransitions[$current]) || !in_array($newStatus, $allowedTransitions[$current])) {
            throw new \Exception("Perubahan status dari {$current} ke {$newStatus} tidak diizinkan.");
        }

        $order->update(['status' => $newStatus]);

        return $order;
    }
}
