<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use Illuminate\Http\Request;

class BetController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $myBets = $user->bets()->latest()->paginate(10);
        return view('user.bets.index', compact('myBets'));
    }

    public function store(Request $request)
    {
        $request->validate([
           'bet_number' => 'required|string|max:4',

            'bet_amount' => 'required|numeric|min:1',
            'bet_type'   => 'required|in:1x7,1x70,1x700,1x7000',
        ]);

        $user = auth()->user();

        if ($user->wallet_balance < $request->bet_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance!'
            ]);
        }

        $multiplier   = match($request->bet_type) {
            '1x7'    => 7,
            '1x70'   => 70,
            '1x700'  => 700,
            '1x7000' => 7000,
            default  => 7,
        };

        $potentialWin = $request->bet_amount * $multiplier;

        $user->debitWallet(
            $request->bet_amount,
            'lottery_purchase',
            "Bet placed on number {$request->bet_number} ({$request->bet_type})"
        );

        Bet::create([
            'user_id'       => $user->id,
            'bet_number'    => $request->bet_number,
            'bet_amount'    => $request->bet_amount,
            'bet_type'      => $request->bet_type,
            'potential_win' => $potentialWin,
            'status'        => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Bet placed! Potential win: Rs. " . number_format($potentialWin, 0),
            'balance' => number_format($user->fresh()->wallet_balance, 0),
        ]);
    }
}