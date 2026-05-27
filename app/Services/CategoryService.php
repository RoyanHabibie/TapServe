<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;
use App\Services\ActivityLogService;

class CategoryService
{
    public function getAll(int $shopId)
    {
        return Category::where('shop_id', $shopId)->orderBy('sort_order')->get();
    }

    public function store(int $shopId, array $data): Category
    {
        $category = Category::create([
            'shop_id' => $shopId,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        // Activity log
        app(ActivityLogService::class)->log(
            'created',
            "Category '{$category->name}' ditambahkan.",
            $category
        );

        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? $category->description,
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
        ]);

        // Activity log
        app(ActivityLogService::class)->log(
            'updated',
            "Category '{$category->name}' diubah.",
            $category
        );

        return $category;
    }

    public function delete(Category $category): void
    {
        // Activity log
        app(ActivityLogService::class)->log(
            'deleted',
            "Category '{$category->name}' dihapus.",
            $category
        );

        $category->delete();
    }
}
