@extends('layouts.user')

@section('title', 'Support')

@section('content')

<div class="page-header">
    <h1>🎧 Support Chat</h1>
    <span class="subtitle">Send us a message — we'll reply soon</span>
</div>

@if(session('success'))
<div class="alert success" style="margin-bottom:16px">{{ session('success') }}</div>
@endif

<div style="max-width:700px;margin:0 auto">

    {{-- CHAT MESSAGES --}}
    @if($tickets->isNotEmpty())
    @php $ticket = $tickets->first(); @endphp
    <div class="card" style="margin-bottom:16px">
        <div class="card-header">
            <h3>💬 Chat</h3>
            <span class="badge {{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span>
        </div>
        <div style="padding:16px;display:flex;flex-direction:column;gap:12px;max-height:400px;overflow-y:auto" id="chatBox">
            @foreach($ticket->messages()->with('user')->oldest()->get() as $msg)
            <div style="display:flex;{{ $msg->is_admin ? 'justify-content:flex-start' : 'justify-content:flex-end' }}">
                <div style="max-width:75%;background:{{ $msg->is_admin ? 'var(--bg3)' : 'var(--teal)' }};color:{{ $msg->is_admin ? 'var(--text)' : '#fff' }};padding:10px 14px;border-radius:{{ $msg->is_admin ? '4px 12px 12px 12px' : '12px 4px 12px 12px' }}">
                    @if($msg->is_admin)
                    <div style="font-size:11px;font-weight:700;color:var(--teal);margin-bottom:4px">Support Team</div>
                    @endif
                    <div style="font-size:14px">{{ $msg->message }}</div>
                    <div style="font-size:10px;opacity:0.7;margin-top:4px;text-align:right">{{ $msg->created_at->format('d M h:i A') }}</div>
                </div>
            </div>
            @endforeach
        </div>

        @if($ticket->status !== 'closed')
        <div style="padding:16px;border-top:1px solid var(--border)">
            <form action="{{ route('user.support.reply', $ticket) }}" method="POST" style="display:flex;gap:8px">
                @csrf
                <input type="text" name="message" class="form-input"
                       placeholder="Type your message..." required>
                <button type="submit" class="btn-primary" style="white-space:nowrap;padding:10px 16px">
                    Send 📤
                </button>
            </form>
        </div>
        @else
        <div style="padding:16px;text-align:center;color:var(--text2);font-size:13px">
            🔒 This chat is closed.
        </div>
        @endif
    </div>
    @endif

    {{-- NEW MESSAGE FORM --}}
    @if($tickets->isEmpty())
    <div class="card">
        <div class="card-header"><h3>📩 Send Message</h3></div>
        <div style="padding:20px">
            <form action="{{ route('user.support.store') }}" method="POST">
                @csrf
                <input type="hidden" name="subject" value="Support Request">
                <input type="hidden" name="priority" value="medium">
                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" class="form-input" rows="5"
                              placeholder="How can we help you?"
                              style="resize:vertical">{{ old('message') }}</textarea>
                    @error('message')<span class="error">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="btn-primary full-width">📤 Send Message</button>
            </form>
        </div>
    </div>
    @endif

</div>

<script>
const chatBox = document.getElementById('chatBox');
if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>

@endsection