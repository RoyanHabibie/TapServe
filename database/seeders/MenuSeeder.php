<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $shop = Shop::firstOrFail();
        $categories = Category::all()->keyBy('name');

        $menus = [
            // Kopi
            ['category' => 'Kopi', 'name' => 'Espresso', 'price' => 25000],
            ['category' => 'Kopi', 'name' => 'Cappuccino', 'price' => 35000],
            ['category' => 'Kopi', 'name' => 'Latte', 'price' => 35000],
            // Teh
            ['category' => 'Teh', 'name' => 'Teh Tarik', 'price' => 20000],
            ['category' => 'Teh', 'name' => 'Teh Hijau', 'price' => 18000],
            // Makanan Ringan
            ['category' => 'Makanan Ringan', 'name' => 'Kentang Goreng', 'price' => 22000],
            ['category' => 'Makanan Ringan', 'name' => 'Tahu Crispy', 'price' => 18000],
            // Makanan Berat
            ['category' => 'Makanan Berat', 'name' => 'Nasi Goreng', 'price' => 30000],
            ['category' => 'Makanan Berat', 'name' => 'Mie Goreng', 'price' => 28000],
            // Minuman Segar
            ['category' => 'Minuman Segar', 'name' => 'Lemon Tea', 'price' => 22000],
            ['category' => 'Minuman Segar', 'name' => 'Milkshake Coklat', 'price' => 28000],
        ];

        foreach ($menus as $index => $menu) {
            $categoryId = $categories[$menu['category']]->id ?? null;

            Menu::create([
                'shop_id' => $shop->id,
                'category_id' => $categoryId,
                'name' => $menu['name'],
                'slug' => Str::slug($menu['name']),
                'price' => $menu['price'],
                'is_available' => true,
                'sort_order' => $index,
            ]);
        }
    }
}
