<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\TableSession;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function show(TableSession $session)
    {
        if ($session->shop_id !== auth()->user()->shop_id) abort(403);

        $session->load('orders.orderItems.menu', 'table');

        $total = $session->orders
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $menus = Menu::where('shop_id', $session->shop_id)
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

        $paymentMethods = PaymentMethod::where('shop_id', $session->shop_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.payments.process', compact('session', 'total', 'menus', 'paymentMethods'));
    }

    public function store(ProcessPaymentRequest $request, TableSession $session)
    {
        if ($session->shop_id !== auth()->user()->shop_id) abort(403);

        try {
            $this->paymentService->process($session, $request->amount, $request->input('method'));
            return redirect()->route('cashier.dashboard')
                ->with('success', 'Pembayaran berhasil. Session ditutup.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function addManualOrder(Request $request, TableSession $session)
    {
        if ($session->shop_id !== auth()->user()->shop_id) abort(403);

        if (!in_array($session->status, ['open', 'payment_pending'])) {
            return back()->with('error', 'Session tidak aktif, tidak dapat menambah pesanan.');
        }

        $request->validate([
            'items'              => ['required', 'array', 'min:1'],
            'items.*.menu_id'    => ['required', 'integer', 'exists:menus,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.order_type' => ['nullable', 'in:dine_in,takeaway'],
            'manual_notes'       => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $session) {
            $total = 0;
            $prepared = [];

            foreach ($request->items as $item) {
                $menu = Menu::where('shop_id', $session->shop_id)->findOrFail($item['menu_id']);
                $subtotal = $menu->price * $item['quantity'];
                $total += $subtotal;
                $prepared[] = [
                    'menu'       => $menu,
                    'quantity'   => (int) $item['quantity'],
                    'subtotal'   => $subtotal,
                    'order_type' => $item['order_type'] ?? 'dine_in',
                ];
            }

            $order = Order::create([
                'shop_id'      => $session->shop_id,
                'session_id'   => $session->id,
                'order_number' => 'MNL-' . strtoupper(substr(uniqid(), -6)) . '-' . now()->format('His'),
                'status'       => OrderStatus::Completed->value,
                'total_amount' => $total,
                'notes'        => $request->manual_notes ?: 'Pesanan manual oleh kasir',
            ]);

            foreach ($prepared as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'menu_id'    => $item['menu']->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['menu']->price,
                    'subtotal'   => $item['subtotal'],
                    'order_type' => $item['order_type'],
                ]);
            }
        });

        return back()->with('success', 'Pesanan manual berhasil ditambahkan.');
    }
}
