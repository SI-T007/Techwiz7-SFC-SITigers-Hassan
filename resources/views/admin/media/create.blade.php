@extends('layouts.app')

@section('title', 'New Media Asset — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.media.index') }}" class="text-muted text-decoration-none small">&larr; Back to Multimedia Management</a>
            </div>
            <h1 class="h2 fw-bold text-light mb-0">Create New Media Asset</h1>
            <p class="text-muted small mb-0">Add official trailers, soundtracks, character concept art, or animated explainers</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="fh-card p-4 p-md-5">
                <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="form-label text-light fw-semibold">Asset Title <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}" 
                               placeholder="e.g., Attack on Titan Final Season — Official Main Trailer" 
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- YouTube Video URL -->
                    <div class="p-3 rounded-3 border border-danger border-opacity-25 mb-4" style="background: rgba(229, 9, 20, 0.04);">
                        <label for="youtube_url" class="form-label text-light fw-semibold">
                            <i class="bi bi-youtube text-danger me-1"></i> YouTube Video URL (or Video ID)
                        </label>
                        <input type="text" 
                               name="youtube_url" 
                               id="youtube_url" 
                               class="form-control @error('youtube_url') is-invalid @enderror" 
                               value="{{ old('youtube_url', old('external_url')) }}" 
                               placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/...">
                        @error('youtube_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">
                            Supports standard YouTube links, youtu.be short links, embed URLs, and shorts. The normalized Video ID and HD thumbnail will be auto-generated.
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Media Type -->
                        <div class="col-md-6">
                            <label for="media_type" class="form-label text-light fw-semibold">Media Type <span class="text-danger">*</span></label>
                            <select name="media_type" id="media_type" class="form-select @error('media_type') is-invalid @enderror" required>
                                @foreach(\App\Models\Media::MEDIA_TYPES as $key => $label)
                                    <option value="{{ $key }}" {{ old('media_type', 'anime_trailer') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('media_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quality Metadata -->
                        <div class="col-md-6">
                            <label for="quality" class="form-label text-light fw-semibold">Video Quality Metadata <span class="text-danger">*</span></label>
                            <select name="quality" id="quality" class="form-select @error('quality') is-invalid @enderror" required>
                                @foreach(\App\Models\Media::QUALITIES as $q)
                                    <option value="{{ $q }}" {{ old('quality', 'HD') === $q ? 'selected' : '' }}>
                                        {{ $q }}
                                    </option>
                                @endforeach
                            </select>
                            @error('quality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Category / Fandom -->
                        <div class="col-md-4">
                            <label for="category_id" class="form-label text-light fw-semibold">Fandom Realm</label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">Select Fandom (Optional)</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div class="col-md-4">
                            <label for="duration" class="form-label text-light fw-semibold">Duration (Optional)</label>
                            <input type="text" 
                                   name="duration" 
                                   id="duration" 
                                   class="form-control @error('duration') is-invalid @enderror" 
                                   value="{{ old('duration') }}" 
                                   placeholder="e.g., 2:45">
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Language -->
                        <div class="col-md-4">
                            <label for="language" class="form-label text-light fw-semibold">Audio / Language</label>
                            <input type="text" 
                                   name="language" 
                                   id="language" 
                                   class="form-control @error('language') is-invalid @enderror" 
                                   value="{{ old('language', 'Japanese') }}" 
                                   placeholder="e.g., Japanese, English, Korean">
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Release Date -->
                        <div class="col-md-6">
                            <label for="release_date" class="form-label text-light fw-semibold">Release Date</label>
                            <input type="date" 
                                   name="release_date" 
                                   id="release_date" 
                                   class="form-control @error('release_date') is-invalid @enderror" 
                                   value="{{ old('release_date') }}">
                            @error('release_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="status" class="form-label text-light fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Live on Multimedia Center)</option>
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Hidden from Public)</option>
                                <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Associated Canon Lore Content -->
                    <div class="mb-4">
                        <label for="content_id" class="form-label text-light fw-semibold">Link to Canon Lore Entry (Optional)</label>
                        <select name="content_id" id="content_id" class="form-select @error('content_id') is-invalid @enderror">
                            <option value="">No Content Link (Standalone Media)</option>
                            @foreach($contents as $content)
                                <option value="{{ $content->id }}" {{ old('content_id') == $content->id ? 'selected' : '' }}>
                                    [{{ $content->category?->name ?? 'General' }}] {{ $content->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('content_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tags -->
                    <div class="mb-4">
                        <label for="tags" class="form-label text-light fw-semibold">Tags (Comma-separated)</label>
                        <input type="text" 
                               name="tags" 
                               id="tags" 
                               class="form-control @error('tags') is-invalid @enderror" 
                               value="{{ is_array(old('tags')) ? implode(', ', old('tags')) : old('tags') }}" 
                               placeholder="e.g., Trailer, Anime, Opening, Studio Mappa, 4K">
                        @error('tags')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label text-light fw-semibold">Description</label>
                        <textarea name="description" 
                                  id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Asset synopsis, track composer details, singer/performer credits...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Custom Thumbnail Upload (Optional override for YouTube) -->
                    <div class="mb-4">
                        <label for="thumbnail" class="form-label text-light fw-semibold">Custom Thumbnail Cover (Optional Override)</label>
                        <input type="file" 
                               name="thumbnail" 
                               id="thumbnail" 
                               class="form-control @error('thumbnail') is-invalid @enderror" 
                               accept="image/jpeg,image/png,image/webp,image/jpg">
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">Leave blank to use YouTube's high-resolution poster automatically.</div>
                    </div>

                    <!-- Featured Toggle -->
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label text-light fw-semibold" for="is_featured">
                                Feature in Multimedia Spotlight & Homepage Rails
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-subtle">
                        <a href="{{ route('admin.media.index') }}" class="fh-btn-ghost">Cancel</a>
                        <button type="submit" class="fh-btn-primary px-4">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Save Media Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
