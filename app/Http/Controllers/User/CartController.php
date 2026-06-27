<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Bet;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::where('user_id', auth()->id())->latest()->get();
        $total     = $cartItems->sum('bet_amount');
        return view('user.cart.index', compact('cartItems', 'total'));
    }

    public function addBet(Request $request)
    {
        $request->validate([
            'bet_number' => 'required|string|max:4',
            'bet_type'   => 'required|in:1x7,1x70,1x700,1x7000',
            'bet_amount' => 'required|numeric|min:1',
        ]);

        $multiplier = match($request->bet_type) {
            '1x7'    => 7,
            '1x70'   => 70,
            '1x700'  => 700,
            '1x7000' => 7000,
            default  => 7,
        };

        $padLength = match($request->bet_type) {
            '1x7'    => 1,
            '1x70'   => 2,
            '1x700'  => 3,
            '1x7000' => 4,
            default  => 1,
        };

        $betNumber    = str_pad($request->bet_number, $padLength, '0', STR_PAD_LEFT);
        $potentialWin = $request->bet_amount * $multiplier;

        CartItem::create([
            'user_id'       => auth()->id(),
            'bet_number'    => $betNumber,
            'bet_type'      => $request->bet_type,
            'bet_amount'    => $request->bet_amount,
            'potential_win' => $potentialWin,
        ]);

        return response()->json([
            'success'    => true,
            'message'    => "Added to cart!",
            'cart_count' => CartItem::where('user_id', auth()->id())->count(),
        ]);
    }

    public function remove(CartItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }
        $cartItem->delete();
        return back()->with('success', '🗑️ Removed from cart.');
    }

    public function checkout()
    {
        $user      = auth()->user();
        $cartItems = CartItem::where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty!');
        }

        $total = $cartItems->sum('bet_amount');

        if ($user->wallet_balance < $total) {
            return back()->with('error', 'Insufficient wallet balance! Please top up your wallet.');
        }

        foreach ($cartItems as $item) {
            $user->debitWallet(
                $item->bet_amount,
                'lottery_purchase',
                "Bet placed on number {$item->bet_number} ({$item->bet_type})"
            );

            Bet::create([
                'user_id'       => $user->id,
                'bet_number'    => $item->bet_number,
                'bet_amount'    => $item->bet_amount,
                'bet_type'      => $item->bet_type,
                'potential_win' => $item->potential_win,
                'status'        => 'pending',
            ]);
        }

        CartItem::where('user_id', $user->id)->delete();

        return redirect()->route('user.bets.index')
            ->with('success', '🎉 All bets placed successfully!');
    }
}