<?php

namespace Database\Seeders;

use App\Models\RestaurantTable;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RestaurantTableSeeder extends Seeder
{
    public function run(): void
    {
        $shop = Shop::firstOrFail();

        $tables = [
            ['name' => 'Meja 1', 'capacity' => 2],
            ['name' => 'Meja 2', 'capacity' => 2],
            ['name' => 'Meja 3', 'capacity' => 4],
            ['name' => 'Meja 4', 'capacity' => 4],
            ['name' => 'Meja 5', 'capacity' => 6],
        ];

        foreach ($tables as $table) {
            RestaurantTable::create([
                'shop_id' => $shop->id,
                'name' => $table['name'],
                'token' => Str::random(64),
                'capacity' => $table['capacity'],
                'status' => 'available',
            ]);
        }
    }
}
