<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('winning_draws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lottery_package_id')->constrained()->onDelete('cascade');
            $table->string('winning_ticket_number');
            $table->foreignId('winner_user_id')->nullable()->constrained('users');
            $table->decimal('prize_amount', 12, 2);
            $table->string('announcement_video_path')->nullable();
            $table->integer('video_display_seconds')->default(30);
            $table->timestamp('video_expires_at')->nullable();
            $table->boolean('video_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('winning_draws');
    }
};