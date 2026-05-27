<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending    = 'pending';
    case Processing = 'processing';
    case Ready      = 'ready';
    case Completed  = 'completed';
    case Cancelled  = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending    => 'Menunggu',
            self::Processing => 'Diproses',
            self::Ready      => 'Siap',
            self::Completed  => 'Selesai',
            self::Cancelled  => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Pending    => 'secondary',
            self::Processing => 'warning',
            self::Ready      => 'info',
            self::Completed  => 'success',
            self::Cancelled  => 'danger',
        };
    }
}
