<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tracked_assets', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 20)->unique();      // BTC, WIF, SOL
            $table->string('name', 80);                   // Bitcoin, dogwifhat, Solana
            $table->string('network', 40)->nullable();    // solana, ethereum, bsc
            $table->decimal('current_price', 18, 8)->nullable();
            $table->decimal('price_change_24h', 8, 4)->nullable(); // percentage
            $table->timestamp('price_updated_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracked_assets');
    }
};
