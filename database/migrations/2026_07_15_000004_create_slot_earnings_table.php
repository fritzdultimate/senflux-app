<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slot_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_slot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('formation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('wallet_transaction_id')->nullable()
                ->constrained('wallet_transactions')->nullOnDelete();

            $table->decimal('amount', 14, 8);
            $table->decimal('base_rate_applied', 8, 6);
            $table->string('formation_state', 20)->nullable();
            $table->decimal('formation_multiplier', 5, 4)->default(1.0);

            $table->date('earned_date');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // Idempotency — same guard pattern as deposit_earnings.
            $table->unique(['pack_slot_id', 'earned_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slot_earnings');
    }
};
