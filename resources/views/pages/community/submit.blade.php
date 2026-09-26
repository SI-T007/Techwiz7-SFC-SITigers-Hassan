@extends('layouts.app')

@section('title', 'Submit Fan Creation — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="fh-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <span class="small text-info fw-bold text-uppercase" style="letter-spacing: 0.08em;">Creative Universe</span>
                    <h1 class="h2 fw-bold text-light mb-1">Submit Fan Content</h1>
                    <p class="text-muted small">Share your original fan art, cosplay showcases, fiction stories, or video creations with the universe</p>
                </div>

                <!-- Moderation Notice Alert -->
                <div class="alert alert-info border-info border-opacity-25 bg-info bg-opacity-10 text-light d-flex align-items-center gap-3 mb-4" role="alert">
                    <i class="bi bi-shield-check fs-3 text-info"></i>
                    <div class="small">
                        <strong>Moderation Protocol:</strong> To ensure high quality and safety, all submissions are reviewed by platform moderators before appearing publicly in the Community Hub.
                    </div>
                </div>

                <form method="POST" action="{{ route('community.submit.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="form-label text-light fw-semibold">Title of Work <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}" 
                               placeholder="e.g., Attack on Titan: Levi Ackerman Charcoal Sketch" 
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Category / Fandom -->
                        <div class="col-md-6">
                            <label for="category_id" class="form-label text-light fw-semibold">Fandom Realm <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Fandom</option>
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

                        <!-- Submission Type -->
                        <div class="col-md-6">
                            <label for="type" class="form-label text-light fw-semibold">Creative Medium <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="fan_art" {{ old('type') === 'fan_art' ? 'selected' : '' }}>Fan Art & Illustration</option>
                                <option value="cosplay" {{ old('type') === 'cosplay' ? 'selected' : '' }}>Cosplay Photography</option>
                                <option value="fan_fiction" {{ old('type') === 'fan_fiction' ? 'selected' : '' }}>Fan Fiction & Creative Writing</option>
                                <option value="fan_video" {{ old('type') === 'fan_video' ? 'selected' : '' }}>Fan Edit / Video / Cover</option>
                                <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other Fan Project</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Image Upload (Featured Image) -->
                    <div class="mb-4">
                        <label for="featured_image" class="form-label text-light fw-semibold">Art / Photo Showcase Image</label>
                        <input type="file" 
                               name="featured_image" 
                               id="featured_image" 
                               class="form-control @error('featured_image') is-invalid @enderror" 
                               accept="image/jpeg,image/png,image/webp,image/gif,image/jpg">
                        @error('featured_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">Upload your artwork, cosplay shot, or story cover image (JPG, PNG, WebP). Maximum 2MB.</div>
                    </div>

                    <!-- External Video / Music URL -->
                    <div class="mb-4">
                        <label for="external_url" class="form-label text-light fw-semibold">External Media URL (Optional)</label>
                        <input type="url" 
                               name="external_url" 
                               id="external_url" 
                               class="form-control @error('external_url') is-invalid @enderror" 
                               value="{{ old('external_url') }}" 
                               placeholder="https://www.youtube.com/watch?v=... or https://artstation.com/...">
                        @error('external_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">Optional link to your YouTube video, animation showcase, or portfolio piece.</div>
                    </div>

                    <!-- Body / Story / Description -->
                    <div class="mb-4">
                        <label for="body" class="form-label text-light fw-semibold">Story Content / Creative Statement</label>
                        <textarea name="body" 
                                  id="body" 
                                  class="form-control @error('body') is-invalid @enderror" 
                                  rows="6" 
                                  placeholder="Describe your creative process, character backstory, fiction chapter, materials used for cosplay, or tools used...">{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-subtle">
                        <a href="{{ route('community.index') }}" class="fh-btn-ghost">Cancel</a>
                        <button type="submit" class="fh-btn-primary px-4">
                            <i class="bi bi-send-fill me-1"></i> Submit for Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
