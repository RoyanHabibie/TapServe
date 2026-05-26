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
        // Jika token diberikan, ini dine-in
        if ($token) {
            $table = RestaurantTable::where('token', $token)
                ->where('shop_id', $shopId)
                ->firstOrFail();

            // Cek apakah ada session open untuk meja ini
            $session = TableSession::where('table_id', $table->id)
                ->where('status', 'open')
                ->where('shop_id', $shopId)
                ->first();

            if ($session) {
                return $session; // lanjut session
            }

            // Buat session baru
            return TableSession::create([
                'shop_id' => $shopId,
                'table_id' => $table->id,
                'order_type' => 'dine_in',
                'payment_mode' => 'open_table',
                'status' => 'open',
                'opened_at' => Carbon::now(),
            ]);
        }

        // Tidak ada token = takeaway
        return TableSession::create([
            'shop_id' => $shopId,
            'table_id' => null,
            'order_type' => 'takeaway',
            'payment_mode' => 'instant',
            'status' => 'open',
            'opened_at' => Carbon::now(),
        ]);
    }
}
