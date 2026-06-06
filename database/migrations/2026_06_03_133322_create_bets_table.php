<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lottery_package_id')->constrained()->onDelete('cascade');
            $table->integer('bet_number');
            $table->decimal('bet_amount', 10, 2);
            $table->enum('bet_type', ['1x7', '1x70', '1x700', '1x7000']);
            $table->decimal('potential_win', 12, 2);
            $table->enum('status', ['pending', 'won', 'lost'])->default('pending');
            $table->decimal('win_amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};