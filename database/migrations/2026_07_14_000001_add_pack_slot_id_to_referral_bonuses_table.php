<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referral_bonuses', function (Blueprint $table) {
            $table->foreignId('pack_slot_id')->nullable()
                ->after('pack_subscription_id')
                ->constrained('pack_slots')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('referral_bonuses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pack_slot_id');
        });
    }
};
