<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\SessionStatus;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\Category;
use App\Models\RestaurantTable;
use App\Models\Shop;
use App\Models\TableSession;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\SessionService;
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
        $shop = Shop::firstOrFail();
        $categories = Category::where('shop_id', $shop->id)
            ->with([
                'menus' => fn($q) => $q->where('is_available', true)->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->get();

        $totalItems = $this->cartService->getTotalQuantity();

        $activeSession = null;
        $sessionTotal  = 0;
        if ($token) {
            $table = RestaurantTable::where('token', $token)->where('shop_id', $shop->id)->first();
            if ($table) {
                $activeSession = TableSession::where('table_id', $table->id)
                    ->whereIn('status', [SessionStatus::Open->value, SessionStatus::PaymentPending->value])
                    ->with(['orders' => fn($q) => $q->where('status', '!=', OrderStatus::Cancelled->value)])
                    ->first();
                if ($activeSession) {
                    $sessionTotal = $activeSession->orders->sum('total_amount');
                }
            }
        }

        return view('public.menu', compact('shop', 'categories', 'token', 'totalItems', 'activeSession', 'sessionTotal'));
    }

    public function sessionSummary(string $token)
    {
        $shop  = Shop::firstOrFail();
        $table = RestaurantTable::where('token', $token)->where('shop_id', $shop->id)->firstOrFail();

        $session = TableSession::where('table_id', $table->id)
            ->whereIn('status', [SessionStatus::Open->value, SessionStatus::PaymentPending->value])
            ->where('shop_id', $shop->id)
            ->with(['orders' => fn($q) => $q->with('orderItems.menu')->where('status', '!=', OrderStatus::Cancelled->value)])
            ->first();

        if (!$session) {
            return redirect()->route('public.menu', ['token' => $token])
                ->with('info', 'Tidak ada sesi aktif untuk meja ini. Silakan mulai pesanan baru.');
        }

        $total      = $session->orders->sum('total_amount');
        $totalItems = $this->cartService->getTotalQuantity();

        return view('public.session-summary', compact('session', 'table', 'token', 'total', 'shop', 'totalItems'));
    }

    public function requestPayment(string $token)
    {
        $shop  = Shop::firstOrFail();
        $table = RestaurantTable::where('token', $token)->where('shop_id', $shop->id)->firstOrFail();

        $session = TableSession::where('table_id', $table->id)
            ->where('status', SessionStatus::Open->value)
            ->where('shop_id', $shop->id)
            ->first();

        if (!$session) {
            return redirect()->route('public.session', ['token' => $token])
                ->with('error', 'Sesi tidak ditemukan atau sudah dalam proses pembayaran.');
        }

        $session->update(['status' => SessionStatus::PaymentPending->value]);

        return redirect()->route('public.session', ['token' => $token])
            ->with('success', 'Permintaan pembayaran dikirim. Kasir akan segera memproses.');
    }

    public function ajaxSessionStatus(string $token)
    {
        $shop  = Shop::firstOrFail();
        $table = RestaurantTable::where('token', $token)->where('shop_id', $shop->id)->firstOrFail();

        $session = TableSession::where('table_id', $table->id)->latest()->first();

        return response()->json(['status' => $session?->status ?? 'not_found']);
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
        $cart       = $this->cartService->getCart();
        $total      = $this->cartService->getTotal();
        $totalItems = $this->cartService->getTotalQuantity();

        return view('public.cart', compact('cart', 'total', 'token', 'totalItems'));
    }

    /**
     * Update jumlah item (AJAX).
     */
    public function updateCart(Request $request, ?string $token = null)
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
    public function removeFromCart(Request $request, ?string $token = null)
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
     * Update order_type satu item atau semua item di keranjang (AJAX).
     */
    public function updateCartItemType(Request $request, ?string $token = null)
    {
        $request->validate([
            'order_type' => 'required|in:dine_in,takeaway',
            'menu_id'    => 'nullable|integer',
            'all'        => 'nullable|boolean',
        ]);

        if ($request->boolean('all')) {
            $this->cartService->updateAllOrderType($request->order_type);
        } else {
            $this->cartService->updateItemOrderType((int) $request->menu_id, $request->order_type);
        }

        return response()->json(['success' => true]);
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
        $total      = $this->cartService->getTotal();
        $totalItems = $this->cartService->getTotalQuantity();

        return view('public.checkout', compact('cart', 'total', 'token', 'totalItems'));
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

        session(['last_order_token' => $token]);

        return redirect()->route('public.order.status', ['order' => $order->id])
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    /**
     * Lihat status order.
     */
    public function orderStatus($orderId)
    {
        $order = \App\Models\Order::with('orderItems.menu')->findOrFail($orderId);
        $token = session('last_order_token');
        return view('public.order-status', compact('order', 'token'));
    }

    /**
     * API untuk polling status order oleh customer.
     */
    public function ajaxOrderStatus($orderId)
    {
        $order = \App\Models\Order::with('orderItems.menu')->findOrFail($orderId);
        return response()->json([
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'total_amount' => $order->total_amount,
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'name' => $item->menu->name,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ];
            }),
        ]);
    }
}
