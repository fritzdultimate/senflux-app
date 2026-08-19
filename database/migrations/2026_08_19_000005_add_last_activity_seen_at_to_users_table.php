<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Durable "last time this user opened their activity feed" marker,
            // used to compute the unread dot on the header bell without
            // relying on a cache entry that can be evicted or expire.
            $table->timestamp('last_activity_seen_at')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_activity_seen_at');
        });
    }
};
