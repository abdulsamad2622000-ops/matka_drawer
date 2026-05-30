@extends('layouts.admin')

@section('title', 'Lotteries')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1>🎰 Lotteries</h1>
        <span class="subtitle">Manage all lottery packages</span>
    </div>
    <a href="{{ route('admin.lotteries.create') }}" class="btn-primary">+ Create New</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Prize</th>
                    <th>Tickets</th>
                    <th>Draw Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lotteries as $l)
                <tr>
                    <td>{{ $l->name }}</td>
                    <td>Rs. {{ number_format($l->price, 0) }}</td>
                    <td style="color:var(--gold);font-weight:700">
                        Rs. {{ number_format($l->prize_amount, 0) }}
                    </td>
                    <td>{{ $l->sold_tickets }}/{{ $l->total_tickets }}</td>
                    <td>{{ $l->draw_date->format('d M Y') }}</td>
                    <td><span class="badge {{ $l->status }}">{{ ucfirst($l->status) }}</span></td>
                    <td style="display:flex;gap:8px">
                        <a href="{{ route('admin.lotteries.show', $l) }}" class="btn-xs">View</a>
                        @if($l->status === 'active')
                        <a href="{{ route('admin.lotteries.show', $l) }}#announce" class="btn-xs"
                           style="background:rgba(245,200,66,.2)">🏆 Winner</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:var(--text2);padding:40px">
                        No lotteries yet. <a href="{{ route('admin.lotteries.create') }}">Create one!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px">{{ $lotteries->links() }}</div>
</div>
@endsection