<?php

namespace App\Services;

use App\Models\RestaurantTable;
use Illuminate\Support\Str;
use App\Services\ActivityLogService;

class RestaurantTableService
{
    public function getAll(int $shopId)
    {
        return RestaurantTable::where('shop_id', $shopId)->get();
    }

    public function store(int $shopId, array $data): RestaurantTable
    {
        $table = RestaurantTable::create([
            'shop_id' => $shopId,
            'name' => $data['name'],
            'token' => Str::random(64),
            'capacity' => $data['capacity'],
            'status' => $data['status'] ?? 'available',
        ]);

        // Activity log
        app(ActivityLogService::class)->log(
            'created',
            "Restaurant Table '{$table->name}' ditambahkan.",
            $table
        );

        return $table;
    }

    public function update(RestaurantTable $table, array $data): RestaurantTable
    {
        $table->update([
            'name' => $data['name'] ?? $table->name,
            'capacity' => $data['capacity'] ?? $table->capacity,
            'status' => $data['status'] ?? $table->status,
        ]);

        // Activity log
        app(ActivityLogService::class)->log(
            'updated',
            "Restaurant Table '{$table->name}' diubah.",
            $table
        );

        return $table;
    }

    public function delete(RestaurantTable $table): void
    {
        // Activity log
        app(ActivityLogService::class)->log(
            'deleted',
            "Restaurant Table '{$table->name}' dihapus.",
            $table
        );

        $table->delete();
    }
}
