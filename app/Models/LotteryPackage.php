<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'prize_amount',
        'total_tickets', 'sold_tickets', 'draw_date', 'status', 'image'
    ];

    protected $casts = [
        'draw_date'    => 'date',
        'price'        => 'decimal:2',
        'prize_amount' => 'decimal:2',
    ];

    public function tickets()
    {
        return $this->hasMany(LotteryTicket::class);
    }

    public function winningDraw()
    {
        return $this->hasOne(WinningDraw::class);
    }

    public function availableTickets(): int
    {
        return $this->total_tickets - $this->sold_tickets;
    }

    public function isSoldOut(): bool
    {
        return $this->availableTickets() <= 0;
    }
}