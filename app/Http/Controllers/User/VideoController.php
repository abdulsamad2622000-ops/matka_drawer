<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WinningDraw;

class VideoController extends Controller
{
    public function markViewed(WinningDraw $draw)
    {
        return response()->json([
            'status'  => 'ok',
            'message' => 'Video marked as viewed'
        ]);
    }

    public function checkActive(WinningDraw $draw)
    {
        return response()->json([
            'active'      => $draw->isVideoVisible(),
            'expires_at'  => $draw->video_expires_at,
            'seconds_left'=> max(0, now()->diffInSeconds($draw->video_expires_at, false)),
        ]);
    }
}