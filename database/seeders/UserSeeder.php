<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $shop = Shop::firstOrFail();

        $users = [
            [
                'shop_id' => $shop->id,
                'name' => 'Owner User',
                'email' => 'owner@tapserve.id',
                'password' => bcrypt('password'),
                'role' => 'owner',
            ],
            [
                'shop_id' => $shop->id,
                'name' => 'Admin User',
                'email' => 'admin@tapserve.id',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ],
            [
                'shop_id' => $shop->id,
                'name' => 'Cashier User',
                'email' => 'cashier@tapserve.id',
                'password' => bcrypt('password'),
                'role' => 'cashier',
            ],
            [
                'shop_id' => $shop->id,
                'name' => 'Kitchen User',
                'email' => 'kitchen@tapserve.id',
                'password' => bcrypt('password'),
                'role' => 'kitchen',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
