<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Native MySQL enum() is painful to extend later — widen to a plain
        // string column so new statuses (e.g. "expired") can be added without
        // another schema migration. Laravel 11+ performs this natively for
        // MySQL/PostgreSQL/SQLite without requiring doctrine/dbal.
        Schema::table('users', function (Blueprint $table) {
            $table->string('kyc_status', 20)->default('unsubmitted')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            // Highest tier the user currently has *approved* — null until
            // their first approval. Rejections never clear this; only a
            // fresh approval at a higher tier raises it.
            $table->string('kyc_tier', 20)->nullable()->after('kyc_status');
            $table->text('kyc_rejection_reason')->nullable()->after('kyc_tier');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kyc_tier', 'kyc_rejection_reason']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('kyc_status', ['pending', 'unsubmitted', 'approved', 'rejected'])
                ->default('unsubmitted')
                ->change();
        });
    }
};
