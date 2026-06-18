<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $tickets     = SupportTicket::with(['user', 'latestMessage'])->latest()->paginate(20);
        $openCount   = SupportTicket::where('status', 'open')->count();
        $unreadCount = SupportMessage::where('is_admin', false)->where('is_read', false)->count();

        return view('admin.support.index', compact('tickets', 'openCount', 'unreadCount'));
    }

    public function show(SupportTicket $ticket)
    {
        // User messages read mark karo
        $ticket->messages()->where('is_admin', false)->update(['is_read' => true]);

        $messages = $ticket->messages()->with('user')->oldest()->get();
        return view('admin.support.show', compact('ticket', 'messages'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string|min:1|max:2000',
        ]);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $request->message,
            'is_admin'  => true,
            'is_read'   => false,
        ]);

        $ticket->update(['status' => 'replied']);

        return back()->with('success', '✅ Reply sent!');
    }

    public function close(SupportTicket $ticket)
    {
        $ticket->update(['status' => 'closed']);
        return back()->with('success', '🔒 Ticket closed.');
    }

    public function destroy(SupportTicket $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin.support.index')->with('success', '🗑️ Ticket deleted.');
    }
}