<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lottery_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lottery_package_id');
            $table->string('ticket_number')->unique();
            $table->decimal('amount_paid', 10, 2);
            $table->enum('status', ['active', 'won', 'lost'])->default('active');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lottery_package_id')->references('id')->on('lottery_packages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lottery_tickets');
    }
};