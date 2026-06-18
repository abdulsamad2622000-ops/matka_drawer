@extends('layouts.user')

@section('title', 'Support')

@section('content')

<div class="page-header">
    <h1>🎧 Support</h1>
    <span class="subtitle">Contact us — we'll reply as soon as possible</span>
</div>

@if(session('success'))
<div class="alert success" style="margin-bottom:20px">{{ session('success') }}</div>
@endif

<div class="two-col" style="align-items:start">

    {{-- NEW TICKET FORM --}}
    <div class="card">
        <div class="card-header">
            <h3>📩 New Message</h3>
        </div>
        <div style="padding:20px">
            <form action="{{ route('user.support.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Subject *</label>
                    <input type="text" name="subject" class="form-input"
                           placeholder="e.g. Payment issue, Withdrawal problem..."
                           value="{{ old('subject') }}">
                    @error('subject')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Priority *</label>
                    <select name="priority" class="form-input">
                        <option value="low"    {{ old('priority') === 'low'    ? 'selected' : '' }}>🟢 Low</option>
                        <option value="medium" {{ old('priority','medium') === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                        <option value="high"   {{ old('priority') === 'high'   ? 'selected' : '' }}>🔴 High</option>
                    </select>
                    @error('priority')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" class="form-input" rows="5"
                              placeholder="Describe your issue in detail..."
                              style="resize:vertical">{{ old('message') }}</textarea>
                    @error('message')<span class="error">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="btn-primary full-width">
                    📤 Send Message
                </button>
            </form>
        </div>
    </div>

    {{-- MY TICKETS --}}
    <div class="card">
        <div class="card-header">
            <h3>📋 My Tickets</h3>
        </div>
        @if($tickets->isEmpty())
        <div class="empty-state">
            <span>🎧</span>
            <p>No support tickets yet.</p>
        </div>
        @else
        <div style="padding:12px;display:flex;flex-direction:column;gap:10px">
            @foreach($tickets as $ticket)
            <a href="{{ route('user.support.show', $ticket) }}"
               style="display:block;background:var(--bg3);border:1px solid var(--border);border-radius:10px;padding:14px;text-decoration:none;color:var(--text);transition:border-color .2s"
               onmouseover="this.style.borderColor='var(--teal)'" onmouseout="this.style.borderColor='var(--border)'">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                    <span style="font-weight:700;font-size:14px">{{ $ticket->subject }}</span>
                    <span class="badge {{ $ticket->status }}" style="font-size:11px">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:12px;color:var(--text2)">
                        {{ $ticket->created_at->format('d M Y h:i A') }}
                    </span>
                    @if($ticket->status === 'replied')
                    <span style="font-size:11px;background:rgba(0,184,148,0.15);color:var(--teal);padding:2px 8px;border-radius:10px;font-weight:700">
                        💬 New Reply
                    </span>
                    @endif
                </div>
                <div style="margin-top:6px;font-size:11px">
                    @if($ticket->priority === 'high')
                    <span style="color:#ef4444;font-weight:700">🔴 High Priority</span>
                    @elseif($ticket->priority === 'medium')
                    <span style="color:#f59e0b;font-weight:700">🟡 Medium Priority</span>
                    @else
                    <span style="color:#22c55e;font-weight:700">🟢 Low Priority</span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>

</div>

@endsection