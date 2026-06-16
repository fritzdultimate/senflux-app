<?php
// FILE: 2025_01_01_000004_create_wallet_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40);                     // enum TransactionType
            $table->decimal('amount', 18, 8);               // always positive
            $table->decimal('balance_before', 18, 8);
            $table->decimal('balance_after', 18, 8);
            $table->string('reference_type', 80)->nullable(); // morphable source
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description', 255)->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()     // admin adjustments
                  ->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['wallet_id', 'type']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
