<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Bet;
use App\Models\PaymentRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $recentBets     = Bet::with('user')->latest()->take(50)->get();
        $recentPayments = PaymentRequest::with('user')->latest()->take(5)->get();

        $stats = [
            'total_users'      => \App\Models\User::where('role', 'user')->count(),
            'pending_payments' => PaymentRequest::where('status', 'pending')->count(),
            'pending_bets'     => Bet::where('status', 'pending')->count(),
            'total_bets'       => Bet::count(),
            'total_bet_amount' => Bet::sum('bet_amount'),
        ];

        return view('admin.dashboard.index', compact('recentBets', 'recentPayments', 'stats'));
    }
}