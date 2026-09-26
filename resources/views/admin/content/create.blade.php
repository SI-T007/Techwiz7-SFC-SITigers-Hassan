@extends('layouts.app')

@section('title', 'New Content Entry — Admin Console')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.content.index') }}" class="text-muted text-decoration-none small">&larr; Back to Content Management</a>
            </div>
            <h1 class="h2 fw-bold text-light mb-0">Create New Universe Lore & Content</h1>
            <p class="text-muted small mb-0">Publish canon articles, guides, character bios, and official video links to the fandom universe</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="fh-card p-4 p-md-5">
                <form method="POST" action="{{ route('admin.content.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="form-label text-light fw-semibold">Content Title <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}" 
                               placeholder="e.g., Attack on Titan: The Secrets of the Walls" 
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">A unique URL slug will be automatically created from the title.</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Category -->
                        <div class="col-md-4">
                            <label for="category_id" class="form-label text-light fw-semibold">Fandom Realm <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
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

                        <!-- Content Type -->
                        <div class="col-md-4">
                            <label for="type" class="form-label text-light fw-semibold">Format / Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="article" {{ old('type') === 'article' ? 'selected' : '' }}>Article / Lore</option>
                                <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>Video Guide</option>
                                <option value="trailer" {{ old('type') === 'trailer' ? 'selected' : '' }}>Official Trailer</option>
                                <option value="image" {{ old('type') === 'image' ? 'selected' : '' }}>Image Gallery</option>
                                <option value="audio" {{ old('type') === 'audio' ? 'selected' : '' }}>Audio Track / OST</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <label for="status" class="form-label text-light fw-semibold">Publish Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Live)</option>
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                                <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Genres Checkboxes -->
                    <div class="mb-4">
                        <label class="form-label text-light fw-semibold mb-2">Universe Genres</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($genres as $genre)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="genres[]" 
                                           value="{{ $genre->id }}" 
                                           id="genre_{{ $genre->id }}"
                                           {{ is_array(old('genres')) && in_array($genre->id, old('genres')) ? 'checked' : '' }}>
                                    <label class="form-check-label text-light small" for="genre_{{ $genre->id }}">
                                        {{ $genre->name }}
                                    </label>
                                </div>
                            @endforeach
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

                        <!-- Featured Toggle -->
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label text-light fw-semibold" for="is_featured">
                                    Promote as Featured Lore on Homepage
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- External Media URL -->
                    <div class="mb-4">
                        <label for="media_url" class="form-label text-light fw-semibold">Media Embed / Video URL (YouTube, etc.)</label>
                        <input type="url" 
                               name="media_url" 
                               id="media_url" 
                               class="form-control @error('media_url') is-invalid @enderror" 
                               value="{{ old('media_url') }}" 
                               placeholder="https://www.youtube.com/watch?v=...">
                        @error('media_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">Optional. If provided, a responsive player will be rendered on the article detail page.</div>
                    </div>

                    <!-- Thumbnail Image Upload -->
                    <div class="mb-4">
                        <label for="thumbnail" class="form-label text-light fw-semibold">Hero Thumbnail Upload</label>
                        <input type="file" 
                               name="thumbnail" 
                               id="thumbnail" 
                               class="form-control @error('thumbnail') is-invalid @enderror" 
                               accept="image/jpeg,image/png,image/webp,image/gif">
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">Recommended: 1200x675px image (JPG, PNG, WebP). Maximum 2MB.</div>
                    </div>

                    <!-- Description (Lead excerpt) -->
                    <div class="mb-4">
                        <label for="description" class="form-label text-light fw-semibold">Lead Description / Summary</label>
                        <textarea name="description" 
                                  id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Brief summary that appears on cards and search results...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Body / Lore Article -->
                    <div class="mb-4">
                        <label for="body" class="form-label text-light fw-semibold">Full Lore Body & Canon Content</label>
                        <textarea name="body" 
                                  id="body" 
                                  class="form-control @error('body') is-invalid @enderror" 
                                  rows="8" 
                                  placeholder="Complete analysis, background lore, history, episode guides, or character breakdown...">{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Actions -->
                    <div class="d-flex justify-content-end gap-3 pt-3 border-top border-subtle">
                        <a href="{{ route('admin.content.index') }}" class="fh-btn-ghost">Cancel</a>
                        <button type="submit" class="fh-btn-primary px-4">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Save & Publish Content
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
