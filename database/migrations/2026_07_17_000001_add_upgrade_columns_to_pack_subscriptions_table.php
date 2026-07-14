<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pack_subscriptions', function (Blueprint $table) {
            $table->foreignId('upgraded_from_tier_id')->nullable()->after('pack_tier_id')->constrained('pack_tiers')->nullOnDelete();
            $table->timestamp('upgraded_at')->nullable()->after('matures_at');
            $table->foreignId('upgrade_transaction_id')->nullable()->after('purchase_transaction_id')->constrained('wallet_transactions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pack_subscriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('upgraded_from_tier_id');
            $table->dropColumn('upgraded_at');
            $table->dropConstrainedForeignId('upgrade_transaction_id');
        });
    }
};