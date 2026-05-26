<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $shop = Shop::firstOrFail();

        $categories = [
            ['name' => 'Kopi', 'description' => 'Aneka kopi segar'],
            ['name' => 'Teh', 'description' => 'Teh pilihan terbaik'],
            ['name' => 'Makanan Ringan', 'description' => 'Camilan lezat'],
            ['name' => 'Makanan Berat', 'description' => 'Makanan utama'],
            ['name' => 'Minuman Segar', 'description' => 'Minuman dingin'],
        ];

        foreach ($categories as $index => $cat) {
            Category::create([
                'shop_id' => $shop->id,
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
                'sort_order' => $index,
            ]);
        }
    }
}
