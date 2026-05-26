<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('table_sessions')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('method')->default('cash'); // cash, qris, transfer, ewallet
            $table->string('status')->default('pending'); // pending, paid, failed, refunded
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
