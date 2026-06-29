<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pack_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // 'scout', 'vanguard', 'dominion' — stable identifier, not the editable label
            $table->string('name'); // editable display label
            $table->decimal('price', 12, 2); // $250 / $500 / $1,000
            $table->unsignedSmallInteger('duration_days');
            $table->unsignedTinyInteger('slot_count');
            $table->decimal('min_capital_per_slot', 14, 2);
            $table->decimal('max_capital_per_slot', 14, 2)->nullable(); // null = no upper bound (Dominion: "$25,000 and above")
            $table->decimal('historical_outcome_min', 5, 2)->nullable(); // percent, e.g. 12.00
            $table->decimal('historical_outcome_max', 5, 2)->nullable();
            $table->json('features')->nullable(); // bullet list shown on the pricing page
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pack_tiers');
    }
};