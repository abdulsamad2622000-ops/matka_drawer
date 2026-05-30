@extends('layouts.admin')

@section('title', 'Create Lottery')

@section('content')
<div class="page-header">
    <h1>🎰 Create New Lottery</h1>
    <span class="subtitle">Add a new lottery package</span>
</div>

<div class="card" style="max-width:700px">
    <div class="card-header"><h3>Lottery Details</h3></div>
    <form action="{{ route('admin.lotteries.store') }}" method="POST"
          enctype="multipart/form-data" style="padding:20px">
        @csrf

        <div class="form-group">
            <label>Lottery Name *</label>
            <input type="text" name="name" class="form-input"
                   placeholder="e.g. Diamond Jackpot" value="{{ old('name') }}" required>
            @error('name')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label>Description *</label>
            <textarea name="description" class="form-input"
                      placeholder="Describe this lottery..." required>{{ old('description') }}</textarea>
            @error('description')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="two-col" style="margin-top:0">
            <div class="form-group">
                <label>Ticket Price (Rs.) *</label>
                <input type="number" name="price" class="form-input"
                       placeholder="500" min="1" value="{{ old('price') }}" required>
                @error('price')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Prize Amount (Rs.) *</label>
                <input type="number" name="prize_amount" class="form-input"
                       placeholder="500000" min="1" value="{{ old('prize_amount') }}" required>
                @error('prize_amount')<span class="error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="two-col" style="margin-top:0">
            <div class="form-group">
                <label>Total Tickets *</label>
                <input type="number" name="total_tickets" class="form-input"
                       placeholder="1000" min="1" value="{{ old('total_tickets') }}" required>
                @error('total_tickets')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Draw Date *</label>
                <input type="date" name="draw_date" class="form-input"
                       value="{{ old('draw_date') }}" required>
                @error('draw_date')<span class="error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-group">
            <label>Image <span class="hint">(optional)</span></label>
            <div class="file-drop">
                <input type="file" name="image" accept="image/*" id="imageFile">
                <div class="file-drop-ui">
                    <span class="file-icon">🖼️</span>
                    <span class="file-text">Upload lottery image</span>
                    <span class="file-name" id="imgName">No file chosen</span>
                </div>
            </div>
            @error('image')<span class="error">{{ $message }}</span>@enderror
        </div>

        <button type="submit" class="btn-primary full-width">
            🎰 Create Lottery
        </button>
    </form>
</div>

<script>
document.getElementById('imageFile').addEventListener('change', function() {
    document.getElementById('imgName').textContent =
        this.files.length ? this.files[0].name : 'No file chosen';
});
</script>
@endsection