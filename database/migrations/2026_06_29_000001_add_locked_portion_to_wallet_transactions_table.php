<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Records how much of a debit transaction came out of locked_balance vs
 * genuinely-unlocked balance. This is what makes a refund an exact reversal
 * rather than a flat credit — without it, refunding a pack purchase that
 * was paid for with locked deposit funds would hand back unlocked
 * (withdrawable) money, which is the exact laundering path we're closing.
 *
 * Null/0 for credit transactions — only debits ever populate this.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->decimal('locked_portion', 24, 8)->nullable()->after('amount');
        });
    }

    public function down(): void {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn('locked_portion');
        });
    }
};