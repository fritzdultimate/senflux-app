<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('referral_bonuses', function (Blueprint $table) {
            // Existing rows (all deposit-sourced, all paid instantly) default
            // to 'confirmed' — nothing about historical data needs revisiting.
            // Every NEW row from here on is pack-purchase-sourced and starts
            // 'pending' until the 3-day refund window closes unrefunded.
            $table->string('status')->default('confirmed')->after('amount');

            $table->foreignId('pack_subscription_id')->nullable()
                ->after('deposit_id')
                ->constrained('pack_subscriptions')->nullOnDelete();

            // Safe even if already nullable — pack-purchase-sourced rows
            // going forward won't have a deposit_id at all.
            $table->foreignId('deposit_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('referral_bonuses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pack_subscription_id');
            $table->dropColumn('status');
        });
    }
};