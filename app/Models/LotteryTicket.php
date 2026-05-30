<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'lottery_package_id', 'ticket_number', 'amount_paid', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lotteryPackage()
    {
        return $this->belongsTo(LotteryPackage::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ticket) {
            $ticket->ticket_number = 'LT-' . date('Y') . '-' . str_pad(
                LotteryTicket::count() + 1, 6, '0', STR_PAD_LEFT
            );
        });
    }
}