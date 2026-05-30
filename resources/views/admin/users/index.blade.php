@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="page-header">
    <h1>👥 Users</h1>
    <span class="subtitle">Manage registered users</span>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Wallet</th>
                    <th>Tickets</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td style="color:var(--gold);font-weight:700">
                        Rs. {{ number_format($user->wallet_balance, 0) }}
                    </td>
                    <td>{{ $user->lottery_tickets_count }}</td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'active' : 'rejected' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td style="display:flex;gap:8px">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn-xs">View</a>
                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-xs"
                                style="{{ $user->is_active ? 'background:rgba(239,68,68,.15);color:var(--red)' : 'background:rgba(34,197,94,.15);color:var(--green)' }}"
                                onclick="return confirm('{{ $user->is_active ? 'Deactivate' : 'Activate' }} this user?')">
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:var(--text2);padding:40px">
                        No users registered yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px">{{ $users->links() }}</div>
</div>
@endsection