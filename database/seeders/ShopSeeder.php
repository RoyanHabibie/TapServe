<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        Shop::create([
            'name' => 'TapServe Demo Cafe',
            'slug' => Str::slug('TapServe Demo Cafe'),
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'phone' => '081234567890',
            'email' => 'demo@tapserve.id',
        ]);
    }
}
