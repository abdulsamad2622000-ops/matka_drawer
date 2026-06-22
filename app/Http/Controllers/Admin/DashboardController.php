<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PaymentRequest;
use App\Models\Bet;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'      => User::where('role', 'user')->count(),
            'pending_payments' => PaymentRequest::where('status', 'pending')->count(),
            'total_bets'       => Bet::count(),
            'pending_bets'     => Bet::where('status', 'pending')->count(),
            'total_bet_amount' => Bet::sum('bet_amount'),
        ];

        $recentPayments = PaymentRequest::with('user')->latest()->take(5)->get();
       $recentBets = Bet::with('user')->orderByDesc('bet_amount')->take(20)->get();

        return view('admin.dashboard.index', compact(
            'stats', 'recentPayments', 'recentBets'
        ));
    }
}