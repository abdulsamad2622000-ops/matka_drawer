<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'wallet_balance', 'is_active'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'wallet_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

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

    public function creditWallet(float $amount, string $purpose, string $desc = ''): void
    {
        $this->increment('wallet_balance', $amount);
        $this->walletTransactions()->create([
            'amount'        => $amount,
            'type'          => 'credit',
            'purpose'       => $purpose,
            'description'   => $desc,
            'balance_after' => $this->fresh()->wallet_balance,
        ]);
    }

    public function debitWallet(float $amount, string $purpose, string $desc = ''): bool
    {
        if ($this->wallet_balance < $amount) return false;
        $this->decrement('wallet_balance', $amount);
        $this->walletTransactions()->create([
            'amount'        => $amount,
            'type'          => 'debit',
            'purpose'       => $purpose,
            'description'   => $desc,
            'balance_after' => $this->fresh()->wallet_balance,
        ]);
        return true;
    }
}