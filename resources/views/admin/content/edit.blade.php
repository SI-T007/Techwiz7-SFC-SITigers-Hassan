@extends('layouts.app')

@section('title', 'Edit Content: ' . $content->title . ' — Admin Console')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.content.index') }}" class="text-muted text-decoration-none small">&larr; Back to Content Management</a>
            </div>
            <h1 class="h2 fw-bold text-light mb-0">Edit Universe Lore Entry</h1>
            <p class="text-muted small mb-0">Updating entry: <span class="text-info">{{ $content->title }}</span></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('explore.show', $content->slug) }}" target="_blank" class="btn btn-outline-info btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i> View Live Page
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="fh-card p-4 p-md-5">
                <form method="POST" action="{{ route('admin.content.update', $content->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="form-label text-light fw-semibold">Content Title <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title', $content->title) }}" 
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">Current Slug: <code>/explore/{{ $content->slug }}</code></div>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Category -->
                        <div class="col-md-4">
                            <label for="category_id" class="form-label text-light fw-semibold">Fandom Realm <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $content->category_id) == $cat->id ? 'selected' : '' }}>
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
                                <option value="article" {{ old('type', $content->type) === 'article' ? 'selected' : '' }}>Article / Lore</option>
                                <option value="video" {{ old('type', $content->type) === 'video' ? 'selected' : '' }}>Video Guide</option>
                                <option value="trailer" {{ old('type', $content->type) === 'trailer' ? 'selected' : '' }}>Official Trailer</option>
                                <option value="image" {{ old('type', $content->type) === 'image' ? 'selected' : '' }}>Image Gallery</option>
                                <option value="audio" {{ old('type', $content->type) === 'audio' ? 'selected' : '' }}>Audio Track / OST</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <label for="status" class="form-label text-light fw-semibold">Publish Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="published" {{ old('status', $content->status) === 'published' ? 'selected' : '' }}>Published (Live)</option>
                                <option value="draft" {{ old('status', $content->status) === 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                                <option value="archived" {{ old('status', $content->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Genres Checkboxes -->
                    @php
                        $assignedGenreIds = $content->genres->pluck('id')->toArray();
                    @endphp
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
                                           {{ (is_array(old('genres')) && in_array($genre->id, old('genres'))) || (!old('genres') && in_array($genre->id, $assignedGenreIds)) ? 'checked' : '' }}>
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
                                   value="{{ old('release_date', $content->release_date?->format('Y-m-d')) }}">
                            @error('release_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Featured Toggle -->
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $content->is_featured) ? 'checked' : '' }}>
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
                               value="{{ old('media_url', $content->media_url) }}" 
                               placeholder="https://www.youtube.com/watch?v=...">
                        @error('media_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Thumbnail Image Upload -->
                    <div class="mb-4">
                        <label for="thumbnail" class="form-label text-light fw-semibold">Hero Thumbnail</label>
                        @if($content->thumbnail_url)
                            <div class="mb-2 d-flex align-items-center gap-3">
                                <img src="{{ $content->thumbnail_url }}" alt="{{ $content->title }}" class="rounded border border-subtle" width="120" height="70" style="object-fit: cover;" onerror="window.handleImageFallback ? window.handleImageFallback(this) : null">
                                <span class="small text-muted">Upload a new image below to replace the existing thumbnail.</span>
                            </div>
                        @endif
                        <input type="file" 
                               name="thumbnail" 
                               id="thumbnail" 
                               class="form-control @error('thumbnail') is-invalid @enderror" 
                               accept="image/jpeg,image/png,image/webp,image/gif">
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label text-light fw-semibold">Lead Description / Summary</label>
                        <textarea name="description" 
                                  id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="3">{{ old('description', $content->description) }}</textarea>
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
                                  rows="8">{{ old('body', $content->body) }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Actions -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-subtle">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="if(confirm('Are you sure you want to delete this content item?')) document.getElementById('deleteForm').submit();">
                            <i class="bi bi-trash3 me-1"></i> Delete Content
                        </button>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.content.index') }}" class="fh-btn-ghost">Cancel</a>
                            <button type="submit" class="fh-btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i> Update Content
                            </button>
                        </div>
                    </div>
                </form>

                <form id="deleteForm" method="POST" action="{{ route('admin.content.destroy', $content->id) }}" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
