<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $user         = auth()->user();
        $myTickets    = $user->lotteryTickets()->latest()->take(5)->get();
        $transactions = $user->walletTransactions()->latest()->take(5)->get();
        $myBets       = $user->bets()->latest()->take(5)->get();

        $activeAnnouncement = Announcement::where('is_active', true)
            ->latest()
            ->first();

        return view('user.dashboard.index', compact(
            'user', 'myTickets', 'transactions', 'activeAnnouncement', 'myBets'
        ));
    }
}