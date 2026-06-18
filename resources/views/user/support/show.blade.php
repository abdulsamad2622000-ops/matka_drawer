@extends('layouts.user')

@section('title', 'Support Ticket')

@section('content')

<div class="page-header">
    <div>
        <a href="{{ route('user.support.index') }}"
           style="font-size:13px;color:var(--text2);text-decoration:none;display:flex;align-items:center;gap:4px;margin-bottom:8px">
            ← Back to Support
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

{{-- MESSAGES --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <h3>💬 Conversation</h3>
        <span style="font-size:12px;color:var(--text2)">Ticket #{{ $ticket->id }}</span>
    </div>
    <div style="padding:16px;display:flex;flex-direction:column;gap:12px">
        @foreach($messages as $msg)
        <div style="display:flex;{{ $msg->is_admin ? 'flex-direction:row' : 'flex-direction:row-reverse' }};gap:10px;align-items:flex-start">

            {{-- AVATAR --}}
            <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;flex-shrink:0;background:{{ $msg->is_admin ? 'var(--teal)' : '#6366f1' }}">
                {{ $msg->is_admin ? 'A' : substr(auth()->user()->name, 0, 1) }}
            </div>

            {{-- MESSAGE BUBBLE --}}
            <div style="max-width:75%">
                <div style="font-size:11px;color:var(--text2);margin-bottom:4px;{{ $msg->is_admin ? '' : 'text-align:right' }}">
                    {{ $msg->is_admin ? 'Admin' : auth()->user()->name }}
                    · {{ $msg->created_at->format('d M h:i A') }}
                </div>
                <div style="background:{{ $msg->is_admin ? 'var(--bg3)' : 'rgba(99,102,241,0.1)' }};border:1px solid {{ $msg->is_admin ? 'var(--border)' : 'rgba(99,102,241,0.3)' }};border-radius:12px;padding:12px 16px;font-size:14px;line-height:1.6">
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
        <h3>✍️ Reply</h3>
    </div>
    <div style="padding:20px">
        <form action="{{ route('user.support.reply', $ticket) }}" method="POST">
            @csrf
            <div class="form-group">
                <textarea name="message" class="form-input" rows="4"
                          placeholder="Type your reply..."
                          style="resize:vertical">{{ old('message') }}</textarea>
                @error('message')<span class="error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn-primary">
                📤 Send Reply
            </button>
        </form>
    </div>
</div>
@else
<div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.3);border-radius:10px;padding:14px 18px;text-align:center;color:#ef4444;font-weight:600">
    🔒 This ticket is closed. <a href="{{ route('user.support.index') }}" style="color:var(--teal)">Open a new ticket</a> if you need help.
</div>
@endif

@endsection