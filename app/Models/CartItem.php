<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'lottery_package_id', 'quantity', 'price_per_ticket', 'total_price'
    ];

    protected $casts = [
        'price_per_ticket' => 'decimal:2',
        'total_price'      => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lotteryPackage()
    {
        return $this->belongsTo(LotteryPackage::class);
    }
}