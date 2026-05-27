<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\TableSession;
use Carbon\Carbon;
use App\Services\ActivityLogService;

class PaymentService
{
    protected SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * Proses pembayaran untuk suatu session.
     *
     * @param TableSession $session
     * @param float $amount
     * @param string $method  cash, qris, transfer, ewallet
     * @return Payment
     */
    public function process(TableSession $session, float $amount, string $method): Payment
    {
        if ($session->status === 'closed' || $session->status === 'cancelled') {
            throw new \Exception('Session sudah tidak aktif.');
        }

        // Hitung total pesanan yang belum dibatalkan
        $totalOrder = $session->orders()
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        if ($amount < $totalOrder) {
            throw new \Exception('Jumlah pembayaran kurang dari total pesanan.');
        }

        $payment = Payment::create([
            'shop_id' => $session->shop_id,
            'session_id' => $session->id,
            'amount' => $amount,
            'method' => $method,
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);

        // Update session: status ke paid, lalu langsung close
        $session->update(['status' => 'paid']);
        $this->sessionService->closeSession($session);

        app(ActivityLogService::class)->log(
            'payment_paid',
            "Pembayaran session #{$session->id} sebesar {$amount} via {$method}.",
            $payment,
            ['method' => $method, 'amount' => $amount]
        );

        return $payment;
    }
}
