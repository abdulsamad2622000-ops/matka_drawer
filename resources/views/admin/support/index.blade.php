 
@extends('layouts.admin')

@section('title', 'Support Tickets')

@section('content')

<div class="page-header">
    <h1>🎧 Support Tickets</h1>
    <span class="subtitle">Manage user support requests</span>
</div>

@if(session('success'))
<div class="alert success" style="margin-bottom:20px">{{ session('success') }}</div>
@endif

{{-- STATS --}}
<div class="stats-grid mini" style="margin-bottom:24px">
    <div class="stat-card warning">
        <div class="stat-icon">📬</div>
        <div class="stat-info">
            <span class="stat-number">{{ $openCount }}</span>
            <span class="stat-label">Open Tickets</span>
        </div>
    </div>
    <div class="stat-card primary">
        <div class="stat-icon">💬</div>
        <div class="stat-info">
            <span class="stat-number">{{ $unreadCount }}</span>
            <span class="stat-label">Unread Messages</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🎫</div>
        <div class="stat-info">
            <span class="stat-number">{{ $tickets->total() }}</span>
            <span class="stat-label">Total Tickets</span>
        </div>
    </div>
</div>

{{-- TICKETS TABLE --}}
<div class="card">
    <div class="card-header">
        <h3>📋 All Tickets</h3>
        @if($unreadCount > 0)
        <span style="background:rgba(239,68,68,0.15);color:#ef4444;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700">
            {{ $unreadCount }} unread
        </span>
        @endif
    </div>

    @if($tickets->isEmpty())
    <div class="empty-state">
        <span>🎧</span>
        <p>No support tickets yet!</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Subject</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr style="{{ $ticket->unreadCount() > 0 ? 'background:rgba(0,184,148,0.04)' : '' }}">
                    <td style="color:var(--text2);font-size:12px">#{{ $ticket->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:30px;height:30px;background:var(--teal);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff">
                                {{ substr($ticket->user->name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px">{{ $ticket->user->name }}</div>
                                <div style="font-size:11px;color:var(--text2)">{{ $ticket->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-weight:600">{{ $ticket->subject }}</span>
                        @if($ticket->unreadCount() > 0)
                        <span style="margin-left:6px;background:#ef4444;color:#fff;font-size:9px;font-weight:700;padding:1px 6px;border-radius:10px">
                            {{ $ticket->unreadCount() }} new
                        </span>
                        @endif
                    </td>
                    <td>
                        @if($ticket->priority === 'high')
                        <span style="color:#ef4444;font-weight:700;font-size:12px">🔴 High</span>
                        @elseif($ticket->priority === 'medium')
                        <span style="color:#f59e0b;font-weight:700;font-size:12px">🟡 Medium</span>
                        @else
                        <span style="color:#22c55e;font-weight:700;font-size:12px">🟢 Low</span>
                        @endif
                    </td>
                    <td><span class="badge {{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span></td>
                    <td style="font-size:12px;color:var(--text2)">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('admin.support.show', $ticket) }}" class="btn-xs">
                                💬 Reply
                            </a>
                            <form action="{{ route('admin.support.destroy', $ticket) }}" method="POST"
                                  onsubmit="return confirm('Delete this ticket?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-xs"
                                    style="background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid rgba(239,68,68,0.3)">
                                    🗑️
                                </button>
                            </form>
                        </div>
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