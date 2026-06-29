<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Replaced entirely by the per-formation system (see Formation model +
 * formations table). A single global "current state" row applied
 * uniformly to every deposit can't support "the Terminal shows why the
 * bot acts" — that requires a payout to trace back to a SPECIFIC
 * formation, not one platform-wide dial. Confirmed with the client
 * before deleting a working system rather than assuming.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('market_formation_states');
    }

    public function down(): void
    {
        Schema::create('market_formation_states', function (Blueprint $table) {
            $table->id();
            $table->string('state', 20);
            $table->string('ecosystem', 50)->default('solana');
            $table->string('bot_status', 20)->default('standby');
            $table->unsignedInteger('active_wallets')->nullable();
            $table->decimal('liquidity_score', 5, 2)->nullable();
            $table->decimal('participation_score', 5, 2)->nullable();
            $table->decimal('formation_score', 5, 2)->nullable();
            $table->decimal('earnings_multiplier', 5, 4)->default(1.0);
            $table->text('notes')->nullable();
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->index('is_current');
        });
    }
};
