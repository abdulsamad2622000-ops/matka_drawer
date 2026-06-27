<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bet_number',
        'bet_type',
        'bet_amount',
        'potential_win',
    ];

    protected $casts = [
        'bet_amount'    => 'decimal:2',
        'potential_win' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}