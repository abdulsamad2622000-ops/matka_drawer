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
            'winning_number'        => 'required|string|max:100',
            'scheduled_at'          => 'nullable|date',
        ]);

        Announcement::where('is_active', true)->update(['is_active' => false]);

        $videoPath   = $request->file('video')->store('announcements', 'public');
        $scheduledAt = $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at) : now();
        $expiresAt   = $scheduledAt->copy()->addSeconds((int) $request->video_display_seconds);

        // Winners data generate karo
        $winnersData = $this->generateWinnersData($request->winning_number);

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
            'winners_data'          => $winnersData,
        ]);

        if ($request->winning_number) {
            $this->processBetResults($request->winning_number);
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement broadcasted! All users will see the video.');
    }

    /**
     * Winners data generate karo — real + fake
     */
    private function generateWinnersData(string $winningNumber): array
    {
        $winners = [];

        // ── Real Winners ─────────────────────────────────────
        $realWinners = Bet::where('status', 'pending')
            ->where('bet_number', $winningNumber)
            ->with('user')
            ->get();

        foreach ($realWinners as $bet) {
            $winAmount = $bet->bet_amount * $bet->multiplier;
            $winners[] = [
                'name'       => $this->maskName($bet->user->name),
                'amount'     => $winAmount,
                'is_real'    => true,
                'bet_type'   => $bet->bet_type,
            ];
        }

        // ── Fake Winners (10 total) ───────────────────────────
        $fakeNames = [
            'Ali H.', 'Sara K.', 'Ahmed R.', 'Fatima M.', 'Usman T.',
            'Ayesha N.', 'Bilal S.', 'Hina A.', 'Zara Q.', 'Hassan F.',
            'Nadia J.', 'Imran B.', 'Sana W.', 'Kamran D.', 'Mehwish L.',
        ];

        $betTypes = ['1x7', '1x70', '1x700', '1x7000'];
        $multipliers = ['1x7' => 7, '1x70' => 70, '1x700' => 700, '1x7000' => 7000];

        shuffle($fakeNames);
        $needed = max(0, 10 - count($winners));

        for ($i = 0; $i < $needed; $i++) {
            $betType   = $betTypes[array_rand($betTypes)];
            $betAmount = rand(1, 10) * 1000; // Rs. 1000 to 10,000
            $winAmount = $betAmount * $multipliers[$betType];

            $winners[] = [
                'name'     => $fakeNames[$i % count($fakeNames)],
                'amount'   => $winAmount,
                'is_real'  => false,
                'bet_type' => $betType,
            ];
        }

        // Shuffle so real winners mix with fake
        shuffle($winners);

        return $winners;
    }

    /**
     * Name mask karo — Ali Hassan → Ali H***
     */
    private function maskName(string $name): string
    {
        $parts = explode(' ', $name);
        if (count($parts) >= 2) {
            return $parts[0] . ' ' . substr($parts[1], 0, 1) . '***';
        }
        return substr($name, 0, 3) . '***';
    }

    private function processBetResults(string $winningNumber): void
    {
        $pendingBets = Bet::where('status', 'pending')
                          ->with('user')
                          ->get();

        if ($pendingBets->isEmpty()) return;

        DB::transaction(function () use ($pendingBets, $winningNumber) {
            foreach ($pendingBets as $bet) {
                if ((string) $bet->bet_number === (string) $winningNumber) {
                    $winAmount = $bet->bet_amount * $bet->multiplier;
                    $bet->update(['status' => 'won', 'win_amount' => $winAmount]);
                    $bet->user->creditWallet(
                        $winAmount,
                        'bet_win',
                        "Bet #{$bet->id} jeet gaye! Number: {$bet->bet_number} | {$bet->bet_type}"
                    );
                } else {
                    $bet->update(['status' => 'lost', 'win_amount' => 0]);
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
        $request->validate(['next_draw_at' => 'required|date']);
        $announcement->update([
            'next_draw_at' => \Carbon\Carbon::parse($request->next_draw_at),
        ]);
        return back()->with('success', 'Next draw date set successfully!');
    }
}