<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->boolean('auto_managed')->default(false)->after('is_active');
            $table->unsignedInteger('previous_score')->nullable()->after('score');
            $table->unsignedBigInteger('active_wallets')->nullable()->after('wallet_quality');
            $table->timestamp('wallet_data_synced_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn(['auto_managed', 'previous_score', 'active_wallets', 'wallet_data_synced_at']);
        });
    }
};