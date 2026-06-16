<?php
// FILE: 2025_01_01_000007_create_subscriptions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_config_id')->constrained()->restrictOnDelete();
            $table->string('interval', 20);                // enum PlanInterval
            $table->decimal('amount_paid', 10, 2);
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->foreignId('deposit_id')->nullable()
                  ->constrained()->nullOnDelete();
            $table->string('nowpayments_id', 100)->nullable();
            $table->string('status', 20)->default('active'); // active|expired|cancelled
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void { Schema::dropIfExists('subscriptions'); }
};
