@extends('layouts.app')

@section('title', $user->display_name . ' — User Profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <!-- Profile Card -->
            <div class="fh-card fh-card-glow mb-4">
                <!-- Banner Graphic Header -->
                <div style="height: 140px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.4), rgba(168, 85, 247, 0.3), rgba(6, 182, 212, 0.3)); border-top-left-radius: 16px; border-top-right-radius: 16px;"></div>

                <div class="p-4 p-md-5 pt-0 position-relative">
                    <!-- Avatar Offset -->
                    <div class="d-flex flex-wrap justify-content-between align-items-end mb-3" style="margin-top: -50px;">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar-lg bg-dark">
                        <div class="mt-3 mt-sm-0">
                            @if(Auth::id() === $user->id)
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary-gradient btn-sm">
                                    <i class="bi bi-pencil-square me-1"></i> Edit Profile
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- User Identity -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h2 class="h3 fw-bold text-light mb-0">{{ $user->display_name }}</h2>
                            <span class="badge {{ $user->isAdmin() ? 'badge-role-admin' : 'badge-role-user' }}">
                                {{ $user->isAdmin() ? 'ADMINISTRATOR' : 'MEMBER' }}
                            </span>
                        </div>
                        <div class="text-muted small">@<span>{{ Str::slug($user->name) }}</span> &bull; {{ $user->email }}</div>
                    </div>

                    <!-- Bio -->
                    <div class="mb-4">
                        <h6 class="text-uppercase small text-muted fw-semibold" style="letter-spacing: 0.05em;">About</h6>
                        <p class="text-light">
                            {{ $user->profile?->bio ?: 'This member prefers to keep their backstory shrouded in mystery.' }}
                        </p>
                    </div>

                    <!-- Metadata Items -->
                    <div class="row g-3 py-3 border-top border-bottom border-secondary border-opacity-25 mb-4">
                        <div class="col-sm-4">
                            <div class="small text-muted">Location</div>
                            <div class="fw-semibold text-light">
                                <i class="bi bi-geo-alt text-info me-1"></i>
                                {{ $user->profile?->location ?: 'Global' }}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="small text-muted">Website</div>
                            <div class="fw-semibold text-light text-truncate">
                                @if($user->profile?->website)
                                    <a href="{{ $user->profile->website }}" target="_blank" class="text-info text-decoration-none">
                                        <i class="bi bi-link-45deg me-1"></i> {{ parse_url($user->profile->website, PHP_URL_HOST) }}
                                    </a>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="small text-muted">Joined</div>
                            <div class="fw-semibold text-light">
                                <i class="bi bi-calendar3 text-primary me-1"></i>
                                {{ $user->created_at->format('F d, Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Favorite Fandoms -->
                    <div class="mb-4">
                        <h6 class="text-uppercase small text-muted fw-semibold mb-3" style="letter-spacing: 0.05em;">
                            Favorite Fandoms
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($user->favoriteCategories as $cat)
                                <div class="fandom-badge">
                                    <i class="bi {{ $cat->icon ?: 'bi-stars' }} text-info"></i>
                                    <span>{{ $cat->name }}</span>
                                </div>
                            @empty
                                <span class="text-muted small">No favorite fandoms selected yet.</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div>
                        <h6 class="text-uppercase small text-muted fw-semibold mb-3" style="letter-spacing: 0.05em;">
                            Recent Portal Milestones
                        </h6>
                        <div class="list-group list-group-flush bg-transparent">
                            @forelse($activities as $act)
                                <div class="list-group-item bg-transparent text-light px-0 py-2 border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-dot text-info fs-4"></i>
                                        <span>{{ $act->description }}</span>
                                    </div>
                                    <span class="small text-muted">{{ $act->created_at->diffForHumans() }}</span>
                                </div>
                            @empty
                                <div class="text-muted small">No recent activity recorded.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
