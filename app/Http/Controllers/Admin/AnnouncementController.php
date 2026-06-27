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
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'video'            => 'nullable|mimetypes:video/mp4,video/avi,video/quicktime|max:102400',
            'video_play_count' => 'nullable|integer|min:1|max:100',
            'winning_number'   => 'nullable|string|max:100',
            'extra_message'    => 'nullable|string|max:500',
        ]);

        Announcement::where('is_active', true)->update(['is_active' => false]);

        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('announcements', 'public');
        }

        $scheduledAt = now();
        $videoDisplaySeconds = $videoPath ? 999999 : 0;
        $expiresAt = $videoPath ? $scheduledAt->copy()->addSeconds($videoDisplaySeconds) : null;

        $winnersData = $request->winning_number
            ? $this->generateWinnersData($request->winning_number)
            : $this->generateFakeWinnersOnly();

        Announcement::create([
            'title'                 => $request->title,
            'description'           => $request->description,
            'video_path'            => $videoPath,
            'winning_number'        => $request->winning_number,
            'show_winning_number'   => $request->winning_number ? true : false,
            'video_display_seconds' => $videoDisplaySeconds,
            'video_expires_at'      => $expiresAt,
            'video_play_count'      => $request->video_play_count ?? 1,
            'extra_message'         => $request->extra_message,
            'show_winners_slide'    => $request->boolean('show_winners_slide', true),
            'scheduled_at'          => null,
            'next_draw_at'          => null,
            'is_active'             => true,
            'created_by'            => auth()->id(),
            'winners_data'          => $winnersData,
        ]);

        if ($request->winning_number) {
            $this->processBetResults($request->winning_number);
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement broadcasted! All users will see it.');
    }

    private function generateWinnersData(string $winningNumber): array
    {
        $winners = [];

        $realWinners = Bet::where('status', 'pending')
            ->where('bet_number', $winningNumber)
            ->with('user')
            ->get();

        foreach ($realWinners as $bet) {
            $winAmount = $bet->bet_amount * $bet->multiplier;
            $winners[] = [
                'name'     => $this->maskName($bet->user->name),
                'amount'   => $winAmount,
                'is_real'  => true,
                'bet_type' => $bet->bet_type,
            ];
        }

        $needed = max(0, 10 - count($winners));
        $fakeWinners = $this->makeFakeWinners($needed);
        $winners = array_merge($winners, $fakeWinners);

        shuffle($winners);
        return $winners;
    }

    private function generateFakeWinnersOnly(): array
    {
        return $this->makeFakeWinners(10);
    }

    private function makeFakeWinners(int $count): array
    {
        $allNames = [
            'Ali H.', 'Sara K.', 'Ahmed R.', 'Fatima M.', 'Usman T.',
            'Ayesha N.', 'Bilal S.', 'Hina A.', 'Zara Q.', 'Hassan F.',
            'Nadia J.', 'Imran B.', 'Sana W.', 'Kamran D.', 'Mehwish L.',
            'Tariq A.', 'Rabia M.', 'Shahid K.', 'Amna Z.', 'Faisal R.',
            'Maryam H.', 'Junaid S.', 'Saima B.', 'Aqib N.', 'Lubna T.',
        ];

        shuffle($allNames);
        $selectedNames = array_slice($allNames, 0, $count);

        $betTypes = ['1x7', '1x70', '1x700', '1x7000'];

        $winners = [];
        foreach ($selectedNames as $name) {
            $betType   = $betTypes[array_rand($betTypes)];
            $winAmount = rand(1, 100) * 1000;

            $winners[] = [
                'name'     => $name,
                'amount'   => $winAmount,
                'is_real'  => false,
                'bet_type' => $betType,
            ];
        }

        return $winners;
    }

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
        $pendingBets = Bet::where('status', 'pending')->with('user')->get();
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

    public function toggleWinners(Announcement $announcement)
    {
        $announcement->update([
            'show_winners_slide' => !$announcement->show_winners_slide,
        ]);
        $status = $announcement->show_winners_slide ? 'OFF' : 'ON';
        return back()->with('success', "Winners slide turned {$status}.");
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