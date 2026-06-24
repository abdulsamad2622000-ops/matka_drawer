@extends('layouts.admin')

@section('title', 'Support')

@section('content')

<div class="page-header">
    <h1>🎧 Support Chats</h1>
    <span class="subtitle">Manage user support messages</span>
</div>

<div class="card">
    <div class="card-header">
        <h3>All Chats</h3>
        <span style="font-size:12px;color:var(--text2)">{{ $tickets->total() }} total</span>
    </div>
    @if($tickets->isEmpty())
    <div class="empty-state"><span>🎧</span><p>No support messages yet.</p></div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Last Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td style="color:var(--text2)">#{{ $ticket->id }}</td>
                    <td>
                        <div style="font-weight:600">{{ $ticket->user->name }}</div>
                        <div style="font-size:11px;color:var(--text2)">{{ $ticket->user->email }}</div>
                    </td>
                    <td style="font-size:13px;color:var(--text2)">
                        {{ $ticket->latestMessage ? Str::limit($ticket->latestMessage->message, 50) : '-' }}
                    </td>
                    <td><span class="badge {{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span></td>
                    <td style="font-size:12px;color:var(--text2)">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td style="display:flex;gap:6px">
                        <a href="{{ route('admin.support.show', $ticket) }}" class="btn-xs">💬 Reply</a>
                        <form action="{{ route('admin.support.destroy', $ticket) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-xs"
                                style="background:rgba(239,68,68,0.1);color:#ef4444"
                                onclick="return confirm('Delete?')">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding:16px">{{ $tickets->links() }}</div>
    @endif
</div>

@endsection