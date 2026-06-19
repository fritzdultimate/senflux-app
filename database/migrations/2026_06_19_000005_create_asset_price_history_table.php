<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracked_asset_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 18, 8);
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->index(['tracked_asset_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_price_history');
    }
};
