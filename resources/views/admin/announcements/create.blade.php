@extends('layouts.admin')

@section('title', 'New Announcement')

@section('content')
<div class="page-header">
    <h1>📢 New Announcement</h1>
    <span class="subtitle">Broadcast a video to all users</span>
</div>

<div class="card" style="max-width:700px">
    <div class="card-header"><h3>Announcement Details</h3></div>
    <form action="{{ route('admin.announcements.store') }}" method="POST"
          enctype="multipart/form-data" style="padding:20px">
        @csrf

        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" class="form-input"
                   placeholder="e.g. Winner Announcement - Gold Rush"
                   value="{{ old('title') }}" required>
            @error('title')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label>Description <span class="hint">(optional)</span></label>
            <textarea name="description" class="form-input"
                      placeholder="Additional details...">{{ old('description') }}</textarea>
            @error('description')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label>Video * <span class="hint">(MP4, max 100MB)</span></label>
            <div class="file-drop">
                <input type="file" name="video" id="videoFile"
                       accept="video/mp4,video/avi,video/quicktime" required>
                <div class="file-drop-ui">
                    <span class="file-icon">🎬</span>
                    <span class="file-text">Drop video here or click to browse</span>
                    <span class="file-name" id="fileName">No file selected</span>
                </div>
            </div>
            @error('video')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label>Video Display Duration (seconds) *</label>
            <input type="number" name="video_display_seconds" class="form-input"
                   value="{{ old('video_display_seconds', 30) }}"
                   min="10" max="300" required>
            <p class="helper-text">Video will auto-hide from user dashboards after this duration</p>
            @error('video_display_seconds')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label>Winning Number <span class="hint">*</span></label>
            <input type="text" name="winning_number" class="form-input mono"
                   placeholder="e.g. 12, 15, 16"
                   value="{{ old('winning_number') }}">
            <p class="helper-text">Shown permanently on user dashboard after video ends until removed</p>
            @error('winning_number')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="two-col" style="margin-top:0">
            <div class="form-group">
                <label>Schedule Date & Time <span class="hint">(optional)</span></label>
                <input type="datetime-local" name="scheduled_at" class="form-input"
                       value="{{ old('scheduled_at') }}">
                <p class="helper-text">Leave empty to broadcast immediately</p>
                @error('scheduled_at')<span class="error">{{ $message }}</span>@enderror
            </div>

          
        </div>

        <div class="alert info" style="margin-bottom:16px">
            ℹ️ This video will be shown to <strong>ALL active users</strong> on their dashboard
            @if(old('scheduled_at'))
                at the scheduled time.
            @else
                immediately after publishing.
            @endif
        </div>

        <button type="submit" class="btn-primary full-width"
                onclick="return confirm('Broadcast this announcement to all users?')">
            📢 Broadcast to All Users
        </button>
    </form>
</div>

<script>
document.getElementById('videoFile').addEventListener('change', function() {
    document.getElementById('fileName').textContent =
        this.files.length ? this.files[0].name : 'No file selected';
});
</script>
@endsection