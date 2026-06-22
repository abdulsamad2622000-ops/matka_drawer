@extends('layouts.admin')

@section('title', 'Withdrawal Requests')

@section('content')

<div class="page-header">
    <h1>💸 Withdrawal Requests</h1>
    <span class="subtitle">Manage user withdrawal requests</span>
</div>

@if(session('success'))
<div class="alert success" style="margin-bottom:16px">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert error" style="margin-bottom:16px">{{ session('error') }}</div>
@endif

{{-- PENDING REQUESTS --}}
<div class="card" style="margin-bottom:24px">
    <div class="card-header">
        <h3>⏳ Pending Requests</h3>
        <span style="background:rgba(255,193,7,0.15);color:#f59e0b;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700">
            {{ $pending->count() }} pending
        </span>
    </div>

    @if($pending->isEmpty())
    <div class="empty-state">
        <span>✅</span>
        <p>No pending withdrawal requests!</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Account Details</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pending as $w)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:32px;height:32px;background:var(--teal);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff">
                                {{ substr($w->user->name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight:600">{{ $w->user->name }}</div>
                                <div style="font-size:11px;color:var(--text2)">{{ $w->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-size:1.2rem;font-weight:700;color:var(--teal)">
                            Rs. {{ number_format($w->amount, 0) }}
                        </span>
                        <div style="font-size:11px;color:var(--text2)">After 2% fee</div>
                    </td>
                    <td>
                        <span style="background:rgba(0,184,148,0.1);border:1px solid rgba(0,184,148,0.3);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">
                            {{ strtoupper($w->method) }}
                        </span>
                    </td>
                    <td>
                        @if($w->method === 'bank')
                        <div style="font-size:12px">
                            <div><strong>Bank:</strong> {{ $w->bank_name }}</div>
                            <div><strong>Title:</strong> {{ $w->account_title }}</div>
                            <div><strong>Account:</strong> <span class="mono">{{ $w->account_number }}</span></div>
                        </div>
                        @else
                        <div style="font-size:12px">
                            <div><strong>Name:</strong> {{ $w->account_holder }}</div>
                            <div><strong>Mobile:</strong> <span class="mono">{{ $w->mobile_number }}</span></div>
                        </div>
                        @endif
                    </td>
                    <td style="color:var(--text2);font-size:12px">
                        {{ $w->created_at->format('d M Y') }}<br>
                        {{ $w->created_at->format('h:i A') }}
                    </td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:6px">
                            <form action="{{ route('admin.withdrawals.approve', $w) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-xs"
                                        style="background:var(--teal);color:#fff;width:100%;padding:6px 12px"
                                        onclick="return confirm('Approve Rs. {{ number_format($w->amount, 0) }} withdrawal for {{ $w->user->name }}?')">
                                    ✅ Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.withdrawals.reject', $w) }}" method="POST"
                                  id="rejectForm{{ $w->id }}">
                                @csrf
                                <input type="hidden" name="admin_note" id="note{{ $w->id }}">
                                <button type="button" class="btn-xs"
                                        style="background:rgba(239,68,68,0.15);color:#ef4444;border:1px solid rgba(239,68,68,0.3);width:100%;padding:6px 12px"
                                        onclick="rejectWithNote({{ $w->id }})">
                                    ❌ Reject
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- PROCESSED REQUESTS --}}
<div class="card">
    <div class="card-header">
        <h3>📋 Recent Processed</h3>
    </div>

    @if($processed->isEmpty())
    <div class="empty-state">
        <span>📭</span>
        <p>No processed requests yet.</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Note</th>
                    <th>Processed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($processed as $w)
                <tr>
                    <td>{{ $w->user->name }}</td>
                    <td style="font-weight:700">Rs. {{ number_format($w->amount, 0) }}</td>
                    <td>{{ strtoupper($w->method) }}</td>
                    <td><span class="badge {{ $w->status }}">{{ ucfirst($w->status) }}</span></td>
                    <td style="font-size:12px;color:var(--text2)">{{ $w->admin_note ?? '-' }}</td>
                    <td style="font-size:12px;color:var(--text2)">
                        {{ $w->processed_at ? $w->processed_at->format('d M Y') : '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<script>
function rejectWithNote(id) {
    const note = prompt('Rejection reason (optional):');
    if (note !== null) {
        document.getElementById('note' + id).value = note;
        document.getElementById('rejectForm' + id).submit();
    }
}
</script>

@endsection