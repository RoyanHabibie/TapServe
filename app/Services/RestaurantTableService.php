<?php

namespace App\Services;

use App\Models\RestaurantTable;
use Illuminate\Support\Str;

class RestaurantTableService
{
    public function getAll(int $shopId)
    {
        return RestaurantTable::where('shop_id', $shopId)->get();
    }

    public function store(int $shopId, array $data): RestaurantTable
    {
        return RestaurantTable::create([
            'shop_id' => $shopId,
            'name' => $data['name'],
            'token' => Str::random(64),
            'capacity' => $data['capacity'],
            'status' => $data['status'] ?? 'available',
        ]);
    }

    public function update(RestaurantTable $table, array $data): RestaurantTable
    {
        $table->update([
            'name' => $data['name'] ?? $table->name,
            'capacity' => $data['capacity'] ?? $table->capacity,
            'status' => $data['status'] ?? $table->status,
        ]);

        return $table;
    }

    public function delete(RestaurantTable $table): void
    {
        $table->delete();
    }
}
