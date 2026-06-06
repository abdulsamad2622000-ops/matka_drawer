<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['bank', 'jazzcash', 'easypaisa']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Bank fields
            $table->string('bank_name')->nullable();
            $table->string('account_title')->nullable();
            $table->string('account_number')->nullable();

            // JazzCash / EasyPaisa fields
            $table->string('mobile_number')->nullable();
            $table->string('account_holder')->nullable();

            $table->text('admin_note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};