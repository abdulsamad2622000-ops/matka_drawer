<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->timestamp('scheduled_at')->nullable()->after('video_expires_at');
            $table->timestamp('next_draw_at')->nullable()->after('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['scheduled_at', 'next_draw_at']);
        });
    }
};