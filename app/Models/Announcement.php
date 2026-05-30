<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'video_path',
        'video_display_seconds', 'video_expires_at',
        'is_active', 'created_by'
    ];

    protected $casts = [
        'video_expires_at' => 'datetime',
        'is_active'        => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isVideoVisible(): bool
    {
        return $this->is_active
            && $this->video_expires_at
            && now()->lt($this->video_expires_at);
    }
}