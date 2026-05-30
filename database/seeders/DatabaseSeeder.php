<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LotteryPackage;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'           => 'Super Admin',
            'email'          => 'admin@lottoapp.com',
            'phone'          => '03001234567',
            'password'       => Hash::make('admin123'),
            'role'           => 'admin',
            'wallet_balance' => 0,
            'is_active'      => true,
        ]);

        // Demo Users
        $users = [
            ['name' => 'Ali Hassan',   'email' => 'ali@demo.com',    'wallet_balance' => 5000],
            ['name' => 'Sara Khan',    'email' => 'sara@demo.com',   'wallet_balance' => 2500],
            ['name' => 'Usman Ahmed',  'email' => 'usman@demo.com',  'wallet_balance' => 10000],
            ['name' => 'Fatima Malik', 'email' => 'fatima@demo.com', 'wallet_balance' => 750],
        ];

        foreach ($users as $u) {
            User::create(array_merge($u, [
                'password'  => Hash::make('user123'),
                'role'      => 'user',
                'is_active' => true,
            ]));
        }

        // Dummy Lotteries
        $lotteries = [
            [
                'name'          => '💎 Diamond Jackpot',
                'description'   => 'Premium lottery with the biggest prize pool. Win a life-changing amount!',
                'price'         => 500,
                'prize_amount'  => 500000,
                'total_tickets' => 1000,
                'sold_tickets'  => 0,
                'draw_date'     => now()->addDays(14),
                'status'        => 'active',
            ],
            [
                'name'          => '🥇 Gold Rush',
                'description'   => 'Monthly gold rush lottery. Affordable tickets, massive prize.',
                'price'         => 200,
                'prize_amount'  => 200000,
                'total_tickets' => 500,
                'sold_tickets'  => 0,
                'draw_date'     => now()->addDays(7),
                'status'        => 'active',
            ],
            [
                'name'          => '🚀 Mega Launch',
                'description'   => 'Weekly mega launch lottery. New draw every week!',
                'price'         => 100,
                'prize_amount'  => 50000,
                'total_tickets' => 300,
                'sold_tickets'  => 0,
                'draw_date'     => now()->addDays(3),
                'status'        => 'active',
            ],
            [
                'name'          => '⚡ Flash Win',
                'description'   => 'Quick daily lottery. Small investment, real rewards.',
                'price'         => 50,
                'prize_amount'  => 10000,
                'total_tickets' => 200,
                'sold_tickets'  => 0,
                'draw_date'     => now()->addDays(1),
                'status'        => 'active',
            ],
            [
                'name'          => '🏆 Champion Draw',
                'description'   => 'Bi-weekly champion draw. Be the champion of this month!',
                'price'         => 1000,
                'prize_amount'  => 1000000,
                'total_tickets' => 1000,
                'sold_tickets'  => 0,
                'draw_date'     => now()->addDays(21),
                'status'        => 'active',
            ],
        ];

        foreach ($lotteries as $l) {
            LotteryPackage::create($l);
        }

        $this->command->info('✅ Admin: admin@lottoapp.com / admin123');
        $this->command->info('✅ User:  ali@demo.com / user123');
    }
}