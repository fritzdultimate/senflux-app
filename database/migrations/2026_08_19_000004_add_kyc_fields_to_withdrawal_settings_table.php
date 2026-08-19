<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawal_settings', function (Blueprint $table) {
            $table->boolean('kyc_required')->default(true)->after('is_enabled');
            // Max a Basic-only verified user may withdraw per request. Null = no
            // extra cap beyond the existing max_amount. Enhanced-tier users are
            // never limited by this field.
            $table->decimal('basic_tier_daily_limit', 15, 2)->nullable()->after('kyc_required');
        });
    }

    public function down(): void
    {
        Schema::table('withdrawal_settings', function (Blueprint $table) {
            $table->dropColumn(['kyc_required', 'basic_tier_daily_limit']);
        });
    }
};
