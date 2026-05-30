@extends('layouts.admin')

@section('title', 'Payments')

@section('content')
<div class="page-header">
    <h1>💳 Payment Requests</h1>
    <span class="subtitle">Review and approve user payment requests</span>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Reference</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>
                        <div>{{ $p->user->name }}</div>
                        <div style="font-size:11px;color:var(--text2)">{{ $p->user->email }}</div>
                    </td>
                    <td style="color:var(--gold);font-weight:700">Rs. {{ number_format($p->amount, 0) }}</td>
                    <td class="mono">{{ $p->transaction_reference ?? '—' }}</td>
                    <td><span class="badge {{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    <td>{{ $p->created_at->format('d M Y') }}</td>
                    <td><a href="{{ route('admin.payments.show', $p) }}" class="btn-xs">Review</a></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:var(--text2);padding:40px">
                        No payment requests yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px">
        {{ $payments->links() }}
    </div>
</div>
@endsection