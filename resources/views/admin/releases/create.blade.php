@extends('layouts.app')

@section('title', 'Add Release — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.releases.index') }}">Releases</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-light mb-0">Add Fandom Release Drop</h1>
    </div>
    <a href="{{ route('admin.releases.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Releases
    </a>
</div>

<div class="fh-card p-4 p-md-5">
    <form action="{{ route('admin.releases.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <!-- Title -->
            <div class="col-md-8">
                <label for="title" class="form-label text-light fw-semibold">Release Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control bg-dark border-secondary text-light @error('title') is-invalid @enderror" 
                       value="{{ old('title') }}" placeholder="e.g. Grand Theft Auto VI, Chainsaw Man Movie" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Category -->
            <div class="col-md-4">
                <label for="category_id" class="form-label text-light fw-semibold">Fandom Realm</label>
                <select name="category_id" id="category_id" class="form-select bg-dark border-secondary text-light @error('category_id') is-invalid @enderror">
                    <option value="">General</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Format Type & Date -->
            <div class="col-md-4">
                <label for="type" class="form-label text-light fw-semibold">Media Format <span class="text-danger">*</span></label>
                <select name="type" id="type" class="form-select bg-dark border-secondary text-light @error('type') is-invalid @enderror" required>
                    <option value="game" {{ old('type') === 'game' ? 'selected' : '' }}>Video Game</option>
                    <option value="anime" {{ old('type') === 'anime' ? 'selected' : '' }}>Anime / Film</option>
                    <option value="movie" {{ old('type') === 'movie' ? 'selected' : '' }}>Theatrical Movie</option>
                    <option value="tv_series" {{ old('type') === 'tv_series' ? 'selected' : '' }}>TV / Streaming Series</option>
                    <option value="comic" {{ old('type') === 'comic' ? 'selected' : '' }}>Comic Book</option>
                    <option value="manga" {{ old('type') === 'manga' ? 'selected' : '' }}>Manga Volume</option>
                    <option value="merchandise" {{ old('type') === 'merchandise' ? 'selected' : '' }}>Merchandise / Collector Figure</option>
                    <option value="album" {{ old('type') === 'album' ? 'selected' : '' }}>Music Album / Soundtrack</option>
                    <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label for="release_date" class="form-label text-light fw-semibold">Release Date <span class="text-danger">*</span></label>
                <input type="date" name="release_date" id="release_date" class="form-control bg-dark border-secondary text-light @error('release_date') is-invalid @enderror" 
                       value="{{ old('release_date') }}" required>
                @error('release_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label for="status" class="form-label text-light fw-semibold">Release Status</label>
                <select name="status" id="status" class="form-select bg-dark border-secondary text-light @error('status') is-invalid @enderror">
                    <option value="upcoming" {{ old('status') === 'upcoming' ? 'selected' : '' }}>Upcoming Drop</option>
                    <option value="announced" {{ old('status') === 'announced' ? 'selected' : '' }}>Announced</option>
                    <option value="released" {{ old('status') === 'released' ? 'selected' : '' }}>Released / In Stores</option>
                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Platform & Developer -->
            <div class="col-md-6">
                <label for="platform" class="form-label text-light">Platform / Medium</label>
                <input type="text" name="platform" id="platform" class="form-control bg-dark border-secondary text-light @error('platform') is-invalid @enderror" 
                       value="{{ old('platform') }}" placeholder="e.g. PlayStation 5, Xbox Series X, PC, Theaters">
                @error('platform') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="developer_publisher" class="form-label text-light">Developer / Publisher / Studio</label>
                <input type="text" name="developer_publisher" id="developer_publisher" class="form-control bg-dark border-secondary text-light @error('developer_publisher') is-invalid @enderror" 
                       value="{{ old('developer_publisher') }}" placeholder="e.g. Rockstar Games, Studio MAPPA, Marvel Studios">
                @error('developer_publisher') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Tags -->
            <div class="col-md-12">
                <label for="tags" class="form-label text-light">Release Tags (Comma-separated)</label>
                <input type="text" name="tags" id="tags" class="form-control bg-dark border-secondary text-light @error('tags') is-invalid @enderror" 
                       value="{{ old('tags') }}" placeholder="e.g. Limited Edition, Pre-Order, Collectible, Steelbook">
                <div class="form-text text-secondary">
                    Quick tags: 
                    <span class="badge bg-secondary cursor-pointer me-1" onclick="appendTag('Limited Edition')">+ Limited Edition</span>
                    <span class="badge bg-secondary cursor-pointer me-1" onclick="appendTag('Pre-Order')">+ Pre-Order</span>
                    <span class="badge bg-secondary cursor-pointer me-1" onclick="appendTag('Collectible')">+ Collectible</span>
                    <span class="badge bg-secondary cursor-pointer me-1" onclick="appendTag('Special Edition')">+ Special Edition</span>
                </div>
                @error('tags') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- External Info Link -->
            <div class="col-md-12">
                <label for="external_url" class="form-label text-light">Official Product / Store URL</label>
                <input type="url" name="external_url" id="external_url" class="form-control bg-dark border-secondary text-light @error('external_url') is-invalid @enderror" 
                       value="{{ old('external_url') }}" placeholder="https://store.steampowered.com/app/...">
                @error('external_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Cover Image -->
            <div class="col-md-12">
                <label for="cover_image" class="form-label text-light">Cover Artwork (JPEG, PNG, WebP &bull; Max 5MB)</label>
                <input type="file" name="cover_image" id="cover_image" class="form-control bg-dark border-secondary text-light @error('cover_image') is-invalid @enderror">
                @error('cover_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="description" class="form-label text-light">Release Description / Synopsis</label>
                <textarea name="description" id="description" rows="5" class="form-control bg-dark border-secondary text-light @error('description') is-invalid @enderror" 
                          placeholder="Comprehensive product features, edition details, story synopsis...">{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Featured Switch -->
            <div class="col-12">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label text-light" for="is_featured">Feature on Release Radar & Homepage Carousel</label>
                </div>
            </div>

            <div class="col-12 mt-4 pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.releases.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">Create Release</button>
            </div>
        </div>
    </form>
</div>
</div>

<script>
function appendTag(tag) {
    const input = document.getElementById('tags');
    if (input.value.trim() === '') {
        input.value = tag;
    } else {
        const parts = input.value.split(',').map(s => s.trim());
        if (!parts.includes(tag)) {
            input.value = input.value + ', ' + tag;
        }
    }
}
</script>
@endsection
