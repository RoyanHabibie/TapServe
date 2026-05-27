<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\SessionService;
use App\Models\Shop;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;

class PublicOrderController extends Controller
{
    protected CartService $cartService;
    protected SessionService $sessionService;
    protected OrderService $orderService;

    public function __construct(
        CartService $cartService,
        SessionService $sessionService,
        OrderService $orderService
    ) {
        $this->cartService = $cartService;
        $this->sessionService = $sessionService;
        $this->orderService = $orderService;
    }

    /**
     * Menampilkan menu publik berdasarkan token (dine-in) atau takeaway.
     */
    public function showMenu(?string $token = null)
    {
        // Asumsi hanya 1 shop untuk MVP
        $shop = Shop::firstOrFail();
        $categories = Category::where('shop_id', $shop->id)
            ->with([
                'menus' => function ($q) {
                    $q->where('is_available', true)->orderBy('sort_order');
                }
            ])
            ->orderBy('sort_order')
            ->get();

        $cart = $this->cartService->getCart();
        $totalItems = array_sum(array_column($cart, 'quantity'));

        return view('public.menu', compact('shop', 'categories', 'token', 'totalItems'));
    }

    /**
     * Tambah item ke keranjang (AJAX).
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $this->cartService->addItem(
            $request->menu_id,
            $request->quantity ?? 1,
            $request->notes
        );

        if ($request->ajax()) {
            return response()->json(['success' => true, 'totalItems' => $this->cartService->getTotalQuantity()]);
        }

        return redirect()->back()->with('success', 'Item ditambahkan ke keranjang.');
    }

    /**
     * Lihat keranjang.
     */
    public function viewCart(?string $token = null)
    {
        $cart = $this->cartService->getCart();
        $total = $this->cartService->getTotal();

        return view('public.cart', compact('cart', 'total', 'token'));
    }

    /**
     * Update jumlah item (AJAX).
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        $this->cartService->updateQuantity($request->menu_id, $request->quantity);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'total' => number_format($this->cartService->getTotal(), 0, ',', '.'),
                'totalItems' => $this->cartService->getTotalQuantity(),
            ]);
        }

        return redirect()->route('public.cart', ['token' => $token])->with('success', 'Keranjang diperbarui.');
    }

    /**
     * Hapus item dari keranjang.
     */
    public function removeFromCart(Request $request)
    {
        $request->validate(['menu_id' => 'required|integer']);
        $this->cartService->removeItem($request->menu_id);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'total' => number_format($this->cartService->getTotal(), 0, ',', '.'),
                'totalItems' => $this->cartService->getTotalQuantity(),
            ]);
        }

        return redirect()->route('public.cart', ['token' => $token])->with('success', 'Item dihapus.');
    }

    /**
     * Tampilkan halaman konfirmasi checkout (opsional langsung place order).
     */
    public function checkout(?string $token = null)
    {
        $cart = $this->cartService->getCart();
        if (empty($cart)) {
            return redirect()->route('public.menu', ['token' => $token])->with('error', 'Keranjang kosong.');
        }
        $total = $this->cartService->getTotal();

        return view('public.checkout', compact('cart', 'total', 'token'));
    }

    /**
     * Proses pembuatan order.
     */
    public function placeOrder(PlaceOrderRequest $request, ?string $token = null)
    {
        $shop = Shop::firstOrFail();
        $session = $this->sessionService->getOrCreateSession($token, $shop->id);

        try {
            $order = $this->orderService->placeOrder($session, $request->notes);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        // Jika takeaway (instant payment), set session ke payment_pending
        if ($session->isTakeaway()) {
            $session->update(['status' => 'payment_pending']);
        }

        return redirect()->route('public.order.status', ['order' => $order->id])
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    /**
     * Lihat status order.
     */
    public function orderStatus($orderId)
    {
        $order = \App\Models\Order::with('orderItems.menu')->findOrFail($orderId);
        return view('public.order-status', compact('order'));
    }
}
