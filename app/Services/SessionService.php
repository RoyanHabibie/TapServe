<?php

namespace App\Services;

use App\Models\RestaurantTable;
use App\Models\TableSession;
use App\Models\Shop;
use Carbon\Carbon;

class SessionService
{
    public function getOrCreateSession(?string $token, int $shopId): TableSession
    {
        if ($token) {
            $table = RestaurantTable::where('token', $token)
                ->where('shop_id', $shopId)
                ->firstOrFail();

            $session = TableSession::where('table_id', $table->id)
                ->whereIn('status', ['open', 'payment_pending'])
                ->where('shop_id', $shopId)
                ->first();

            if ($session) {
                return $session;
            }

            // Buat session baru
            $session = TableSession::create([
                'shop_id' => $shopId,
                'table_id' => $table->id,
                'order_type' => 'dine_in',
                'payment_mode' => 'open_table',
                'status' => 'open',
                'opened_at' => Carbon::now(),
            ]);

            // Update status meja
            $table->update(['status' => 'occupied']);

            return $session;
        }

        // Takeaway
        return TableSession::create([
            'shop_id' => $shopId,
            'table_id' => null,
            'order_type' => 'takeaway',
            'payment_mode' => 'instant',
            'status' => 'open',
            'opened_at' => Carbon::now(),
        ]);
    }

    /**
     * Menutup session (set status closed, catat closed_at)
     */
    public function closeSession(TableSession $session): TableSession
    {
        $session->update([
            'status' => 'closed',
            'closed_at' => Carbon::now(),
        ]);

        // Kembalikan status meja jika dine-in
        if ($session->table_id) {
            $session->table()->update(['status' => 'available']);
        }

        return $session;
    }

    /**
     * Batalkan session
     */
    public function cancelSession(TableSession $session): TableSession
    {
        $session->update([
            'status' => 'cancelled',
            'closed_at' => Carbon::now(),
        ]);

        if ($session->table_id) {
            $session->table()->update(['status' => 'available']);
        }

        return $session;
    }

    /**
     * Cek apakah session masih bisa menerima order
     */
    public function canAddOrder(TableSession $session): bool
    {
        return in_array($session->status, ['open', 'payment_pending']);
    }
}
