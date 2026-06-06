<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN purpose ENUM(
            'deposit',
            'withdrawal',
            'refund',
            'referral_bonus',
            'lottery_purchase',
            'lottery_win',
            'bet_placed',
            'bet_win'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN purpose ENUM(
            'deposit',
            'refund',
            'referral_bonus',
            'lottery_purchase',
            'lottery_win',
            'bet_placed',
            'bet_win'
        ) NOT NULL");
    }
};