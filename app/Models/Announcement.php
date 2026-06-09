<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Announcement extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'video_path',
        'winning_number', 'show_winning_number',
        'video_display_seconds', 'video_expires_at',
        'scheduled_at', 'next_draw_at',
        'is_active', 'created_by',
        'winners_data',
    ];
    protected $casts = [
        'video_expires_at'    => 'datetime',
        'scheduled_at'        => 'datetime',
        'next_draw_at'        => 'datetime',
        'is_active'           => 'boolean',
        'show_winning_number' => 'boolean',
        'winners_data'        => 'array',
    ];
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function isVideoVisible(): bool
    {
        if ($this->scheduled_at && now()->lt($this->scheduled_at)) {
            return false;
        }
        return $this->is_active
            && $this->video_expires_at
            && now()->lt($this->video_expires_at);
    }
    public function isScheduled(): bool
    {
        return $this->scheduled_at && now()->lt($this->scheduled_at);
    }
}