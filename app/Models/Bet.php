<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'bet_number',
        'bet_amount', 'bet_type', 'potential_win', 'status', 'win_amount'
    ];

    protected $casts = [
        'bet_amount'    => 'decimal:2',
        'potential_win' => 'decimal:2',
        'win_amount'    => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getMultiplierAttribute(): int
    {
        return match($this->bet_type) {
            '1x7'    => 7,
            '1x70'   => 70,
            '1x700'  => 700,
            '1x7000' => 7000,
            default  => 7,
        };
    }
}