<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WinningDraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'lottery_package_id', 'winning_ticket_number', 'winner_user_id',
        'prize_amount', 'announcement_video_path', 'video_display_seconds',
        'video_expires_at', 'video_active'
    ];

    protected $casts = [
        'video_expires_at' => 'datetime',
        'video_active'     => 'boolean',
    ];

    public function lotteryPackage()
    {
        return $this->belongsTo(LotteryPackage::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function isVideoVisible(): bool
    {
        return $this->video_active
            && $this->video_expires_at
            && now()->lt($this->video_expires_at);
    }
}