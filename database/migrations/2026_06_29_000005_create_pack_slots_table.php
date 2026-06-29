<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pack_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_subscription_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('slot_number'); // 1-indexed within the subscription

            $table->string('status')->default('empty');
            $table->decimal('capital_amount', 14, 2)->nullable(); // principal only — see note below

            /**
             * Running total of profit credited to this slot so far. This is
             * a DISPLAY counter, not an escrow balance — daily profit pays
             * out straight to the wallet as it's earned (every tier lists
             * "Daily Profit Withdrawals" unconditionally). Maturity/exit
             * only ever needs to return capital_amount; profit already
             * left the building. Auto-Compound is the one case that pulls
             * this same already-paid money back out of the wallet to
             * re-stake it — see PackLifecycleService.
             */
            $table->decimal('realized_profit', 14, 2)->default(0);

            $table->timestamp('funded_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->foreignId('fund_transaction_id')->nullable()
                ->constrained('wallet_transactions')->nullOnDelete();
            $table->foreignId('close_transaction_id')->nullable()
                ->constrained('wallet_transactions')->nullOnDelete();

            $table->decimal('early_exit_fee_charged', 14, 2)->nullable();
            $table->boolean('was_early_exit')->default(false);

            $table->timestamps();

            $table->unique(['pack_subscription_id', 'slot_number']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pack_slots');
    }
};