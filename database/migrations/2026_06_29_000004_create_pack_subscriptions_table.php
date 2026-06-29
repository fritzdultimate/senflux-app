<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pack_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pack_tier_id')->constrained();
            $table->string('status')->default('active');

            $table->decimal('price_paid', 12, 2); // snapshot — tier price may change later, this row shouldn't

            $table->timestamp('purchased_at');
            $table->timestamp('matures_at');
            $table->timestamp('renewal_window_ends_at')->nullable(); // set when status becomes in_renewal_window

            // The purchase debit, for audit — refund (3-day window) reverses this exact transaction.
            $table->foreignId('purchase_transaction_id')->nullable()
                ->constrained('wallet_transactions')->nullOnDelete();

            $table->timestamp('refunded_at')->nullable();
            $table->foreignId('refund_transaction_id')->nullable()
                ->constrained('wallet_transactions')->nullOnDelete();

            // Renewal chain — set on the OLD subscription once a Continue/
            // Compound/Upgrade creates a new one, and on the NEW subscription
            // pointing back. Lets a user's full cycle history be walked either
            // direction without guessing from timestamps.
            $table->foreignId('renewed_into_subscription_id')->nullable()
                ->constrained('pack_subscriptions')->nullOnDelete();
            $table->foreignId('renewed_from_subscription_id')->nullable()
                ->constrained('pack_subscriptions')->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'matures_at']);
            $table->index(['status', 'renewal_window_ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pack_subscriptions');
    }
};