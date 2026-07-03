<?php
// database/migrations/2026_07_06_000001_add_type_and_amount_to_formation_trade_activities_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('formation_trade_activities', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->decimal('token_amount', 30, 6)->nullable();
            $table->string('trader_wallet')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('formation_trade_activities', function (Blueprint $table) {
            $table->dropColumn(['type', 'token_amount', 'trader_wallet']);
        });
    }
};