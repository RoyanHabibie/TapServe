<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\SessionStatus;
use App\Models\Payment;
use App\Models\TableSession;
use Carbon\Carbon;

class PaymentService
{
    protected SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function process(TableSession $session, float $amount, string $method): Payment
    {
        if (in_array($session->status, [SessionStatus::Closed->value, SessionStatus::Cancelled->value])) {
            throw new \Exception('Session sudah tidak aktif.');
        }

        $totalOrder = $session->orders()
            ->where('status', '!=', OrderStatus::Cancelled->value)
            ->sum('total_amount');

        if ($amount < $totalOrder) {
            throw new \Exception('Jumlah pembayaran kurang dari total pesanan.');
        }

        $payment = Payment::create([
            'shop_id'    => $session->shop_id,
            'session_id' => $session->id,
            'amount'     => $amount,
            'method'     => $method,
            'status'     => PaymentStatus::Paid->value,
            'paid_at'    => Carbon::now(),
        ]);

        $session->update(['status' => SessionStatus::Paid->value]);
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
