<?php
// database/migrations/2026_07_02_000003_create_formation_liquidity_snapshots_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('formation_liquidity_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->decimal('liquidity_usd', 20, 2);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['formation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_liquidity_snapshots');
    }
};