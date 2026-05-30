@extends('layouts.admin')

@section('title', 'Announcements')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1>📢 Announcements</h1>
        <span class="subtitle">Broadcast video announcements to all users</span>
    </div>
    <a href="{{ route('admin.announcements.create') }}" class="btn-primary">+ New Announcement</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $a)
                <tr>
                    <td>{{ $a->id }}</td>
                    <td>
                        <div>{{ $a->title }}</div>
                        @if($a->description)
                        <div style="font-size:11px;color:var(--text2)">{{ $a->description }}</div>
                        @endif
                    </td>
                    <td>{{ $a->video_display_seconds }}s</td>
                    <td>
                        @if($a->isVideoVisible())
                            <span class="badge active">🔴 Live</span>
                        @elseif($a->is_active)
                            <span class="badge warning">Expired</span>
                        @else
                            <span class="badge rejected">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $a->creator->name }}</td>
                    <td>{{ $a->created_at->format('d M Y H:i') }}</td>
                    <td>
                        @if($a->is_active)
                        <form action="{{ route('admin.announcements.destroy', $a) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-xs"
                                style="background:rgba(239,68,68,.15);color:var(--red)"
                                onclick="return confirm('Deactivate this announcement?')">
                                Deactivate
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:var(--text2);padding:40px">
                        No announcements yet. <a href="{{ route('admin.announcements.create') }}">Create one!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px">{{ $announcements->links() }}</div>
</div>
@endsection