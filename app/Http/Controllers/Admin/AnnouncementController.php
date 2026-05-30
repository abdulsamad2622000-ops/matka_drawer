<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')->latest()->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'                 => 'required|string|max:255',
            'description'           => 'nullable|string',
            'video'                 => 'required|mimetypes:video/mp4,video/avi,video/quicktime|max:102400',
            'video_display_seconds' => 'required|integer|min:10|max:300',
        ]);

        // Deactivate all previous announcements
        Announcement::where('is_active', true)->update(['is_active' => false]);

        $videoPath = $request->file('video')->store('announcements', 'public');

        Announcement::create([
            'title'                 => $request->title,
            'description'           => $request->description,
            'video_path'            => $videoPath,
            'video_display_seconds' => $request->video_display_seconds,
            'video_expires_at'      => now()->addSeconds($request->video_display_seconds),
            'is_active'             => true,
            'created_by'            => auth()->id(),
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement broadcasted! All users will see the video.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->update(['is_active' => false]);
        return back()->with('success', 'Announcement deactivated.');
    }
}