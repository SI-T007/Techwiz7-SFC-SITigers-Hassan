@extends('layouts.app')

@section('title', 'Edit Fandom Category — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4" style="max-width: 800px;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h3 fw-bold text-light mb-0">Edit Category: {{ $category->name }}</h1>
            </div>
            <p class="text-muted small mb-0">Update category details, slug, banner image, and sort order</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm text-light">
            <i class="bi bi-arrow-left me-1"></i> Back to Categories
        </a>
    </div>

    <!-- Form Card -->
    <div class="fh-card p-4">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Category Name -->
            <div class="mb-3">
                <label for="name" class="form-label text-light small fw-bold">Category Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control bg-dark border-secondary text-light @error('name') is-invalid @enderror" 
                       value="{{ old('name', $category->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Slug -->
            <div class="mb-3">
                <label for="slug" class="form-label text-light small fw-bold">URL Slug <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-muted">/fandoms/</span>
                    <input type="text" name="slug" id="slug" class="form-control bg-dark border-secondary text-light @error('slug') is-invalid @enderror" 
                           value="{{ old('slug', $category->slug) }}" required>
                </div>
                @error('slug')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label text-light small fw-bold">Description</label>
                <textarea name="description" id="description" rows="3" class="form-control bg-dark border-secondary text-light @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Icon & Sort Order -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="icon" class="form-label text-light small fw-bold">Bootstrap Icon Class</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi {{ $category->icon ?: 'bi-stars' }}"></i></span>
                        <input type="text" name="icon" id="icon" class="form-control bg-dark border-secondary text-light @error('icon') is-invalid @enderror" 
                               value="{{ old('icon', $category->icon) }}" placeholder="bi-tv, bi-controller, etc.">
                    </div>
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="sort_order" class="form-label text-light small fw-bold">Sort Order</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control bg-dark border-secondary text-light @error('sort_order') is-invalid @enderror" 
                           value="{{ old('sort_order', $category->sort_order) }}" min="0">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label text-light small fw-bold">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select bg-dark border-secondary text-light @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $category->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $category->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Cover Banner Preview & Upload -->
            @if($category->cover_image)
                <div class="mb-3">
                    <label class="form-label text-light small fw-bold d-block">Current Cover Image</label>
                    <img src="{{ filter_var($category->cover_image, FILTER_VALIDATE_URL) ? $category->cover_image : asset('storage/' . $category->cover_image) }}" 
                         alt="{{ $category->name }}" class="rounded img-fluid mb-2 border border-secondary" style="max-height: 140px;">
                </div>
            @endif

            <div class="mb-3">
                <label for="cover_image" class="form-label text-light small fw-bold">Replace Cover Banner Image</label>
                <input type="file" name="cover_image" id="cover_image" class="form-control bg-dark border-secondary text-light @error('cover_image') is-invalid @enderror" accept="image/*">
                @error('cover_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Alternative URL -->
            <div class="mb-4">
                <label for="cover_image_url" class="form-label text-light small fw-bold">Or External Cover Image URL</label>
                <input type="url" name="cover_image_url" id="cover_image_url" class="form-control bg-dark border-secondary text-light @error('cover_image_url') is-invalid @enderror" 
                       value="{{ old('cover_image_url', filter_var($category->cover_image, FILTER_VALIDATE_URL) ? $category->cover_image : '') }}" placeholder="https://example.com/cover.jpg">
                @error('cover_image_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Update Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
