<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableSession extends Model
{
    protected $fillable = [
        'shop_id',
        'table_id',
        'order_type',   // dine_in, takeaway
        'payment_mode', // open_table, instant
        'status',       // open, payment_pending, paid, closed, cancelled
        'opened_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'session_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'session_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isPaymentPending(): bool
    {
        return $this->status === 'payment_pending';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isTakeaway(): bool
    {
        return $this->order_type === 'takeaway';
    }

    public function isDineIn(): bool
    {
        return $this->order_type === 'dine_in';
    }

    public function isInstantPayment(): bool
    {
        return $this->payment_mode === 'instant';
    }

    public function isOpenTablePayment(): bool
    {
        return $this->payment_mode === 'open_table';
    }
}
