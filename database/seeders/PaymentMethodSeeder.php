<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $shop = \App\Models\Shop::first();
        if (!$shop) return;

        $methods = [
            ['name' => 'Tunai',    'code' => 'cash',     'icon' => 'bi-cash',          'color' => '#16a34a', 'sort_order' => 1],
            ['name' => 'QRIS',     'code' => 'qris',     'icon' => 'bi-qr-code-scan',  'color' => '#7c3aed', 'sort_order' => 2],
            ['name' => 'Transfer', 'code' => 'transfer', 'icon' => 'bi-bank',           'color' => '#2563eb', 'sort_order' => 3],
            ['name' => 'E-Wallet', 'code' => 'ewallet',  'icon' => 'bi-wallet2',        'color' => '#d97706', 'sort_order' => 4],
        ];

        foreach ($methods as $m) {
            \App\Models\PaymentMethod::firstOrCreate(
                ['shop_id' => $shop->id, 'code' => $m['code']],
                array_merge($m, ['shop_id' => $shop->id, 'is_active' => true])
            );
        }
    }
}
