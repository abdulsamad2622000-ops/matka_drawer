<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LotteryPackage;
use App\Models\LotteryTicket;
use App\Models\WinningDraw;
use Illuminate\Http\Request;

class LotteryController extends Controller
{
    public function index()
    {
        $lotteries = LotteryPackage::withCount('tickets')->latest()->paginate(15);
        return view('admin.lotteries.index', compact('lotteries'));
    }

    public function create()
    {
        return view('admin.lotteries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'required|string',
            'price'         => 'required|numeric|min:1',
            'prize_amount'  => 'required|numeric|min:1',
            'total_tickets' => 'required|integer|min:1',
            'draw_date'     => 'required|date|after:today',
            'image'         => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('lotteries', 'public');
        }

        LotteryPackage::create($data);
        return redirect()->route('admin.lotteries.index')->with('success', 'Lottery package created!');
    }

    public function show(LotteryPackage $lottery)
    {
        $tickets = $lottery->tickets()->with('user')->paginate(20);
        return view('admin.lotteries.show', compact('lottery', 'tickets'));
    }

    public function announceWinner(Request $request, LotteryPackage $lottery)
    {
        $request->validate([
            'winning_ticket_number' => 'required|string',
            'announcement_video'    => 'required|mimetypes:video/mp4,video/avi,video/quicktime|max:102400',
            'video_display_seconds' => 'required|integer|min:10|max:300',
        ]);

        $ticket = LotteryTicket::where('ticket_number', $request->winning_ticket_number)
            ->where('lottery_package_id', $lottery->id)
            ->first();

        if (!$ticket) {
            return back()->with('error', 'Ticket number not found in this lottery!');
        }

        $videoPath      = $request->file('announcement_video')->store('winning-videos', 'public');
        $displaySeconds = (int) $request->video_display_seconds;

        WinningDraw::create([
            'lottery_package_id'      => $lottery->id,
            'winning_ticket_number'   => $ticket->ticket_number,
            'winner_user_id'          => $ticket->user_id,
            'prize_amount'            => $lottery->prize_amount,
            'announcement_video_path' => $videoPath,
            'video_display_seconds'   => $displaySeconds,
            'video_expires_at'        => now()->addSeconds($displaySeconds),
            'video_active'            => true,
        ]);

        LotteryTicket::where('lottery_package_id', $lottery->id)
            ->where('id', '!=', $ticket->id)
            ->update(['status' => 'lost']);

        $ticket->update(['status' => 'won']);

        $ticket->user->creditWallet(
            $lottery->prize_amount,
            'prize_won',
            'Congratulations! You won lottery: ' . $lottery->name
        );

        $lottery->update(['status' => 'completed']);

        return redirect()->route('admin.lotteries.index')
            ->with('success', 'Winner announced! Prize of Rs. ' . $lottery->prize_amount . ' added to winners wallet.');
    }
}