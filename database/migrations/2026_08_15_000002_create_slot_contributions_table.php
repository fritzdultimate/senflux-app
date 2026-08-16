<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slot_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_slot_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('type'); // 'deploy' | 'topup'
            $table->foreignId('wallet_transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['pack_slot_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slot_contributions');
    }
};