<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
         'name', 'email', 'phone', 'password', 'role', 'wallet_balance', 'is_active',
    'referral_code', 'referred_by', 'referral_bonus', 'referral_bonus_balance'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
         'wallet_balance'         => 'decimal:2',
    'referral_bonus_balance' => 'decimal:2',
    'is_active'              => 'boolean',
    ];

    // ─── Roles ───────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // ─── Relationships ───────────────────────────────────
    public function paymentRequests()
    {
        return $this->hasMany(PaymentRequest::class);
    }

    public function lotteryTickets()
    {
        return $this->hasMany(LotteryTicket::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }




// ✅ Yeh add karo
public function withdrawalRequests()
{
    return $this->hasMany(WithdrawalRequest::class);
}




    // ─── Wallet Methods ──────────────────────────────────
    public function creditWallet(float $amount, string $purpose, string $desc = ''): void
    {
        DB::transaction(function () use ($amount, $purpose, $desc) {
            $this->increment('wallet_balance', $amount);
            $this->walletTransactions()->create([
                'amount'        => $amount,
                'type'          => 'credit',
                'purpose'       => $purpose,
                'description'   => $desc,
                'balance_after' => $this->fresh()->wallet_balance,
            ]);
        });
    }

    public function debitWallet(float $amount, string $purpose, string $desc = ''): bool
    {
        if ($this->wallet_balance < $amount) return false;

        DB::transaction(function () use ($amount, $purpose, $desc) {
            $this->decrement('wallet_balance', $amount);
            $this->walletTransactions()->create([
                'amount'        => $amount,
                'type'          => 'debit',
                'purpose'       => $purpose,
                'description'   => $desc,
                'balance_after' => $this->fresh()->wallet_balance,
            ]);
        });

        return true;
    }
}