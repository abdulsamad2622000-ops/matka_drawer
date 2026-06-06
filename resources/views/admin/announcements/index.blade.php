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
                    <th>Winning Number</th>
                    <th>Next Draw</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $a)
                <tr>
                    <td>{{ $a->id }}</td>
                    <td>
                        <div style="font-weight:600">{{ $a->title }}</div>
                        @if($a->description)
                        <div style="font-size:11px;color:var(--text2)">{{ $a->description }}</div>
                        @endif
                        @if($a->scheduled_at)
                        <div style="font-size:11px;color:var(--orange)">
                            📅 Scheduled: {{ $a->scheduled_at->format('d M Y H:i') }}
                        </div>
                        @endif
                    </td>
                    <td>
                        @if($a->winning_number)
                            <span class="mono" style="color:var(--teal);font-weight:700">{{ $a->winning_number }}</span>
                        @else
                            <span style="color:var(--text2)">—</span>
                        @endif
                    </td>
                    <td>
                        @if($a->next_draw_at)
                            <span style="font-size:12px;color:var(--teal)">
                                {{ $a->next_draw_at->format('d M Y') }}<br>
                                {{ $a->next_draw_at->format('h:i A') }}
                            </span>
                        @else
                            <span style="color:var(--text2)">—</span>
                        @endif
                    </td>
                    <td>{{ $a->video_display_seconds }}s</td>
                    <td>
                        @if($a->isScheduled())
                            <span class="badge warning">⏰ Scheduled</span>
                        @elseif($a->isVideoVisible())
                            <span class="badge active">🔴 Live</span>
                        @elseif($a->is_active)
                            <span class="badge warning">Active</span>
                        @else
                            <span class="badge rejected">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $a->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:6px">

                            {{-- Set Next Draw --}}
                            @if($a->is_active)
                            <div style="display:flex;gap:6px;align-items:center">
                                <form action="{{ route('admin.announcements.set-next-draw', $a) }}"
                                      method="POST" style="display:flex;gap:4px;align-items:center">
                                    @csrf
                                    <input type="datetime-local" name="next_draw_at"
                                           class="form-input" style="padding:4px 8px;font-size:11px;width:170px"
                                           value="{{ $a->next_draw_at ? $a->next_draw_at->format('Y-m-d\TH:i') : '' }}">
                                    <button type="submit" class="btn-xs"
                                        style="background:rgba(0,184,148,.15);color:var(--teal);white-space:nowrap">
                                        📅 Set
                                    </button>
                                </form>
                            </div>
                            @endif

                            {{-- Remove Winning Number --}}
                            @if($a->winning_number && $a->is_active)
                            <form action="{{ route('admin.announcements.remove-winning-number', $a) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-xs"
                                    style="background:rgba(249,115,22,.15);color:var(--orange)"
                                    onclick="return confirm('Remove winning number from dashboard?')">
                                    🗑 Remove Number
                                </button>
                            </form>
                            @endif

                            {{-- Deactivate --}}
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
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:var(--text2);padding:40px">
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