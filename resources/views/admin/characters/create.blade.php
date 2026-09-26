@extends('layouts.app')

@section('title', 'Create Character Profile — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4" style="max-width: 800px;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h3 fw-bold text-light mb-0">Create Character Profile</h1>
            </div>
            <p class="text-muted small mb-0">Add a new character to the universe lore database</p>
        </div>
        <a href="{{ route('admin.characters.index') }}" class="btn btn-outline-secondary btn-sm text-light">
            <i class="bi bi-arrow-left me-1"></i> Back to Characters
        </a>
    </div>

    <!-- Form Card -->
    <div class="fh-card p-4">
        <form method="POST" action="{{ route('admin.characters.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Fandom Category & Franchise -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="category_id" class="form-label text-light small fw-bold">Fandom Category <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select bg-dark border-secondary text-light @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Fandom...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="franchise" class="form-label text-light small fw-bold">Franchise / Universe <span class="text-danger">*</span></label>
                    <input type="text" name="franchise" id="franchise" class="form-control bg-dark border-secondary text-light @error('franchise') is-invalid @enderror" 
                           value="{{ old('franchise') }}" placeholder="e.g. League of Legends / Arcane" required>
                    @error('franchise')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Name & Slug -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label text-light small fw-bold">Character Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control bg-dark border-secondary text-light @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" placeholder="e.g. Jinx (Powder)" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="slug" class="form-label text-light small fw-bold">URL Slug (Optional)</label>
                    <input type="text" name="slug" id="slug" class="form-control bg-dark border-secondary text-light @error('slug') is-invalid @enderror" 
                           value="{{ old('slug') }}" placeholder="auto-generated-from-name">
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Biography -->
            <div class="mb-3">
                <label for="biography" class="form-label text-light small fw-bold">Biography & Lore History <span class="text-danger">*</span></label>
                <textarea name="biography" id="biography" rows="5" class="form-control bg-dark border-secondary text-light @error('biography') is-invalid @enderror" 
                          placeholder="Comprehensive character backstory, origins, alliances, and personality..." required>{{ old('biography') }}</textarea>
                @error('biography')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Abilities -->
            <div class="mb-3">
                <label for="abilities" class="form-label text-light small fw-bold">Abilities & Signature Powers</label>
                <input type="text" name="abilities" id="abilities" class="form-control bg-dark border-secondary text-light @error('abilities') is-invalid @enderror" 
                       value="{{ old('abilities') }}" placeholder="e.g. Hextech Firearms, Super Mega Death Rocket, Telekinesis">
                @error('abilities')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Images -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="profile_image" class="form-label text-light small fw-bold">Avatar / Profile Image</label>
                    <input type="file" name="profile_image" id="profile_image" class="form-control bg-dark border-secondary text-light @error('profile_image') is-invalid @enderror" accept="image/*">
                    <input type="url" name="profile_image_url" class="form-control bg-dark border-secondary text-light mt-1 small" placeholder="Or profile image URL..." value="{{ old('profile_image_url') }}">
                    @error('profile_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="cover_image" class="form-label text-light small fw-bold">Cover Banner Art</label>
                    <input type="file" name="cover_image" id="cover_image" class="form-control bg-dark border-secondary text-light @error('cover_image') is-invalid @enderror" accept="image/*">
                    <input type="url" name="cover_image_url" class="form-control bg-dark border-secondary text-light mt-1 small" placeholder="Or cover image URL..." value="{{ old('cover_image_url') }}">
                    @error('cover_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Options: Status & Featured -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="status" class="form-label text-light small fw-bold">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select bg-dark border-secondary text-light @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label text-light small fw-bold" for="is_featured">Feature on Character Hub</label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.characters.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Save Character Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
