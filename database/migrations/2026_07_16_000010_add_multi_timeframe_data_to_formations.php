<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('formations', function (Blueprint $table) {
            $table->decimal('price_change_5m', 8, 2)->nullable()->after('price_change_24h');
            $table->decimal('price_change_1h', 8, 2)->nullable()->after('price_change_5m');
            $table->decimal('price_change_6h', 8, 2)->nullable()->after('price_change_1h');

            $table->decimal('volume_5m', 16, 2)->nullable();
            $table->decimal('volume_1h', 16, 2)->nullable();
            $table->decimal('volume_6h', 16, 2)->nullable();

            $table->unsignedInteger('buys_5m')->nullable();
            $table->unsignedInteger('sells_5m')->nullable();
            $table->unsignedInteger('buys_1h')->nullable();
            $table->unsignedInteger('sells_1h')->nullable();
            $table->unsignedInteger('buys_6h')->nullable();
            $table->unsignedInteger('sells_6h')->nullable();

            $table->decimal('fdv', 20, 2)->nullable();
            $table->decimal('market_cap', 20, 2)->nullable();

            // infos

            $table->text('image_url')->nullable();
            $table->text('header')->nullable();
            $table->text('open_graph')->nullable();

            // Birdeye-only — null until that service is wired up
            $table->unsignedInteger('unique_buyers_24h')->nullable();
            $table->unsignedInteger('unique_sellers_24h')->nullable();
        });
    }

    public function down(): void {
        // Schema::dropIfExists('formations');
    }
};
