<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\WithdrawalRequest;
class DashboardController extends Controller
{
    public function index()
    {
        $user         = auth()->user();
        $myTickets    = $user->lotteryTickets()->latest()->take(5)->get();
        $transactions = $user->walletTransactions()->latest()->take(5)->get();
        $myBets       = $user->bets()->whereIn('status', ['pending', 'won'])->latest()->take(5)->get();
        $withdrawals  = WithdrawalRequest::where('user_id', $user->id)->latest()->take(5)->get();
        $activeAnnouncement = Announcement::where('is_active', true)
            ->latest()
            ->first();
        return view('user.dashboard.index', compact(
            'user', 'myTickets', 'transactions', 'activeAnnouncement', 'myBets', 'withdrawals'
        ));
    }
}