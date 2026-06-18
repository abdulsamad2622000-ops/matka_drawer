 
@extends('layouts.admin')

@section('title', 'Support Ticket')

@section('content')

<div class="page-header">
    <div>
        <a href="{{ route('admin.support.index') }}"
           style="font-size:13px;color:var(--text2);text-decoration:none;display:flex;align-items:center;gap:4px;margin-bottom:8px">
            ← Back to Tickets
        </a>
        <h1>🎧 {{ $ticket->subject }}</h1>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
        <span class="badge {{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span>
        @if($ticket->priority === 'high')
        <span style="color:#ef4444;font-weight:700;font-size:13px">🔴 High</span>
        @elseif($ticket->priority === 'medium')
        <span style="color:#f59e0b;font-weight:700;font-size:13px">🟡 Medium</span>
        @else
        <span style="color:#22c55e;font-weight:700;font-size:13px">🟢 Low</span>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert success" style="margin-bottom:20px">{{ session('success') }}</div>
@endif

{{-- USER INFO --}}
<div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:16px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between">
    <div style="display:flex;align-items:center;gap:12px">
        <div style="width:44px;height:44px;background:var(--teal);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#fff">
            {{ substr($ticket->user->name, 0, 1) }}
        </div>
        <div>
            <div style="font-weight:700;font-size:15px">{{ $ticket->user->name }}</div>
            <div style="font-size:12px;color:var(--text2)">{{ $ticket->user->email }}</div>
            <div style="font-size:11px;color:var(--text2);margin-top:2px">
                Ticket #{{ $ticket->id }} · {{ $ticket->created_at->format('d M Y h:i A') }}
            </div>
        </div>
    </div>

    {{-- CLOSE TICKET --}}
    @if($ticket->status !== 'closed')
    <form action="{{ route('admin.support.close', $ticket) }}" method="POST"
          onsubmit="return confirm('Close this ticket?')">
        @csrf
        <button type="submit"
            style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;padding:8px 16px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600">
            🔒 Close Ticket
        </button>
    </form>
    @else
    <span style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600">
        🔒 Closed
    </span>
    @endif
</div>

{{-- MESSAGES --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <h3>💬 Conversation</h3>
    </div>
    <div style="padding:16px;display:flex;flex-direction:column;gap:12px">
        @foreach($messages as $msg)
        <div style="display:flex;{{ $msg->is_admin ? 'flex-direction:row-reverse' : 'flex-direction:row' }};gap:10px;align-items:flex-start">

            {{-- AVATAR --}}
            <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;flex-shrink:0;background:{{ $msg->is_admin ? 'var(--teal)' : '#6366f1' }}">
                {{ $msg->is_admin ? 'A' : substr($ticket->user->name, 0, 1) }}
            </div>

            {{-- MESSAGE BUBBLE --}}
            <div style="max-width:75%">
                <div style="font-size:11px;color:var(--text2);margin-bottom:4px;{{ $msg->is_admin ? 'text-align:right' : '' }}">
                    {{ $msg->is_admin ? 'Admin' : $ticket->user->name }}
                    · {{ $msg->created_at->format('d M h:i A') }}
                </div>
                <div style="background:{{ $msg->is_admin ? 'rgba(0,184,148,0.1)' : 'var(--bg3)' }};border:1px solid {{ $msg->is_admin ? 'rgba(0,184,148,0.3)' : 'var(--border)' }};border-radius:12px;padding:12px 16px;font-size:14px;line-height:1.6">
                    {{ $msg->message }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- REPLY FORM --}}
@if($ticket->status !== 'closed')
<div class="card">
    <div class="card-header">
        <h3>✍️ Reply to User</h3>
    </div>
    <div style="padding:20px">
        <form action="{{ route('admin.support.reply', $ticket) }}" method="POST">
            @csrf
            <div class="form-group">
                <textarea name="message" class="form-input" rows="4"
                          placeholder="Type your reply to the user..."
                          style="resize:vertical">{{ old('message') }}</textarea>
                @error('message')<span class="error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn-primary">
                📤 Send Reply
            </button>
        </form>
    </div>
</div>
@endif

@endsection