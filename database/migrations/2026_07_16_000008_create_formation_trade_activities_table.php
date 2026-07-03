<?php
// database/migrations/2026_07_05_000001_create_formation_trade_activities_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('formation_trade_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->string('tx_signature')->unique();
            $table->unsignedBigInteger('slot')->nullable();
            $table->timestamp('block_time')->nullable();
            $table->string('source')->default('market_pool'); // 'market_pool' now, 'senflux' once real execution exists
            $table->boolean('failed')->default(false);
            $table->timestamps();

            $table->index(['formation_id', 'block_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_trade_activities');
    }
};