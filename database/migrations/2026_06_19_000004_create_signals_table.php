<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracked_asset_id')->constrained()->cascadeOnDelete();
            $table->string('signal_type', 10);              // buy|sell|watch
            $table->decimal('confidence_score', 5, 2);       // 0-100
            $table->text('note')->nullable();
            $table->string('min_plan', 20)->nullable();      // enum PlanType, null = all plans
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('tracked_asset_id');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
