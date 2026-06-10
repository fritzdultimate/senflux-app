<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->date('dob')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->json('balances')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('country')->nullable();
            $table->string('timezone')->nullable();
            $table->string('affiliate_code')->unique()->nullable();
            $table->foreignId('referrer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('kyc_status', [
                'pending',
                'unsubmitted',
                'approved',
                'rejected'
            ])->default('unsubmitted');
            $table->timestamp('kyc_submitted_at')->nullable();
            $table->boolean('two_factor_enable')->default(false);
            $table->timestamp('blocked_at')->nullable();
            $table->timestamp('suspended_at')->nullable();

            $table->boolean('notify_login_attempts')->default(false);

            $table->boolean('notify_email_notifications')->default(true);
            $table->boolean('notify_deposit_alerts')->default(true);
            $table->boolean('notify_withdrawal_alerts')->default(true);
            $table->boolean('notify_security_alerts')->default(true);

            $table->string('vpss')->nullable();
            $table->boolean('has_seen_tour')->default(false);

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
