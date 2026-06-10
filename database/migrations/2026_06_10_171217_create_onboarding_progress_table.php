<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Which steps are done
            $table->boolean('profile_completed')->default(false);
            $table->boolean('explored_signals')->default(false);
            $table->boolean('viewed_terminal')->default(false);
            $table->boolean('connected_bot')->default(false);
            $table->boolean('joined_telegram')->default(false);

            // Welcome screen shown
            $table->boolean('welcome_dismissed')->default(false);

            // Full onboarding dismissed (skipped or completed)
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_progress');
    }
};