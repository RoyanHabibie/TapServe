<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\TableSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    protected int $shopId;

    public function __construct()
    {
        $this->shopId = auth()->user()->shop_id;
    }

    /**
     * Ringkasan untuk dashboard.
     */
    public function dashboardSummary(): array
    {
        $today = Carbon::today();

        // Pendapatan hari ini (dari payment yang paid hari ini)
        $revenueToday = Payment::where('shop_id', $this->shopId)
            ->where('status', 'paid')
            ->whereDate('paid_at', $today)
            ->sum('amount');

        // Total order hari ini (semua status selain cancelled)
        $ordersToday = Order::where('shop_id', $this->shopId)
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->count();

        // Sesi aktif
        $activeSessions = TableSession::where('shop_id', $this->shopId)
            ->whereIn('status', ['open', 'payment_pending'])
            ->count();

        // Menu terpopuler (top 5)
        $popularMenus = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->where('menus.shop_id', $this->shopId)
            ->select('menus.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('menus.id', 'menus.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return [
            'revenue_today' => $revenueToday,
            'orders_today' => $ordersToday,
            'active_sessions' => $activeSessions,
            'popular_menus' => $popularMenus,
        ];
    }

    /**
     * Laporan penjualan berdasarkan rentang tanggal.
     */
    public function salesReport(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::today()->subDays(30)->startOfDay();
        $end   = $endDate   ? Carbon::parse($endDate)->endOfDay()     : Carbon::today()->endOfDay();

        // Laporan pendapatan: payment yang sudah paid
        $payments = Payment::with('session')
            ->where('shop_id', $this->shopId)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$start, $end])
            ->orderBy('paid_at', 'desc')
            ->get();

        // Laporan pesanan: semua order (kecuali cancelled)
        $orders = Order::with(['session.table', 'orderItems'])
            ->where('shop_id', $this->shopId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'payments'           => $payments,
            'total_revenue'      => $payments->sum('amount'),
            'total_transactions' => $payments->count(),
            'orders'             => $orders,
            'total_orders'       => $orders->count(),
            'total_orders_amount'=> $orders->sum('total_amount'),
            'start_date'         => $start->format('Y-m-d'),
            'end_date'           => $end->format('Y-m-d'),
        ];
    }
}
