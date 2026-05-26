<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MenuService
{
    public function getAll(int $shopId)
    {
        return Menu::with('category')
            ->where('shop_id', $shopId)
            ->orderBy('sort_order')
            ->get();
    }

    public function store(int $shopId, array $data): Menu
    {
        $imagePath = null;
        if (isset($data['image'])) {
            $imagePath = $data['image']->store('menus', 'public');
        }

        return Menu::create([
            'shop_id' => $shopId,
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'image' => $imagePath,
            'is_available' => $data['is_available'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(Menu $menu, array $data): Menu
    {
        if (isset($data['image'])) {
            // Hapus gambar lama
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $menu->image = $data['image']->store('menus', 'public');
        }

        $menu->update([
            'category_id' => $data['category_id'] ?? $menu->category_id,
            'name' => $data['name'] ?? $menu->name,
            'slug' => isset($data['name']) ? Str::slug($data['name']) : $menu->slug,
            'description' => $data['description'] ?? $menu->description,
            'price' => $data['price'] ?? $menu->price,
            'is_available' => $data['is_available'] ?? $menu->is_available,
            'sort_order' => $data['sort_order'] ?? $menu->sort_order,
        ]);

        return $menu;
    }

    public function delete(Menu $menu): void
    {
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }
        $menu->delete();
    }
}
