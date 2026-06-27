@extends('layouts.admin')
@section('title', 'New Announcement')
@section('content')
<div class="page-header">
    <h1>📢 New Announcement</h1>
    <span class="subtitle">Broadcast to all users</span>
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
            <label>Video <span class="hint">(optional — MP4, max 100MB)</span></label>
            <div class="file-drop">
                <input type="file" name="video" id="videoFile"
                       accept="video/mp4,video/avi,video/quicktime">
                <div class="file-drop-ui">
                    <span class="file-icon">🎬</span>
                    <span class="file-text">Drop video here or click to browse</span>
                    <span class="file-name" id="fileName">No file selected</span>
                </div>
            </div>
            @error('video')<span class="error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group" id="playCountGroup" style="display:none">
            <label>Video Play Count <span class="hint">(kitni baar play ho)</span></label>
            <input type="number" name="video_play_count" class="form-input"
                   value="{{ old('video_play_count', 1) }}"
                   min="1" max="100" id="videoPlayCount">
            <p class="helper-text">Video itni baar play hogi users ke dashboard par</p>
            @error('video_play_count')<span class="error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label>Winning Number <span class="hint">(optional)</span></label>
            <input type="text" name="winning_number" class="form-input mono"
                   placeholder="e.g. 12, 15, 16"
                   value="{{ old('winning_number') }}">
            <p class="helper-text">Shown on user dashboard. Leave empty if no winner yet.</p>
            @error('winning_number')<span class="error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label>Extra Message <span class="hint">(optional)</span></label>
            <textarea name="extra_message" class="form-input" rows="3"
                      placeholder="e.g. Coming Soon! New feature launching next week..."
                      style="resize:vertical">{{ old('extra_message') }}</textarea>
            <p class="helper-text">Ye message user dashboard par announcement ke neeche dikhega.</p>
            @error('extra_message')<span class="error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                <input type="checkbox" name="show_winners_slide" value="1"
                       {{ old('show_winners_slide', '1') == '1' ? 'checked' : '' }}
                       style="width:18px;height:18px;cursor:pointer">
                <span>🏆 Show Winners Slide on user dashboard</span>
            </label>
            <p class="helper-text">Winners slide on/off karein — 10 random winners display honge.</p>
        </div>
        <div class="alert info" style="margin-bottom:16px">
            ℹ️ This announcement will be shown to <strong>ALL active users</strong> on their dashboard.
        </div>
        <button type="submit" class="btn-primary full-width"
                onclick="return confirm('Broadcast this announcement to all users?')">
            📢 Broadcast to All Users
        </button>
    </form>
</div>
<script>
const videoFile = document.getElementById('videoFile');
const playCountGroup = document.getElementById('playCountGroup');
videoFile.addEventListener('change', function() {
    document.getElementById('fileName').textContent =
        this.files.length ? this.files[0].name : 'No file selected';
    playCountGroup.style.display = this.files.length ? 'block' : 'none';
});
</script>
@endsection