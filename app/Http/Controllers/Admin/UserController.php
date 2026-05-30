<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')
            ->withCount(['lotteryTickets', 'paymentRequests'])
            ->latest()
            ->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $tickets      = $user->lotteryTickets()->with('lotteryPackage')->latest()->get();
        $transactions = $user->walletTransactions()->latest()->get();
        $payments     = $user->paymentRequests()->latest()->get();
        return view('admin.users.show', compact('user', 'tickets', 'transactions', 'payments'));
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully.");
    }
}