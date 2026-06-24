@extends('layouts.admin')

@section('title', 'Support Chat')

@section('content')

<div class="page-header">
    <div>
        <h1>💬 Chat — {{ $ticket->user->name }}</h1>
        <span class="subtitle">{{ $ticket->user->email }}</span>
    </div>
    <div style="display:flex;gap:8px">
        @if($ticket->status !== 'closed')
        <form action="{{ route('admin.support.close', $ticket) }}" method="POST">
            @csrf
            <button type="submit" class="btn-sm" onclick="return confirm('Close this chat?')">🔒 Close</button>
        </form>
        @endif
        <a href="{{ route('admin.support.index') }}" class="btn-sm">← Back</a>
    </div>
</div>

<div style="max-width:700px">
    <div class="card" style="margin-bottom:16px">
        <div style="padding:16px;display:flex;flex-direction:column;gap:12px;max-height:500px;overflow-y:auto" id="chatBox">
            @foreach($messages as $msg)
            <div style="display:flex;{{ $msg->is_admin ? 'justify-content:flex-end' : 'justify-content:flex-start' }}">
                <div style="max-width:75%;background:{{ $msg->is_admin ? 'var(--teal)' : 'var(--bg3)' }};color:{{ $msg->is_admin ? '#fff' : 'var(--text)' }};padding:10px 14px;border-radius:{{ $msg->is_admin ? '12px 4px 12px 12px' : '4px 12px 12px 12px' }}">
                    @if(!$msg->is_admin)
                    <div style="font-size:11px;font-weight:700;color:var(--teal);margin-bottom:4px">{{ $ticket->user->name }}</div>
                    @endif
                    <div style="font-size:14px">{{ $msg->message }}</div>
                    <div style="font-size:10px;opacity:0.7;margin-top:4px;text-align:right">{{ $msg->created_at->format('d M h:i A') }}</div>
                </div>
            </div>
            @endforeach
        </div>

        @if($ticket->status !== 'closed')
        <div style="padding:16px;border-top:1px solid var(--border)">
            <form action="{{ route('admin.support.reply', $ticket) }}" method="POST" style="display:flex;gap:8px">
                @csrf
                <input type="text" name="message" class="form-input"
                       placeholder="Type reply..." required>
                <button type="submit" class="btn-primary" style="white-space:nowrap;padding:10px 16px">
                    Send 📤
                </button>
            </form>
        </div>
        @else
        <div style="padding:16px;text-align:center;color:var(--text2);font-size:13px">
            🔒 Chat closed.
        </div>
        @endif
    </div>
</div>

<script>
const chatBox = document.getElementById('chatBox');
if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>

@endsection