<?php
// FILE: 2025_01_01_000006_create_deposit_earnings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_transaction_id')->nullable()
                  ->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 8);
            $table->decimal('rate_applied', 6, 4);
            $table->string('formation_state', 20)->nullable(); // enum MarketFormationState
            $table->decimal('formation_multiplier', 5, 4)->default(1.0);
            $table->date('earned_date');
            $table->timestamp('processed_at');
            $table->timestamps();

            $table->unique(['deposit_id', 'earned_date']); // idempotent
            $table->index(['user_id', 'earned_date']);
        });
    }

    public function down(): void { Schema::dropIfExists('deposit_earnings'); }
};
