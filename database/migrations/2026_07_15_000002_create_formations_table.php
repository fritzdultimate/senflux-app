<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('token_name');
            $table->string('token_symbol', 20); // '$XYZ', 'BONK', 'POPCAT'
            $table->string('ecosystem', 50)->default('solana');

            $table->string('state', 20)->default('idle'); // FormationState
            $table->unsignedTinyInteger('score')->default(0); // 0-100, the card's headline number
            $table->string('confidence', 10)->default('low'); // low/moderate/high/strong — display label, not a separate enum table

            // The four sub-metric bars on the card — all 0-100
            $table->unsignedTinyInteger('capital_concentration')->default(0);
            $table->unsignedTinyInteger('liquidity_migration')->default(0);
            $table->unsignedTinyInteger('participation_growth')->default(0);
            $table->unsignedTinyInteger('wallet_quality')->default(0);

            $table->timestamp('detected_at')->nullable();
            $table->timestamp('state_changed_at')->nullable(); // drives "Detected 18 mins ago" style displays per-state

            $table->boolean('is_active')->default(true); // soft-hide from the feed without deleting history
            $table->text('notes')->nullable();
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['state', 'is_active']);
            $table->index('token_symbol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
