<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\LotteryPackage;
use App\Models\LotteryTicket;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::where('user_id', auth()->id())
            ->with('lotteryPackage')
            ->get();

        $total = $cartItems->sum('total_price');

        return view('user.cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, LotteryPackage $lottery)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $quantity = (int) $request->quantity;

        // Check if already in cart
        $existing = CartItem::where('user_id', auth()->id())
            ->where('lottery_package_id', $lottery->id)
            ->first();

        if ($existing) {
            $newQty = $existing->quantity + $quantity;
            $existing->update([
                'quantity'    => $newQty,
                'total_price' => $lottery->price * $newQty,
            ]);
        } else {
            CartItem::create([
                'user_id'            => auth()->id(),
                'lottery_package_id' => $lottery->id,
                'quantity'           => $quantity,
                'price_per_ticket'   => $lottery->price,
                'total_price'        => $lottery->price * $quantity,
            ]);
        }

        return back()->with('success', 'Added to cart!');
    }

    public function remove(CartItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }
        $cartItem->delete();
        return back()->with('success', 'Removed from cart.');
    }

    public function checkout()
    {
        $user      = auth()->user();
        $cartItems = CartItem::where('user_id', $user->id)
            ->with('lotteryPackage')
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty!');
        }

        $total = $cartItems->sum('total_price');

        if ($user->wallet_balance < $total) {
            return back()->with('error', 'Insufficient wallet balance! Please top up your wallet.');
        }

        foreach ($cartItems as $item) {
            $lottery = $item->lotteryPackage;

            if ($lottery->status !== 'active') {
                return back()->with('error', $lottery->name . ' is no longer available.');
            }

            if ($lottery->availableTickets() < $item->quantity) {
                return back()->with('error', 'Not enough tickets for ' . $lottery->name);
            }

            // Debit wallet
            $user->debitWallet(
                $item->total_price,
                'lottery_purchase',
                "Purchased {$item->quantity} ticket(s) for: {$lottery->name}"
            );

            // Create tickets
            for ($i = 0; $i < $item->quantity; $i++) {
                LotteryTicket::create([
                    'user_id'            => $user->id,
                    'lottery_package_id' => $lottery->id,
                    'amount_paid'        => $lottery->price,
                    'status'             => 'active',
                ]);
            }

            // Update sold count
            $lottery->increment('sold_tickets', $item->quantity);
        }

        // Clear cart
        CartItem::where('user_id', $user->id)->delete();

        return redirect()->route('user.dashboard')
            ->with('success', '🎉 Checkout successful! Your tickets have been issued.');
    }
}