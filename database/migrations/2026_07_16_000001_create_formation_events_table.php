<?php
// database/migrations/2026_07_02_000001_create_formation_events_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('formation_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // FormationEventType enum value
            $table->string('message'); // rendered display text, e.g. "Formation upgraded to Active"
            $table->json('meta')->nullable(); // e.g. {"from":"building","to":"active"} or {"wallet_count":14}
            $table->timestamp('created_at')->useCurrent();

            $table->index(['formation_id', 'created_at']);
            $table->index('created_at'); // global ticker query
        });
    }

    public function down(): void {
        Schema::dropIfExists('formation_events');
    }
};