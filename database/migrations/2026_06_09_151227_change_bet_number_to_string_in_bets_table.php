<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pehle existing data ko string mein convert karo
        DB::statement("ALTER TABLE bets MODIFY COLUMN bet_number VARCHAR(10) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bets MODIFY COLUMN bet_number INT NOT NULL");
    }
};