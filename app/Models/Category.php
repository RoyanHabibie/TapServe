<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'shop_id',
        'name',
        'slug',
        'description',
        'image',
        'sort_order',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
