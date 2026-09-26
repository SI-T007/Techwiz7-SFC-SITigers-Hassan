@extends('layouts.app')

@section('title', 'Create Lore Article — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4" style="max-width: 900px;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h3 fw-bold text-light mb-0">Create Lore Article</h1>
            </div>
            <p class="text-muted small mb-0">Publish canon universe analyses, guides, and lore encyclopedias</p>
        </div>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary btn-sm text-light">
            <i class="bi bi-arrow-left me-1"></i> Back to Articles
        </a>
    </div>

    <!-- Form Card -->
    <div class="fh-card p-4">
        <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Fandom Category & Status -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="category_id" class="form-label text-light small fw-bold">Fandom Universe <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select bg-dark border-secondary text-light @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Fandom Universe...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label text-light small fw-bold">Publication Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select bg-dark border-secondary text-light @error('status') is-invalid @enderror" required>
                        <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label text-light small fw-bold" for="is_featured">Featured Article</label>
                    </div>
                </div>
            </div>

            <!-- Title & Slug -->
            <div class="row g-3 mb-3">
                <div class="col-md-7">
                    <label for="title" class="form-label text-light small fw-bold">Article Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control bg-dark border-secondary text-light @error('title') is-invalid @enderror" 
                           value="{{ old('title') }}" placeholder="e.g. The Secrets of Piltover and Zaun" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-5">
                    <label for="slug" class="form-label text-light small fw-bold">URL Slug (Optional)</label>
                    <input type="text" name="slug" id="slug" class="form-control bg-dark border-secondary text-light @error('slug') is-invalid @enderror" 
                           value="{{ old('slug') }}" placeholder="auto-generated-from-title">
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Short Synopsis -->
            <div class="mb-3">
                <label for="description" class="form-label text-light small fw-bold">Short Synopsis / Excerpt</label>
                <textarea name="description" id="description" rows="2" class="form-control bg-dark border-secondary text-light @error('description') is-invalid @enderror" 
                          placeholder="Brief 1-2 sentence preview for search cards...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Full Article Body -->
            <div class="mb-3">
                <label for="body" class="form-label text-light small fw-bold">Full Article Content & Lore Body <span class="text-danger">*</span></label>
                <textarea name="body" id="body" rows="10" class="form-control bg-dark border-secondary text-light font-monospace @error('body') is-invalid @enderror" 
                          placeholder="Write rich formatted lore text, paragraphs, headers, and world-building notes..." required>{{ old('body') }}</textarea>
                @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Thumbnail Image -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="thumbnail" class="form-label text-light small fw-bold">Cover Thumbnail Image</label>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control bg-dark border-secondary text-light @error('thumbnail') is-invalid @enderror" accept="image/*">
                    <small class="text-muted">High resolution 16:9 artwork (JPEG, PNG, WebP).</small>
                    @error('thumbnail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="thumbnail_url" class="form-label text-light small fw-bold">Or External Artwork URL</label>
                    <input type="url" name="thumbnail_url" id="thumbnail_url" class="form-control bg-dark border-secondary text-light @error('thumbnail_url') is-invalid @enderror" 
                           value="{{ old('thumbnail_url') }}" placeholder="https://example.com/artwork.jpg">
                    @error('thumbnail_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Genre Tags -->
            @if(isset($genres) && $genres->count() > 0)
                <div class="mb-4">
                    <label class="form-label text-light small fw-bold d-block">Genre & Lore Themes</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($genres as $genre)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="genres[]" id="genre{{ $genre->id }}" value="{{ $genre->id }}" 
                                       {{ is_array(old('genres')) && in_array($genre->id, old('genres')) ? 'checked' : '' }}>
                                <label class="form-check-label text-muted small" for="genre{{ $genre->id }}">{{ $genre->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Submit Button -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Publish Article
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
