<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Session\SessionManager;

class CartService
{
    protected $session;

    public function __construct(SessionManager $session)
    {
        $this->session = $session;
    }

    public function getCart()
    {
        return $this->session->get('cart', []);
    }

    public function addItem(int $menuId, int $quantity = 1, ?string $notes = null)
    {
        $cart = $this->getCart();

        // Cek jika sudah ada, tambah quantity
        if (isset($cart[$menuId])) {
            $cart[$menuId]['quantity'] += $quantity;
            if ($notes) {
                $cart[$menuId]['notes'] = $notes;
            }
        } else {
            $menu = Menu::findOrFail($menuId);
            $cart[$menuId] = [
                'menu_id' => $menuId,
                'name' => $menu->name,
                'price' => $menu->price,
                'quantity' => $quantity,
                'notes' => $notes,
            ];
        }

        $this->session->put('cart', $cart);
    }

    public function updateQuantity(int $menuId, int $quantity)
    {
        $cart = $this->getCart();
        if (isset($cart[$menuId])) {
            if ($quantity <= 0) {
                unset($cart[$menuId]);
            } else {
                $cart[$menuId]['quantity'] = $quantity;
            }
            $this->session->put('cart', $cart);
        }
    }

    public function removeItem(int $menuId)
    {
        $cart = $this->getCart();
        unset($cart[$menuId]);
        $this->session->put('cart', $cart);
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getCart() as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function clear()
    {
        $this->session->forget('cart');
    }
}
