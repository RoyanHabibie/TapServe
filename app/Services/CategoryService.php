<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    public function getAll(int $shopId)
    {
        return Category::where('shop_id', $shopId)->orderBy('sort_order')->get();
    }

    public function store(int $shopId, array $data): Category
    {
        return Category::create([
            'shop_id' => $shopId,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? $category->description,
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
        ]);

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
