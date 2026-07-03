<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formation_watchlist_items', function (Blueprint $table) {
            $table->id();
            $table->string('mint_address')->unique();
            $table->string('token_symbol')->nullable();
            $table->string('sector')->nullable();
            $table->string('ecosystem')->default('Solana');
            $table->boolean('is_active')->default(true);
            $table->foreignId('formation_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_watchlist_items');
    }
};