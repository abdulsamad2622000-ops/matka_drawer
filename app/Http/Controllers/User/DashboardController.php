<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $user         = auth()->user();
        $myTickets    = $user->lotteryTickets()->with('lotteryPackage')->latest()->take(5)->get();
        $transactions = $user->walletTransactions()->latest()->take(5)->get();

        // Global announcement video
        $activeAnnouncement = Announcement::where('is_active', true)
            ->where('video_expires_at', '>', now())
            ->latest()
            ->first();

        return view('user.dashboard.index', compact(
            'user', 'myTickets', 'transactions', 'activeAnnouncement'
        ));
    }
}