<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Bet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'winning_number'        => 'nullable|string|max:100',
            'scheduled_at'          => 'nullable|date',
        ]);

        Announcement::where('is_active', true)->update(['is_active' => false]);

        $videoPath   = $request->file('video')->store('announcements', 'public');
        $scheduledAt = $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at) : now();
        $expiresAt   = $scheduledAt->copy()->addSeconds((int) $request->video_display_seconds);

        Announcement::create([
            'title'                 => $request->title,
            'description'           => $request->description,
            'video_path'            => $videoPath,
            'winning_number'        => $request->winning_number,
            'show_winning_number'   => $request->winning_number ? true : false,
            'video_display_seconds' => (int) $request->video_display_seconds,
            'video_expires_at'      => $expiresAt,
            'scheduled_at'          => $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at) : null,
            'next_draw_at'          => null,
            'is_active'             => true,
            'created_by'            => auth()->id(),
        ]);

        // ─── Bet Result Processing ────────────────────────────────────────
        if ($request->winning_number) {
            $this->processBetResults($request->winning_number);
        }
        // ─────────────────────────────────────────────────────────────────

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement broadcasted! All users will see the video.');
    }

    /**
     * Pending bets check karo, won/lost mark karo, wallet credit karo.
     */
    private function processBetResults(string $winningNumber): void
    {
        // Sirf pending bets — eager load user taake creditWallet() kaam kare
        $pendingBets = Bet::where('status', 'pending')
                          ->with('user')
                          ->get();

        if ($pendingBets->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($pendingBets, $winningNumber) {
            foreach ($pendingBets as $bet) {

                if ((string) $bet->bet_number === (string) $winningNumber) {

                    // ── WON ──────────────────────────────────────────────
                    $winAmount = $bet->bet_amount * $bet->multiplier;

                    $bet->update([
                        'status'     => 'won',
                        'win_amount' => $winAmount,
                    ]);

                    // User ka wallet credit karo — transaction log bhi khud banta hai
                    $bet->user->creditWallet(
                        $winAmount,
                        'bet_win',
                        "Bet #{$bet->id} jeet gaye! Number: {$bet->bet_number} | {$bet->bet_type}"
                    );

                } else {

                    // ── LOST ─────────────────────────────────────────────
                    $bet->update([
                        'status'     => 'lost',
                        'win_amount' => 0,
                    ]);
                }
            }
        });
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->update(['is_active' => false]);
        return back()->with('success', 'Announcement deactivated.');
    }

    public function removeWinningNumber(Announcement $announcement)
    {
        $announcement->update([
            'winning_number'      => null,
            'show_winning_number' => false,
        ]);
        return back()->with('success', 'Winning number removed.');
    }

    public function setNextDraw(Request $request, Announcement $announcement)
    {
        $request->validate([
            'next_draw_at' => 'required|date',
        ]);

        $announcement->update([
            'next_draw_at' => \Carbon\Carbon::parse($request->next_draw_at),
        ]);

        return back()->with('success', 'Next draw date set successfully!');
    }
}