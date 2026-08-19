<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('tier', 20); // basic | enhanced
            $table->string('status', 20)->default('pending'); // pending | approved | rejected

            // Identity document (required for both tiers)
            $table->string('id_document_type', 30)->nullable(); // passport | national_id | drivers_license
            $table->text('id_document_number')->nullable(); // encrypted
            $table->string('id_front_path')->nullable();
            $table->string('id_back_path')->nullable();
            $table->string('selfie_path')->nullable();

            // Enhanced tier only
            $table->string('proof_of_address_path')->nullable();

            // Review trail
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Provider-agnostic hook for future auto-verification (Sumsub/Veriff/Onfido/etc.)
            $table->string('provider')->nullable();
            $table->string('provider_reference_id')->nullable();
            $table->string('provider_status')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'tier', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_submissions');
    }
};
