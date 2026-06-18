<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = Auth::user()->supportTickets()->with('latestMessage')->latest()->get();
        return view('user.support.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'  => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'message'  => 'required|string|min:10|max:2000',
        ]);

        $ticket = SupportTicket::create([
            'user_id'  => Auth::id(),
            'subject'  => $request->subject,
            'priority' => $request->priority,
            'status'   => 'open',
        ]);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(),
            'message'   => $request->message,
            'is_admin'  => false,
            'is_read'   => false,
        ]);

        return redirect()->route('user.support.show', $ticket)
            ->with('success', '✅ Ticket submitted! Admin will reply soon.');
    }

    public function show(SupportTicket $ticket)
    {
        // Sirf apna ticket dekh sake
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        // Admin ke messages read mark karo
        $ticket->messages()->where('is_admin', true)->update(['is_read' => true]);

        $messages = $ticket->messages()->with('user')->oldest()->get();
        return view('user.support.show', compact('ticket', 'messages'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        if ($ticket->status === 'closed') {
            return back()->withErrors(['message' => 'This ticket is closed.']);
        }

        $request->validate([
            'message' => 'required|string|min:1|max:2000',
        ]);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(),
            'message'   => $request->message,
            'is_admin'  => false,
            'is_read'   => false,
        ]);

        $ticket->update(['status' => 'open']);

        return back()->with('success', '✅ Reply sent!');
    }
}