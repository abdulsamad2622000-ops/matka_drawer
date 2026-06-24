@extends('layouts.admin')

@section('title', 'Password Reset Requests')

@section('content')

<div class="page-header">
    <h1>🔑 Password Reset Requests</h1>
    <span class="subtitle">Users who forgot their password</span>
</div>

@if(session('success'))
<div class="alert success" style="margin-bottom:20px">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3>🔑 Reset Requests</h3>
        @if($pendingCount > 0)
        <span style="background:rgba(255,59,59,0.15);color:#ff3b3b;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">
            {{ $pendingCount }} pending
        </span>
        @endif
    </div>

    @if($resets->isEmpty())
    <div class="empty-state">
        <span>🔑</span>
        <p>No password reset requests yet.</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
    <th>Full Name</th>
    <th>Login Number</th>
    <th>Email Address</th>
    <th>Reason</th>
    <th>Generated Password</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>
            </thead>
            <tbody>
                @foreach($resets as $reset)
                <tr>
                    <td>
    <div style="display:flex;align-items:center;gap:8px">
        <div style="width:32px;height:32px;background:var(--teal);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:13px">
            {{ substr($reset->user_name ?? '?', 0, 1) }}
        </div>
        <span style="font-weight:600">{{ $reset->user_name ?? '—' }}</span>
    </div>
</td>
<td style="font-family:monospace;font-size:13px">{{ $reset->user->email ?? '—' }}</td>
<td>
    @if($reset->email)
    <div style="display:flex;align-items:center;gap:8px">
        <span style="font-size:13px;color:var(--teal)">{{ $reset->email }}</span>
        <button onclick="copyEmail('{{ $reset->email }}')"
            style="background:rgba(0,184,148,0.1);color:var(--teal);border:1px solid rgba(0,184,148,0.3);padding:3px 8px;border-radius:6px;cursor:pointer;font-size:11px;font-weight:600">
            📋
        </button>
    </div>
    @else
    <span style="color:var(--text2)">—</span>
    @endif
</td>                    <td>
                        @if($reset->generated_password)
                        <div style="display:flex;align-items:center;gap:8px">
                            <code style="background:var(--bg3);border:1px solid var(--border);padding:4px 10px;border-radius:6px;font-size:13px;font-weight:700;color:var(--teal)">
                                {{ $reset->generated_password }}
                            </code>
                            <button onclick="copyPassword('{{ $reset->generated_password }}', '{{ $reset->user_name }}')"
                                style="background:var(--teal);color:#fff;border:none;padding:4px 10px;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600">
                                📋 Copy
                            </button>
                        </div>
                        @else
                        <span style="color:var(--text2);font-size:12px">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $reset->status }}" style="font-size:11px">
                            {{ ucfirst($reset->status) }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--text2)">{{ $reset->created_at->diffForHumans() }}</td>
                    <td>
                        @if($reset->status === 'pending')
                        <form action="{{ route('admin.settings.password-reset.resolve', $reset) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-xs"
                                style="background:rgba(0,184,148,0.1);color:var(--teal);border:1px solid rgba(0,184,148,0.3)"
                                onclick="return confirm('Mark as resolved?')">
                                ✅ Resolved
                            </button>
                        </form>
                        @else
                        <span style="font-size:12px;color:var(--text2)">✅ Done</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding:16px">{{ $resets->links() }}</div>
    @endif
</div>

<script>
function copyPassword(password, name) {
    const message = `Dear ${name},\nYour new Matka Champion password is:\n🔐 ${password}\nPlease login and change your password immediately.\nLogin here: https://matkachampion.com/login\n- Matka Champion Team`;

    navigator.clipboard.writeText(message).then(() => {
        alert('✅ Message copied! Paste it in WhatsApp/SMS to send to user.');
    }).catch(() => {
        const el = document.createElement('textarea');
        el.value = message;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        alert('✅ Message copied! Paste it in WhatsApp/SMS to send to user.');
    });
}

function copyEmail(email) {
    navigator.clipboard.writeText(email).then(() => {
        alert('✅ Email copied!');
    }).catch(() => {
        const el = document.createElement('textarea');
        el.value = email;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        alert('✅ Email copied!');
    });
}
</script>

@endsection