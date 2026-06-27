<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('bet_number')->nullable()->after('user_id');
            $table->string('bet_type')->nullable()->after('bet_number');
            $table->decimal('bet_amount', 10, 2)->nullable()->after('bet_type');
            $table->decimal('potential_win', 10, 2)->nullable()->after('bet_amount');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['bet_number', 'bet_type', 'bet_amount', 'potential_win']);
        });
    }
};