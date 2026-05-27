<?php

namespace App\Services;

use App\Enums\SessionStatus;
use App\Enums\TableStatus;
use App\Models\RestaurantTable;
use App\Models\TableSession;
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
                ->whereIn('status', [SessionStatus::Open->value, SessionStatus::PaymentPending->value])
                ->where('shop_id', $shopId)
                ->first();

            if ($session) {
                return $session;
            }

            $session = TableSession::create([
                'shop_id'      => $shopId,
                'table_id'     => $table->id,
                'order_type'   => 'dine_in',
                'payment_mode' => 'open_table',
                'status'       => SessionStatus::Open->value,
                'opened_at'    => Carbon::now(),
            ]);

            $table->update(['status' => TableStatus::Occupied->value]);

            return $session;
        }

        return TableSession::create([
            'shop_id'      => $shopId,
            'table_id'     => null,
            'order_type'   => 'takeaway',
            'payment_mode' => 'instant',
            'status'       => SessionStatus::Open->value,
            'opened_at'    => Carbon::now(),
        ]);
    }

    public function closeSession(TableSession $session): TableSession
    {
        $session->update([
            'status'    => SessionStatus::Closed->value,
            'closed_at' => Carbon::now(),
        ]);

        if ($session->table_id) {
            $session->table()->update(['status' => TableStatus::Available->value]);
        }

        app(ActivityLogService::class)->log(
            'session_closed',
            "Session #{$session->id} ditutup.",
            $session
        );

        return $session;
    }

    public function cancelSession(TableSession $session): TableSession
    {
        $session->update([
            'status'    => SessionStatus::Cancelled->value,
            'closed_at' => Carbon::now(),
        ]);

        if ($session->table_id) {
            $session->table()->update(['status' => TableStatus::Available->value]);
        }

        app(ActivityLogService::class)->log(
            'session_cancelled',
            "Session #{$session->id} dibatalkan.",
            $session
        );

        return $session;
    }

    public function canAddOrder(TableSession $session): bool
    {
        return in_array($session->status, [SessionStatus::Open->value, SessionStatus::PaymentPending->value]);
    }
}
