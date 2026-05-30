<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lottery_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->decimal('prize_amount', 12, 2);
            $table->integer('total_tickets');
            $table->integer('sold_tickets')->default(0);
            $table->date('draw_date');
            $table->enum('status', ['active', 'closed', 'completed'])->default('active');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lottery_packages');
    }
};