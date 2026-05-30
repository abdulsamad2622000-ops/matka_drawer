@extends('layouts.admin')

@section('title', $lottery->name)

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <div>
        <h1>{{ $lottery->name }}</h1>
        <span class="subtitle">Draw Date: {{ $lottery->draw_date->format('d M Y') }}</span>
    </div>
    <span class="badge {{ $lottery->status }}" style="font-size:14px;padding:8px 16px">
        {{ ucfirst($lottery->status) }}
    </span>
</div>

{{-- Lottery Stats --}}
<div class="stats-grid" style="margin-bottom:28px">
    <div class="stat-card primary">
        <div class="stat-icon">💰</div>
        <div class="stat-info">
            <span class="stat-number">Rs. {{ number_format($lottery->price, 0) }}</span>
            <span class="stat-label">Ticket Price</span>
        </div>
    </div>
    <div class="stat-card revenue">
        <div class="stat-icon">🏆</div>
        <div class="stat-info">
            <span class="stat-number">Rs. {{ number_format($lottery->prize_amount, 0) }}</span>
            <span class="stat-label">Prize Amount</span>
        </div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon">🎟️</div>
        <div class="stat-info">
            <span class="stat-number">{{ $lottery->sold_tickets }}/{{ $lottery->total_tickets }}</span>
            <span class="stat-label">Tickets Sold</span>
        </div>
    </div>
</div>

<div class="two-col">
    {{-- Tickets List --}}
    <div class="card">
        <div class="card-header"><h3>🎟️ Ticket Holders</h3></div>
        <div class="table-wrap" style="max-height:500px;overflow-y:auto">
            <table>
                <thead>
                    <tr><th>Ticket #</th><th>User</th><th>Date</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($tickets as $t)
                    <tr>
                        <td class="mono">{{ $t->ticket_number }}</td>
                        <td>{{ $t->user->name }}</td>
                        <td>{{ $t->created_at->format('d M') }}</td>
                        <td><span class="badge {{ $t->status }}">{{ ucfirst($t->status) }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--text2);padding:30px">
                            No tickets sold yet
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:12px">{{ $tickets->links() }}</div>
    </div>

    {{-- Announce Winner --}}
    @if($lottery->status === 'active')
    <div class="card" id="announce">
        <div class="card-header"><h3>🏆 Announce Winner</h3></div>
        <form action="{{ route('admin.lotteries.announce-winner', $lottery) }}"
              method="POST" enctype="multipart/form-data" style="padding:20px">
            @csrf

            <div class="form-group">
                <label>Winning Ticket Number *</label>
                <input type="text" name="winning_ticket_number" id="winnerTicket"
                       class="form-input mono" required placeholder="LT-2026-000001"
                       value="{{ old('winning_ticket_number') }}">
                <p class="helper-text">Click any ticket row on the left to auto-fill</p>
                @error('winning_ticket_number')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Announcement Video * <span class="hint">(MP4, max 100MB)</span></label>
                <div class="file-drop">
                    <input type="file" name="announcement_video" id="videoFile"
                           accept="video/mp4,video/avi,video/quicktime" required>
                    <div class="file-drop-ui">
                        <span class="file-icon">🎬</span>
                        <span class="file-text">Drop video here or click to browse</span>
                        <span class="file-name" id="fileName">No file selected</span>
                    </div>
                </div>
                @error('announcement_video')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Video Display Duration (seconds) *</label>
                <input type="number" name="video_display_seconds" class="form-input"
                       value="{{ old('video_display_seconds', 30) }}" min="10" max="300" required>
                <p class="helper-text">Video will auto-hide from user dashboards after this time</p>
                @error('video_display_seconds')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="prize-summary">
                <span>Prize to be credited:</span>
                <strong style="color:var(--gold)">Rs. {{ number_format($lottery->prize_amount, 0) }}</strong>
            </div>

            <button type="submit" class="btn-primary full-width"
                    onclick="return confirm('Announce winner? This will credit Rs.{{ number_format($lottery->prize_amount,0) }} to winner wallet and close the lottery.')">
                🏆 Announce Winner & Broadcast Video
            </button>
        </form>
    </div>
    @else
    <div class="card">
        <div class="card-header"><h3>🏆 Winner</h3></div>
        @if($lottery->winningDraw)
        <div style="padding:20px">
            <div class="detail-row">
                <span class="detail-label">Winning Ticket</span>
                <span class="mono">{{ $lottery->winningDraw->winning_ticket_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Winner</span>
                <span>{{ $lottery->winningDraw->winner->name ?? '—' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Prize</span>
                <span style="color:var(--gold);font-weight:700">
                    Rs. {{ number_format($lottery->winningDraw->prize_amount, 0) }}
                </span>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>

<script>
document.querySelectorAll('tbody tr').forEach(row => {
    row.style.cursor = 'pointer';
    row.addEventListener('click', () => {
        const ticketNum = row.querySelector('.mono');
        if (ticketNum) {
            const input = document.getElementById('winnerTicket');
            if (input) input.value = ticketNum.textContent.trim();
        }
    });
});

const videoFile = document.getElementById('videoFile');
if (videoFile) {
    videoFile.addEventListener('change', function() {
        document.getElementById('fileName').textContent =
            this.files.length ? this.files[0].name : 'No file selected';
    });
}
</script>
@endsection