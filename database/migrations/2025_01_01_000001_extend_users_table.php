<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rank', 30)->default('none')->after('balances');
            $table->timestamp('rank_achieved_at')->nullable()->after('rank');
            $table->string('subscription_plan', 20)->nullable()->after('rank_achieved_at');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_plan');
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_submitted_at');
            $table->timestamp('last_login_at')->nullable()->after('kyc_verified_at');
            $table->boolean('is_active')->default(true)->after('last_login_at');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn([
                'rank', 'rank_achieved_at',
                'subscription_plan', 'subscription_expires_at', 
                'last_login_at', 'is_active', 'deleted_at', 'kyc_verified_at'
            ]);
        });
    }
};
