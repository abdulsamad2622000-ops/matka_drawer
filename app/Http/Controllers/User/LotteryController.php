<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LotteryPackage;
use App\Models\LotteryTicket;
use Illuminate\Http\Request;

class LotteryController extends Controller
{
    public function index()
    {
        $lotteries = LotteryPackage::where('status', 'active')
            ->where('draw_date', '>=', now())
            ->orderBy('draw_date')
            ->get();

        $myTickets = auth()->user()->lotteryTickets()
            ->with('lotteryPackage')->latest()->paginate(10);

        return view('user.lotteries.index', compact('lotteries', 'myTickets'));
    }

    public function show(LotteryPackage $lottery)
    {
        $userTickets = auth()->user()->lotteryTickets()
            ->where('lottery_package_id', $lottery->id)->get();
        return view('user.lotteries.show', compact('lottery', 'userTickets'));
    }

    public function buy(Request $request, LotteryPackage $lottery)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:10']);

        $user     = auth()->user();
        $quantity = (int) $request->quantity;
        $total    = $lottery->price * $quantity;

        if ($lottery->status !== 'active') {
            return back()->with('error', 'This lottery is not available.');
        }
        if ($lottery->isSoldOut()) {
            return back()->with('error', 'Lottery is sold out!');
        }
        if ($lottery->availableTickets() < $quantity) {
            return back()->with('error', 'Only ' . $lottery->availableTickets() . ' tickets left!');
        }
        if ($user->wallet_balance < $total) {
            return back()->with('error', 'Insufficient wallet balance! Please top up your wallet.');
        }

        $user->debitWallet($total, 'lottery_purchase', "Purchased {$quantity} ticket(s) for: {$lottery->name}");

        for ($i = 0; $i < $quantity; $i++) {
            LotteryTicket::create([
                'user_id'            => $user->id,
                'lottery_package_id' => $lottery->id,
                'amount_paid'        => $lottery->price,
                'status'             => 'active',
            ]);
        }

        $lottery->increment('sold_tickets', $quantity);

        return back()->with('success', "🎉 Successfully purchased {$quantity} ticket(s)! Good luck!");
    }
}