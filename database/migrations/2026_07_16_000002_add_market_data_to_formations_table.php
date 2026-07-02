<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->string('mint_address')->nullable()->after('token_symbol');
            $table->string('dex')->nullable();
            $table->string('pair_address')->nullable();
            $table->string('pair_url')->nullable();
            $table->decimal('price_usd', 24, 10)->nullable();
            $table->decimal('liquidity_usd', 20, 2)->nullable();
            $table->decimal('volume_24h', 20, 2)->nullable();
            $table->unsignedInteger('buys_24h')->nullable();
            $table->unsignedInteger('sells_24h')->nullable();
            $table->decimal('price_change_24h', 8, 4)->nullable();
            $table->timestamp('market_data_synced_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn([
                'mint_address', 'dex', 'pair_address', 'pair_url', 'price_usd',
                'liquidity_usd', 'volume_24h', 'buys_24h', 'sells_24h',
                'price_change_24h', 'market_data_synced_at',
            ]);
        });
    }
};