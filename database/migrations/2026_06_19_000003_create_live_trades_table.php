<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('live_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracked_asset_id')->constrained()->cascadeOnDelete();
            $table->string('type', 10);                    // long|short
            $table->decimal('entry_price', 18, 8);
            $table->decimal('current_price', 18, 8)->nullable();
            $table->decimal('exit_price', 18, 8)->nullable();
            $table->string('status', 10)->default('open'); // open|closed
            $table->decimal('pnl_amount', 15, 2)->nullable();
            $table->decimal('pnl_percent', 8, 4)->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'tracked_asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_trades');
    }
};
