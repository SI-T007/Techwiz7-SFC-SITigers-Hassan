@extends('layouts.app')

@section('title', 'Profile & Settings — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Sidebar Navigation Pills -->
        <div class="col-lg-3">
            <div class="fh-card p-3 sticky-lg-top" style="top: 80px;">
                <div class="d-flex align-items-center gap-3 p-2 mb-3 border-bottom border-secondary border-opacity-25">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar-img" width="46" height="46">
                    <div class="overflow-hidden">
                        <div class="fw-bold text-light text-truncate">{{ $user->display_name }}</div>
                        <span class="badge {{ $user->isAdmin() ? 'badge-role-admin' : 'badge-role-user' }} small">
                            {{ $user->isAdmin() ? 'ADMIN' : 'MEMBER' }}
                        </span>
                    </div>
                </div>

                <div class="nav flex-column nav-pills" id="settings-tabs" role="tablist">
                    <button class="nav-link text-start active" id="tab-profile-btn" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab">
                        <i class="bi bi-person me-2"></i> Personal Details
                    </button>
                    <button class="nav-link text-start" id="tab-avatar-btn" data-bs-toggle="pill" data-bs-target="#tab-avatar" type="button" role="tab">
                        <i class="bi bi-image me-2"></i> Avatar & Image
                    </button>
                    <button class="nav-link text-start" id="tab-fandoms-btn" data-bs-toggle="pill" data-bs-target="#tab-fandoms" type="button" role="tab">
                        <i class="bi bi-heart me-2"></i> Favorite Fandoms
                    </button>
                    <button class="nav-link text-start" id="tab-preferences-btn" data-bs-toggle="pill" data-bs-target="#tab-preferences" type="button" role="tab">
                        <i class="bi bi-sliders me-2"></i> Preferences
                    </button>
                    <button class="nav-link text-start" id="tab-security-btn" data-bs-toggle="pill" data-bs-target="#tab-security" type="button" role="tab">
                        <i class="bi bi-shield-lock me-2"></i> Security & Password
                    </button>
                    <button class="nav-link text-start text-danger" id="tab-danger-btn" data-bs-toggle="pill" data-bs-target="#tab-danger" type="button" role="tab">
                        <i class="bi bi-trash me-2"></i> Danger Zone
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Settings Content Area -->
        <div class="col-lg-9">
            <div class="tab-content" id="settings-tabs-content">

                <!-- 1. PERSONAL DETAILS TAB -->
                <div class="tab-pane fade show active" id="tab-profile" role="tabpanel">
                    <div class="fh-card p-4 p-md-5">
                        <div class="mb-4">
                            <h4 class="fw-bold text-light mb-1">Personal Details</h4>
                            <p class="text-muted small">Update your public identity and profile information across the universe</p>
                        </div>

                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="display_name" class="form-label">Display Name / Handle</label>
                                    <input type="text" class="form-control @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name', $user->profile?->display_name) }}" placeholder="e.g. StarLord99">
                                    @error('display_name')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control text-muted" id="email" value="{{ $user->email }}" disabled readonly>
                                <div class="form-text text-muted small">Primary account email used for authentication and security notifications.</div>
                            </div>

                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio / About Me</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4" placeholder="Tell the universe about your favorite fandoms, theories, and obsessions...">{{ old('bio', $user->profile?->bio) }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="location" class="form-label">Location / Origin</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark border-secondary border-opacity-25 text-muted"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $user->profile?->location) }}" placeholder="e.g. Tokyo, Japan or Gotham City">
                                    </div>
                                    @error('location')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="website" class="form-label">Personal Website / Link</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark border-secondary border-opacity-25 text-muted"><i class="bi bi-link-45deg"></i></span>
                                        <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $user->profile?->website) }}" placeholder="https://yourfandomblog.com">
                                    </div>
                                    @error('website')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary-gradient">
                                <i class="bi bi-check-circle me-1"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 2. AVATAR UPLOAD TAB -->
                <div class="tab-pane fade" id="tab-avatar" role="tabpanel">
                    <div class="fh-card p-4 p-md-5">
                        <div class="mb-4">
                            <h4 class="fw-bold text-light mb-1">Avatar & Visual Identity</h4>
                            <p class="text-muted small">Upload a custom profile image to represent you across community threads, ratings, and reviews.</p>
                        </div>

                        <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="d-flex flex-column flex-sm-row align-items-center gap-4 mb-4 p-4 rounded-3 border border-secondary border-opacity-25 bg-dark bg-opacity-50">
                                <div>
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avatarPreview" class="avatar-lg bg-dark">
                                </div>
                                <div class="flex-grow-1 text-center text-sm-start">
                                    <h6 class="fw-bold text-light mb-1">Upload New Avatar</h6>
                                    <p class="small text-muted mb-2">Supported formats: JPG, PNG, WEBP, GIF. Max file size: 2MB.</p>
                                    <input type="file" name="avatar" id="avatarInput" class="form-control form-control-sm @error('avatar') is-invalid @enderror" accept="image/*" onchange="previewAvatar(event)">
                                    @error('avatar')
                                        <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary-gradient">
                                <i class="bi bi-cloud-arrow-up me-1"></i> Upload Avatar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 3. FAVORITE FANDOMS TAB -->
                <div class="tab-pane fade" id="tab-fandoms" role="tabpanel">
                    <div class="fh-card p-4 p-md-5">
                        <div class="mb-4">
                            <h4 class="fw-bold text-light mb-1">My Favorite Fandoms</h4>
                            <p class="text-muted small">Select the fandom universes you love. This tailors your home feed and dashboard.</p>
                        </div>

                        <form method="POST" action="{{ route('profile.fandoms') }}">
                            @csrf

                            <div class="row g-3 mb-4">
                                @foreach($allCategories as $category)
                                    <div class="col-md-6">
                                        <label class="d-flex align-items-start gap-3 p-3 rounded-3 border border-secondary border-opacity-25 cursor-pointer h-100" style="background: rgba(255,255,255,0.02); cursor: pointer;">
                                            <input class="form-check-input mt-1" type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, $selectedCategoryIds) ? 'checked' : '' }}>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi {{ $category->icon ?: 'bi-stars' }} text-info"></i>
                                                    <span class="fw-bold text-light">{{ $category->name }}</span>
                                                </div>
                                                <div class="small text-muted mt-1">{{ $category->description }}</div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary-gradient">
                                <i class="bi bi-heart-fill me-1 text-danger"></i> Update Favorite Fandoms
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 4. PREFERENCES TAB -->
                <div class="tab-pane fade" id="tab-preferences" role="tabpanel">
                    <div class="fh-card p-4 p-md-5">
                        <div class="mb-4">
                            <h4 class="fw-bold text-light mb-1">Display & Accessibility Preferences</h4>
                            <p class="text-muted small">Customize your interface theme, motion settings, and readability parameters</p>
                        </div>

                        @php
                            $disp = $user->profile?->display_preferences ?? [];
                            $access = $user->profile?->accessibility_preferences ?? [];
                        @endphp

                        <form method="POST" action="{{ route('profile.preferences') }}">
                            @csrf

                            <h6 class="text-uppercase small text-info fw-semibold mb-3">Display Settings</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="theme" class="form-label">Theme Mode</label>
                                    <select class="form-select" id="theme" name="theme">
                                        <option value="dark" {{ ($disp['theme'] ?? 'dark') === 'dark' ? 'selected' : '' }}>Dark (Default Cyber)</option>
                                        <option value="light" {{ ($disp['theme'] ?? '') === 'light' ? 'selected' : '' }}>Light</option>
                                        <option value="system" {{ ($disp['theme'] ?? '') === 'system' ? 'selected' : '' }}>System Adaptive</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="font_size" class="form-label">Typography Scaling</label>
                                    <select class="form-select" id="font_size" name="font_size">
                                        <option value="small" {{ ($access['font_size'] ?? '') === 'small' ? 'selected' : '' }}>Compact (Small)</option>
                                        <option value="normal" {{ ($access['font_size'] ?? 'normal') === 'normal' ? 'selected' : '' }}>Standard (Normal)</option>
                                        <option value="large" {{ ($access['font_size'] ?? '') === 'large' ? 'selected' : '' }}>Accessible (Large)</option>
                                    </select>
                                </div>
                            </div>

                            <h6 class="text-uppercase small text-info fw-semibold mb-3">Accessibility & Motion</h6>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="animations" name="animations" value="1" {{ ($disp['animations'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label text-light small" for="animations">
                                    Enable UI animations and micro-transitions
                                </label>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="reduced_motion" name="reduced_motion" value="1" {{ ($access['reduced_motion'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label text-light small" for="reduced_motion">
                                    Reduced Motion (prefers minimal animation)
                                </label>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="high_contrast" name="high_contrast" value="1" {{ ($access['high_contrast'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label text-light small" for="high_contrast">
                                    High Contrast Mode (enhanced border definition)
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary-gradient">
                                <i class="bi bi-sliders me-1"></i> Save Preferences
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 5. SECURITY & PASSWORD TAB -->
                <div class="tab-pane fade" id="tab-security" role="tabpanel">
                    <div class="fh-card p-4 p-md-5">
                        <div class="mb-4">
                            <h4 class="fw-bold text-light mb-1">Security & Password</h4>
                            <p class="text-muted small">Update your secret credentials regularly to protect your account</p>
                        </div>

                        <form method="POST" action="{{ route('profile.password') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required placeholder="Verify existing password">
                                @error('current_password')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="new_password" name="password" required placeholder="Min. 8 characters">
                                    @error('password')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="new_password_confirmation" name="password_confirmation" required placeholder="Repeat new password">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary-gradient">
                                <i class="bi bi-shield-check me-1"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 6. DANGER ZONE TAB -->
                <div class="tab-pane fade" id="tab-danger" role="tabpanel">
                    <div class="fh-card p-4 p-md-5 border-danger border-opacity-50">
                        <div class="mb-4">
                            <h4 class="fw-bold text-danger mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Danger Zone</h4>
                            <p class="text-muted small">Permanent actions regarding your account and associated universe data</p>
                        </div>

                        <div class="p-4 rounded-3 border border-danger border-opacity-25 bg-danger bg-opacity-10 mb-4">
                            <h6 class="fw-bold text-light mb-1">Delete Account</h6>
                            <p class="small text-muted mb-3">Once deleted, all your profile data, bookmarks, ratings, and activities will be permanently purged.</p>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                <i class="bi bi-trash-fill me-1"></i> Delete My Account
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content fh-card border-danger">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title text-danger fw-bold" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Account Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="modal-body text-light">
                    <p class="small text-muted mb-3">
                        Are you sure you want to permanently delete your account? This action is irreversible. Please enter your current password to confirm:
                    </p>
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="delete_password" name="password" required placeholder="Enter password to confirm">
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-25">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Permanently Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Avatar instant client-side preview
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
