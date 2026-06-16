<?php
// FILE: 2025_01_01_000005_create_deposits_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_config_id')->constrained()->restrictOnDelete();

            // Amounts
            $table->decimal('amount_usd', 15, 2);           // what user requested
            $table->string('crypto_currency', 20)->nullable();
            $table->decimal('crypto_amount', 18, 8)->nullable(); // NP expected amount
            $table->decimal('actually_paid', 18, 8)->nullable(); // NP confirmed
            $table->decimal('actually_paid_usd', 15, 2)->nullable();

            // Status
            $table->string('status', 30)->default('pending'); // enum DepositStatus

            // NowPayments
            $table->string('nowpayments_id', 100)->nullable()->unique();
            $table->string('nowpayments_order_id', 100)->nullable();
            $table->string('payment_url', 500)->nullable();
            $table->string('pay_address', 200)->nullable();
            $table->string('network', 50)->nullable();
            $table->unsignedInteger('confirmations')->default(0);
            $table->unsignedInteger('required_confirmations')->default(0);

            // Earnings
            $table->decimal('daily_rate', 6, 4)->nullable(); // locked at activation
            $table->decimal('total_earnings', 15, 8)->default(0);
            $table->timestamp('last_earnings_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();    // NP invoice expiry

            // Webhook
            $table->timestamp('ipn_received_at')->nullable();

            $table->json('meta')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('nowpayments_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
