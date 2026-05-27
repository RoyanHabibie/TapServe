<?php

namespace App\Enums;

enum SessionStatus: string
{
    case Open           = 'open';
    case PaymentPending = 'payment_pending';
    case Paid           = 'paid';
    case Closed         = 'closed';
    case Cancelled      = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Open           => 'Aktif',
            self::PaymentPending => 'Menunggu Pembayaran',
            self::Paid           => 'Lunas',
            self::Closed         => 'Ditutup',
            self::Cancelled      => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Open           => 'success',
            self::PaymentPending => 'warning',
            self::Paid           => 'primary',
            self::Closed         => 'secondary',
            self::Cancelled      => 'danger',
        };
    }
}
