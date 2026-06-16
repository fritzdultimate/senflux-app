<?php
// FILE: 2025_01_01_000003_create_wallets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);                    // enum WalletType
            $table->decimal('balance', 18, 8)->default(0);
            $table->decimal('locked_balance', 18, 8)->default(0); // pending withdrawals
            $table->string('currency', 10)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'type']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
