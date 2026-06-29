<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pack_slots', function (Blueprint $table) {
            $table->foreignId('formation_id')->nullable()
                ->after('status')
                ->constrained('formations')->nullOnDelete();
            $table->timestamp('deployed_at')->nullable()->after('formation_id');
        });
    }

    public function down(): void
    {
        Schema::table('pack_slots', function (Blueprint $table) {
            $table->dropConstrainedForeignId('formation_id');
            $table->dropColumn('deployed_at');
        });
    }
};
