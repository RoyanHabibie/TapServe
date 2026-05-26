<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $fillable = [
        'shop_id',
        'name',
        'token',
        'capacity',
        'status', // available, occupied, disabled
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function tableSessions()
    {
        return $this->hasMany(TableSession::class, 'table_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }
}
