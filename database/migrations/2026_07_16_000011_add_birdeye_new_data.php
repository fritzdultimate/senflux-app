<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('formations', function (Blueprint $table) {
            $table->unsignedInteger('holders')->nullable();
            $table->unsignedInteger('unique_wallets_24h')->nullable();
            $table->decimal('unique_wallets_24h_change_pct', 8, 2)->nullable();
            $table->decimal('volume_buy_24h_usd', 16, 2)->nullable();
            $table->decimal('volume_sell_24h_usd', 16, 2)->nullable();
            $table->timestamp('birdeye_synced_at')->nullable();
        });
    }

    public function down(): void {
        // Schema::dropIfExists('formations');
    }
};
