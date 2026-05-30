<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LotteryPackage;
use App\Models\PaymentRequest;
use App\Models\LotteryTicket;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'        => User::where('role', 'user')->count(),
            'pending_payments'   => PaymentRequest::where('status', 'pending')->count(),
            'active_lotteries'   => LotteryPackage::where('status', 'active')->count(),
            'total_tickets_sold' => LotteryTicket::count(),
            'total_revenue'      => LotteryTicket::sum('amount_paid'),
        ];

        $recentPayments = PaymentRequest::with('user')->latest()->take(5)->get();
        $recentTickets  = LotteryTicket::with(['user', 'lotteryPackage'])->latest()->take(5)->get();

        return view('admin.dashboard.index', compact('stats', 'recentPayments', 'recentTickets'));
    }
}