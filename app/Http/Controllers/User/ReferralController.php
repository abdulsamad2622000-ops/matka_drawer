<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index()
    {
        $user          = Auth::user();
        $referredUsers = $user->referredUsers()->latest()->get();
        $bonusPerUser  = (float) Setting::get('referral_bonus', 100);
        $totalEarned   = $referredUsers->count() * $bonusPerUser;

        return view('user.referrals.index', compact(
            'user',
            'referredUsers',
            'bonusPerUser',
            'totalEarned'
        ));
    }
}