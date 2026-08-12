<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('email', 255)->index();
            $table->string('company', 150)->nullable();
            $table->string('subject', 150);
            $table->text('message');

            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_submissions');
    }
};
