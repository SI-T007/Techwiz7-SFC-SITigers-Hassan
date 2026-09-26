@extends('layouts.app')

@section('title', 'My Fandom Dashboard — Fan Hub Plus')

@push('styles')
<style>
    .fh-fan-stat-card {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1rem;
        backdrop-filter: blur(12px);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        transition: transform 0.25s ease, border-color 0.25s ease;
    }
    .fh-fan-stat-card:hover {
        border-color: rgba(99, 102, 241, 0.35);
    }
    .xp-bar-container {
        height: 8px;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.08);
        overflow: hidden;
    }
    .xp-bar-fill {
        height: 100%;
        border-radius: 4px;
        background: linear-gradient(90deg, #6366f1, #06b6d4, #10b981);
        transition: width 0.8s ease;
    }
    .dash-tab-btn {
        background: rgba(255, 255, 255, 0.04);
        color: #94a3b8;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 0.6rem;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .dash-tab-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.16);
    }
    .dash-tab-btn.active {
        background: linear-gradient(135deg, rgba(229, 9, 20, 0.2), rgba(99, 102, 241, 0.2));
        color: #ffffff;
        border-color: rgba(229, 9, 20, 0.6);
        box-shadow: 0 4px 15px rgba(229, 9, 20, 0.2);
    }
    .fh-tab-scroll-bar {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .fh-tab-scroll-bar::-webkit-scrollbar {
        display: none;
    }
    .dash-item-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 0.75rem;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .dash-item-card:hover {
        transform: translateY(-2px);
        border-color: rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 24px -6px rgba(0, 0, 0, 0.4);
    }
    .dash-star-btn {
        cursor: pointer;
        transition: transform 0.15s ease;
    }
    .dash-star-btn:hover {
        transform: scale(1.25);
    }
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-3px);
    }
    .feedback-type-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #94a3b8;
        font-size: 0.8125rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        user-select: none;
    }
    .feedback-type-pill:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        transform: translateY(-1px);
    }
    .feedback-type-pill.active {
        background: rgba(229, 9, 20, 0.15);
        border-color: rgba(229, 9, 20, 0.6);
        color: #ffffff;
        box-shadow: 0 0 14px rgba(229, 9, 20, 0.3);
    }
    .feedback-type-pill[data-type="suggestion"].active {
        background: rgba(245, 158, 11, 0.15);
        border-color: rgba(245, 158, 11, 0.6);
        box-shadow: 0 0 14px rgba(245, 158, 11, 0.3);
    }
    .feedback-type-pill[data-type="query"].active {
        background: rgba(6, 182, 212, 0.15);
        border-color: rgba(6, 182, 212, 0.6);
        box-shadow: 0 0 14px rgba(6, 182, 212, 0.3);
    }
    .feedback-type-pill[data-type="other"].active {
        background: rgba(139, 92, 246, 0.15);
        border-color: rgba(139, 92, 246, 0.6);
        box-shadow: 0 0 14px rgba(139, 92, 246, 0.3);
    }
    @keyframes fbCardSlideIn {
        from {
            opacity: 0;
            transform: translateY(-12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .fh-feedback-floating-btn {
        background: linear-gradient(135deg, #e50914 0%, #ea580c 100%) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        box-shadow: 0 8px 25px rgba(229, 9, 20, 0.45) !important;
        transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.22s ease !important;
    }
    .fh-feedback-floating-btn:hover {
        transform: translateY(-3px) scale(1.03) !important;
        box-shadow: 0 12px 32px rgba(229, 9, 20, 0.65) !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-lg-5 py-4">
    <!-- Hero Banner Card with Level & XP Meter -->
    <div class="fh-card fh-card-glow p-4 p-md-5 mb-4 position-relative overflow-hidden">
        <div class="row align-items-center g-4 position-relative" style="z-index: 2;">
            <div class="col-auto">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar-lg rounded-circle border border-2 border-primary border-opacity-50" style="width: 80px; height: 80px; object-fit: cover;">
            </div>
            <div class="col">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h2 class="h2 fw-bold text-light mb-0">{{ $user->display_name }}</h2>
                    <span class="badge {{ $user->isAdmin() ? 'badge-role-admin' : 'badge-role-user' }}">
                        {{ $user->isAdmin() ? 'ADMINISTRATOR' : 'MEMBER' }}
                    </span>
                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25">
                        <i class="bi bi-shield-shaded me-1"></i> Tier {{ $fanLevel }}: {{ $fanRankTitle }}
                    </span>
                    @if($user->hasVerifiedEmail())
                        <span class="badge bg-success bg-opacity-25 text-success small">
                            <i class="bi bi-patch-check-fill me-1"></i> Verified Fan
                        </span>
                    @else
                        <a href="{{ route('verification.notice') }}" class="badge bg-warning bg-opacity-25 text-warning text-decoration-none small">
                            <i class="bi bi-exclamation-circle me-1"></i> Verify Email
                        </a>
                    @endif
                </div>
                <p class="text-muted mb-2">
                    {{ $user->profile?->bio ?: 'Universe explorer and fandom connoisseur.' }}
                </p>

                <!-- Fan XP Progress Bar -->
                <div class="row align-items-center g-2 mt-1" style="max-width: 500px;">
                    <div class="col">
                        <div class="xp-bar-container">
                            <div class="xp-bar-fill" style="width: {{ $currentLevelProgress }}%;"></div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <span class="text-info small fw-bold" style="font-size: 0.75rem;">{{ $fanXp }} XP (Level {{ $fanLevel }})</span>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3 small text-muted mt-2">
                    @if($user->profile?->location)
                        <span><i class="bi bi-geo-alt me-1 text-info"></i> {{ $user->profile->location }}</span>
                    @endif
                    @if($user->profile?->website)
                        <span><a href="{{ $user->profile->website }}" target="_blank" class="text-info text-decoration-none"><i class="bi bi-link-45deg me-1"></i> {{ parse_url($user->profile->website, PHP_URL_HOST) }}</a></span>
                    @endif
                    <span><i class="bi bi-calendar3 me-1"></i> Joined {{ $user->created_at->format('M Y') }}</span>
                </div>
            </div>
            <div class="col-md-auto d-flex flex-md-column gap-2">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary-gradient">
                    <i class="bi bi-pencil-square me-1"></i> Edit Profile
                </a>
                <a href="{{ route('profile.show') }}" class="btn btn-outline-custom">
                    <i class="bi bi-eye me-1"></i> View Public Profile
                </a>
                <button type="button" class="btn btn-danger d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #e50914 0%, #b20710 100%); border: none; box-shadow: 0 4px 14px rgba(229, 9, 20, 0.4);" onclick="switchDashboardTab('feedback'); document.getElementById('dashboardMainTabs').scrollIntoView({behavior:'smooth'});">
                    <i class="bi bi-chat-left-heart-fill"></i> Give Feedback / Bug
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl">
            <div class="fh-fan-stat-card p-3 h-100 hover-lift" style="cursor: pointer;" onclick="switchDashboardTab('fandoms')" title="View My Fandoms">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Favorite Fandoms</span>
                    <i class="bi bi-heart-fill text-danger fs-5"></i>
                </div>
                <div class="h3 fw-bold text-light mb-0">{{ $stats['fandoms_count'] }}</div>
                <div class="small text-danger mt-2 d-inline-flex align-items-center gap-1">
                    <span>Manage favorites</span> &rarr;
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="fh-fan-stat-card p-3 h-100 hover-lift" style="cursor: pointer;" onclick="switchDashboardTab('bookmarks')" title="View Saved Bookmarks">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Saved Bookmarks</span>
                    <i class="bi bi-bookmark-star-fill text-warning fs-5"></i>
                </div>
                <div class="h3 fw-bold text-light mb-0" id="statBookmarksCount">{{ $stats['bookmarks_count'] }}</div>
                <div class="small text-warning mt-2 d-inline-flex align-items-center gap-1">
                    <span>View collection</span> &rarr;
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="fh-fan-stat-card p-3 h-100 hover-lift" style="cursor: pointer;" onclick="switchDashboardTab('ratings')" title="View My Ratings & Reviews">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Reviews & Ratings</span>
                    <i class="bi bi-star-fill text-info fs-5"></i>
                </div>
                <div class="h3 fw-bold text-light mb-0" id="statRatingsCount">{{ $stats['ratings_count'] }}</div>
                <div class="small text-info mt-2 d-inline-flex align-items-center gap-1">
                    <span>{{ $userAvgRating ? number_format($userAvgRating, 1) . ' ★ avg' : 'Scored lore' }}</span> &rarr;
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="fh-fan-stat-card p-3 h-100 hover-lift">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Fan Submissions</span>
                    <i class="bi bi-palette-fill text-primary fs-5"></i>
                </div>
                <div class="h3 fw-bold text-light mb-0">{{ $stats['submissions_count'] }}</div>
                <a href="{{ route('community.my-submissions') }}" class="small text-primary text-decoration-none mt-2 d-inline-block">
                    My creations &rarr;
                </a>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="fh-fan-stat-card p-3 h-100 hover-lift" style="cursor: pointer;" onclick="switchDashboardTab('feedback')" title="Feedback & Error Reports">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Feedback & Bugs</span>
                    <i class="bi bi-chat-left-heart-fill text-danger fs-5"></i>
                </div>
                <div class="h3 fw-bold text-light mb-0" id="statFeedbackCount">{{ $stats['feedback_count'] ?? 0 }}</div>
                <div class="small text-danger mt-2 d-inline-flex align-items-center gap-1">
                    <span>Give feedback</span> &rarr;
                </div>
            </div>
        </div>
    </div>

    <!-- Watch History Quick Access Banner -->
    <div class="fh-fan-stat-card p-3 mb-3 d-flex align-items-center gap-4 flex-wrap">
        <div class="d-flex align-items-center gap-3 flex-grow-1">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-info flex-shrink-0"
                 style="width: 48px; height: 48px; background: rgba(6,182,212,0.12); font-size: 1.5rem;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="fw-bold text-light">Watch History</div>
                <div class="small text-muted">{{ $stats['watch_history_count'] }} items viewed &mdash; track every piece of lore and media you've explored</div>
            </div>
        </div>
        <a href="{{ route('watch-history.index') }}" class="btn btn-sm" style="background: rgba(6,182,212,0.15); color: #06b6d4; border: 1px solid rgba(6,182,212,0.3);">
            <i class="bi bi-play-circle me-1"></i> View History
        </a>
    </div>

    <!-- Dedicated Feedback & Error Suggestion Action Banner -->
    <div class="fh-fan-stat-card p-3 mb-4 d-flex align-items-center justify-content-between gap-3 flex-wrap hover-lift"
         style="background: linear-gradient(135deg, rgba(229, 9, 20, 0.12), rgba(20, 24, 38, 0.85)); border: 1px solid rgba(229, 9, 20, 0.35); box-shadow: 0 8px 24px -6px rgba(229, 9, 20, 0.2);">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-danger flex-shrink-0"
                 style="width: 48px; height: 48px; background: rgba(229, 9, 20, 0.18); font-size: 1.5rem;">
                <i class="bi bi-chat-left-heart-fill"></i>
            </div>
            <div>
                <div class="fw-bold text-light fs-6">Feedback & Error Suggestions Hub</div>
                <div class="small text-muted">Suggest new fandom realms, request features, or report playback/glitch errors directly to our engineering team</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-primary-gradient px-3 py-2 fw-semibold" onclick="switchDashboardTab('feedback'); setFeedbackType('suggestion');">
                <i class="bi bi-lightbulb-fill me-1"></i> Give Suggestion
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger px-3 py-2 fw-semibold" onclick="switchDashboardTab('feedback'); setFeedbackType('bug');">
                <i class="bi bi-bug-fill me-1"></i> Report an Error
            </button>
        </div>
    </div>

    <!-- Personal Graphical Fan Analytics Section -->
    <div class="row g-4 mb-4">
        <!-- 1. 14-Day Personal Activity Velocity Line Chart -->
        <div class="col-lg-7">
            <div class="fh-fan-stat-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-light mb-1">
                            <i class="bi bi-activity text-primary me-2"></i>My Platform Activity Velocity
                        </h5>
                        <p class="text-muted small mb-0">Daily interactions, bookmarks, reviews, and lore explorations over the last 14 days.</p>
                    </div>
                    <span class="badge bg-primary bg-opacity-25 text-primary small">14-Day View</span>
                </div>
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="userActivityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 2. Personal Universe Affinity Radar Chart -->
        <div class="col-lg-5">
            <div class="fh-fan-stat-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-light mb-1">
                            <i class="bi bi-compass text-info me-2"></i>Universe Lore Affinity
                        </h5>
                        <p class="text-muted small mb-0">Engagement radar based on your saved lore & favorites.</p>
                    </div>
                    <span class="badge bg-info bg-opacity-25 text-info small">360° Radar</span>
                </div>
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="userRadarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Body: Fandoms & Activity -->
    <div class="row g-4">
        <!-- Left Column: Interactive Multiverse Dashboard Tabs -->
        <div class="col-lg-8">
            <!-- Navigation Tab Bar -->
            <div class="fh-fan-stat-card p-2 px-3 mb-4" id="dashboardMainTabs">
                <div class="d-flex flex-wrap align-items-center gap-2 py-1 fh-tab-scroll-bar" role="tablist">
                    <button type="button" class="btn btn-sm dash-tab-btn active px-3 py-2 fw-semibold" id="tab-btn-bookmarks" onclick="switchDashboardTab('bookmarks')">
                        <i class="bi bi-bookmark-star-fill text-warning me-2"></i>Saved Bookmarks
                        <span class="badge bg-warning bg-opacity-25 text-warning ms-2" id="dashBookmarksBadge">{{ $stats['bookmarks_count'] }}</span>
                    </button>
                    <button type="button" class="btn btn-sm dash-tab-btn px-3 py-2 fw-semibold" id="tab-btn-ratings" onclick="switchDashboardTab('ratings')">
                        <i class="bi bi-star-fill text-info me-2"></i>My Ratings & Reviews
                        <span class="badge bg-info bg-opacity-25 text-info ms-2" id="dashRatingsBadge">{{ $stats['ratings_count'] }}</span>
                    </button>
                    <button type="button" class="btn btn-sm dash-tab-btn px-3 py-2 fw-semibold" id="tab-btn-fandoms" onclick="switchDashboardTab('fandoms')">
                        <i class="bi bi-heart-fill text-danger me-2"></i>My Fandoms
                        <span class="badge bg-danger bg-opacity-25 text-danger ms-2">{{ $stats['fandoms_count'] }}</span>
                    </button>
                    <button type="button" class="btn btn-sm dash-tab-btn px-3 py-2 fw-semibold" id="tab-btn-feedback" onclick="switchDashboardTab('feedback')">
                        <i class="bi bi-chat-left-heart-fill text-danger me-2"></i>Feedback & Suggestions
                        <span class="badge bg-danger bg-opacity-25 text-danger ms-2" id="dashFeedbackBadge">{{ $stats['feedback_count'] ?? 0 }}</span>
                    </button>
                </div>
            </div>

            <!-- Tab Pane 1: Saved Bookmarks -->
            <div class="dash-tab-pane" id="pane-bookmarks">
                <div class="fh-fan-stat-card p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h5 class="fw-bold text-light mb-1">
                                <i class="bi bi-bookmark-star-fill text-warning me-2"></i>Saved Bookmarks
                            </h5>
                            <p class="text-muted small mb-0">Your saved lore articles, character dossiers, media drops, and events.</p>
                        </div>
                        <a href="{{ route('bookmarks.index') }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-collection me-1"></i> Full Collection ({{ $stats['bookmarks_count'] }}) &rarr;
                        </a>
                    </div>

                    @if($userBookmarks->count() > 0)
                        <div class="row g-3" id="dashBookmarksContainer">
                            @foreach($userBookmarks as $bm)
                                @php
                                    $item = $bm->bookmarkable;
                                    $isContent = $item instanceof \App\Models\Content;
                                    $isCharacter = $item instanceof \App\Models\Character;
                                    $isRelease = $item instanceof \App\Models\Release;
                                    $isEvent = $item instanceof \App\Models\Event;
                                    $isMedia = $item instanceof \App\Models\Media;

                                    $title = $item?->title ?? $item?->name ?? 'Saved Universe Item';
                                    $url = '#';
                                    $typeLabel = 'Item';
                                    $badgeBg = 'bg-secondary';
                                    $iconClass = 'bi-bookmark';

                                    if ($isContent) {
                                        $url = route('explore.show', $item->slug);
                                        $typeLabel = ucfirst($item->type ?? 'Lore');
                                        $badgeBg = 'bg-danger';
                                        $iconClass = 'bi-journal-bookmark';
                                    } elseif ($isCharacter) {
                                        $url = route('characters.show', $item->slug);
                                        $typeLabel = 'Character';
                                        $badgeBg = 'bg-info text-dark';
                                        $iconClass = 'bi-person-badge';
                                    } elseif ($isRelease) {
                                        $url = route('releases.index');
                                        $typeLabel = 'Release Drop';
                                        $badgeBg = 'bg-warning text-dark';
                                        $iconClass = 'bi-calendar-event';
                                    } elseif ($isEvent) {
                                        $url = route('events.index');
                                        $typeLabel = 'Event';
                                        $badgeBg = 'bg-success';
                                        $iconClass = 'bi-broadcast';
                                    } elseif ($isMedia) {
                                        $url = route('multimedia.show', $item->slug ?? $item->id);
                                        $typeLabel = 'Media';
                                        $badgeBg = 'bg-primary';
                                        $iconClass = 'bi-play-circle';
                                    }
                                @endphp

                                <div class="col-md-6" id="dash-bm-card-{{ $bm->id }}">
                                    <div class="dash-item-card p-3 h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge {{ $badgeBg }} small fw-semibold">
                                                    <i class="bi {{ $iconClass }} me-1"></i>{{ $typeLabel }}
                                                </span>
                                                <button type="button" 
                                                        class="btn btn-sm btn-link text-danger p-0 text-decoration-none dash-remove-bm-btn"
                                                        data-id="{{ $bm->id }}"
                                                        data-delete-url="{{ route('bookmarks.destroy', $bm->id) }}"
                                                        data-title="{{ e($title) }}"
                                                        title="Remove from bookmarks">
                                                    <i class="bi bi-trash3"></i> Remove
                                                </button>
                                            </div>
                                            <h6 class="fw-bold text-light mb-1 text-truncate" title="{{ $title }}">
                                                @if($item)
                                                    <a href="{{ $url }}" class="text-light text-decoration-none hover-primary">
                                                        {{ $title }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">(Item no longer available)</span>
                                                @endif
                                            </h6>
                                            <div class="small text-muted mb-2">
                                                @if($item && isset($item->category))
                                                    <span class="text-info">{{ $item->category?->name }}</span> &bull;
                                                @endif
                                                <span>Saved {{ $bm->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if($bm->note)
                                                <div class="small text-secondary p-2 rounded mb-2" style="background: rgba(255,255,255,0.03); border-left: 2px solid #e50914;">
                                                    <i class="bi bi-quote me-1 text-muted"></i>{{ Str::limit($bm->note, 85) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="pt-2 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                                            <a href="{{ $url }}" class="small text-info text-decoration-none fw-semibold">
                                                View details &rarr;
                                            </a>
                                            <span class="badge bg-secondary bg-opacity-25 text-muted" style="font-size: 0.65rem;">
                                                ID #{{ $bm->id }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 border border-dashed border-secondary border-opacity-25 rounded-3">
                            <i class="bi bi-bookmark-plus fs-2 text-warning mb-2 d-block"></i>
                            <h6 class="text-light fw-bold">No bookmarks saved yet</h6>
                            <p class="small text-muted mb-3">Save lore entries, character dossiers, and video drops to access them quickly here.</p>
                            <a href="{{ route('explore') }}" class="btn btn-sm btn-primary-gradient">
                                <i class="bi bi-compass me-1"></i> Explore Lore & Content
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab Pane 2: My Ratings & Reviews -->
            <div class="dash-tab-pane d-none" id="pane-ratings">
                <div class="fh-fan-stat-card p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h5 class="fw-bold text-light mb-1">
                                <i class="bi bi-star-fill text-info me-2"></i>My Ratings & Reviews
                            </h5>
                            <p class="text-muted small mb-0">Track, update, or remove every rating you've given across multiverse content.</p>
                        </div>
                        @if($userAvgRating)
                            <div class="badge bg-info bg-opacity-25 text-info px-3 py-2 fs-6 border border-info border-opacity-25">
                                <i class="bi bi-star-fill text-warning me-1"></i> {{ number_format($userAvgRating, 1) }} / 5.0 Average Score
                            </div>
                        @endif
                    </div>

                    <!-- Rating Distribution Summary Box -->
                    <div class="p-3 rounded-3 mb-4 border border-secondary border-opacity-25" style="background: rgba(255,255,255,0.02);">
                        <div class="row align-items-center g-3">
                            <div class="col-md-4 text-center border-end border-secondary border-opacity-25">
                                <div class="display-5 fw-bold text-warning mb-0" id="dashAvgRatingDisplay">
                                    {{ $userAvgRating ? number_format($userAvgRating, 1) : '—' }}
                                </div>
                                <div class="text-warning small mb-1">
                                    @php $rounded = round($userAvgRating ?? 0); @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $rounded ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                                <div class="small text-muted">Across {{ $stats['ratings_count'] }} recorded scores</div>
                            </div>
                            <div class="col-md-8">
                                @php $totalRatings = max(1, $stats['ratings_count']); @endphp
                                @foreach([5, 4, 3, 2, 1] as $starVal)
                                    @php
                                        $cnt = $userRatingDistribution[(string)$starVal] ?? 0;
                                        $pct = round(($cnt / $totalRatings) * 100);
                                    @endphp
                                    <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.8rem;">
                                        <span class="text-light" style="width: 38px;">{{ $starVal }} <i class="bi bi-star-fill text-warning" style="font-size: 0.7rem;"></i></span>
                                        <div class="progress flex-grow-1" style="height: 6px; background: rgba(255,255,255,0.08);">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="text-muted" style="width: 52px; text-align: right;">{{ $cnt }} ({{ $pct }}%)</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- User Rated Items List -->
                    @if($userRatingsList->count() > 0)
                        <div class="row g-3" id="dashRatingsContainer">
                            @foreach($userRatingsList as $rating)
                                @php
                                    $item = $rating->ratable;
                                    $isContent = $item instanceof \App\Models\Content;
                                    $isCharacter = $item instanceof \App\Models\Character;
                                    $isRelease = $item instanceof \App\Models\Release;
                                    $isMedia = $item instanceof \App\Models\Media;

                                    $title = $item?->title ?? $item?->name ?? 'Multiverse Lore Item';
                                    $url = '#';
                                    $typeLabel = 'Item';
                                    $badgeBg = 'bg-secondary';
                                    $rawType = strtolower(class_basename($rating->ratable_type));

                                    if ($isContent) {
                                        $url = route('explore.show', $item->slug);
                                        $typeLabel = ucfirst($item->type ?? 'Lore');
                                        $badgeBg = 'bg-danger';
                                        $rawType = 'content';
                                    } elseif ($isCharacter) {
                                        $url = route('characters.show', $item->slug);
                                        $typeLabel = 'Character';
                                        $badgeBg = 'bg-info text-dark';
                                        $rawType = 'character';
                                    } elseif ($isRelease) {
                                        $url = route('releases.index');
                                        $typeLabel = 'Release Drop';
                                        $badgeBg = 'bg-warning text-dark';
                                        $rawType = 'release';
                                    } elseif ($isMedia) {
                                        $url = route('multimedia.show', $item->slug ?? $item->id);
                                        $typeLabel = 'Media';
                                        $badgeBg = 'bg-primary';
                                        $rawType = 'media';
                                    }
                                @endphp

                                <div class="col-md-6" id="dash-rating-card-{{ $rating->id }}">
                                    <div class="dash-item-card p-3 h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge {{ $badgeBg }} small fw-semibold">{{ $typeLabel }}</span>
                                                <button type="button" 
                                                        class="btn btn-sm btn-link text-danger p-0 text-decoration-none dash-remove-rating-btn"
                                                        data-id="{{ $rating->id }}"
                                                        data-delete-url="{{ route('ratings.destroy-id', $rating->id) }}"
                                                        data-title="{{ e($title) }}"
                                                        title="Delete rating">
                                                    <i class="bi bi-trash3"></i> Delete
                                                </button>
                                            </div>
                                            <h6 class="fw-bold text-light mb-1 text-truncate" title="{{ $title }}">
                                                @if($item)
                                                    <a href="{{ $url }}" class="text-light text-decoration-none hover-primary">
                                                        {{ $title }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">(Item no longer available)</span>
                                                @endif
                                            </h6>
                                            <div class="small text-muted mb-2">
                                                <span>Rated {{ $rating->updated_at->diffForHumans() }}</span>
                                            </div>

                                            <!-- Live interactive 5-Star rating on card -->
                                            <div class="dash-rating-stars-group d-flex align-items-center gap-1 my-2"
                                                 data-rating-id="{{ $rating->id }}"
                                                 data-type="{{ $rawType }}"
                                                 data-item-id="{{ $rating->ratable_id }}">
                                                @for($s = 1; $s <= 5; $s++)
                                                    <button type="button" 
                                                            class="btn btn-link p-0 text-decoration-none dash-star-btn {{ $rating->score >= $s ? 'text-warning' : 'text-secondary' }}"
                                                            data-score="{{ $s }}"
                                                            title="Rate {{ $s }} stars">
                                                        <i class="bi {{ $rating->score >= $s ? 'bi-star-fill' : 'bi-star' }} fs-5"></i>
                                                    </button>
                                                @endfor
                                                <span class="small text-muted ms-2 rating-feedback-label">
                                                    <strong class="text-light">{{ $rating->score }}/5</strong> stars
                                                </span>
                                            </div>
                                        </div>
                                        <div class="pt-2 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                                            <a href="{{ $url }}" class="small text-info text-decoration-none fw-semibold">
                                                Go to entry &rarr;
                                            </a>
                                            <span class="badge bg-secondary bg-opacity-25 text-muted" style="font-size: 0.65rem;">
                                                Rating #{{ $rating->id }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 border border-dashed border-secondary border-opacity-25 rounded-3">
                            <i class="bi bi-star fs-2 text-info mb-2 d-block"></i>
                            <h6 class="text-light fw-bold">No ratings recorded yet</h6>
                            <p class="small text-muted mb-3">Rate lore entries, multimedia drops, and character dossiers to build your fan score profile!</p>
                            <a href="{{ route('explore') }}" class="btn btn-sm btn-primary-gradient">
                                <i class="bi bi-compass me-1"></i> Explore & Rate Multiverse Lore
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab Pane 3: Favorite Fandoms -->
            <div class="dash-tab-pane d-none" id="pane-fandoms">
                <!-- Favorite Fandoms -->
                <div class="fh-fan-stat-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-light mb-0">
                            <i class="bi bi-heart-fill text-danger me-2"></i> My Favorite Fandoms
                        </h5>
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-custom">
                            <i class="bi bi-gear me-1"></i> Edit Fandoms
                        </a>
                    </div>

                    @if($favoriteCategories->count() > 0)
                        <div class="row g-3">
                            @foreach($favoriteCategories as $category)
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3 border border-secondary border-opacity-25" style="background: rgba(255,255,255,0.02);">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="fh-logo-icon" style="width: 38px; height: 38px; font-size: 1.1rem;">
                                                <i class="bi {{ $category->icon ?: 'bi-stars' }}"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('fandoms.show', $category->slug) }}" class="fw-bold text-light text-decoration-none hover-primary">{{ $category->name }}</a>
                                                <div class="small text-muted text-truncate" style="max-width: 220px;">{{ $category->description }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 border border-dashed border-secondary border-opacity-25 rounded-3">
                            <i class="bi bi-heart fs-2 text-muted mb-2 d-block"></i>
                            <h6 class="text-light">No favorite fandoms selected yet</h6>
                            <p class="small text-muted mb-3">Select your favorite categories to customize your universe feed.</p>
                            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary-gradient">
                                <i class="bi bi-plus-lg me-1"></i> Choose Fandoms Now
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Suggested Fandoms to Discover -->
                @if($suggestedCategories->count() > 0)
                    <div class="fh-fan-stat-card p-4">
                        <h5 class="fw-bold text-light mb-3">
                            <i class="bi bi-compass-fill text-info me-2"></i> Discover More Fandoms
                        </h5>
                        <div class="row g-3">
                            @foreach($suggestedCategories as $category)
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3 border border-secondary border-opacity-25 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02);">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary bg-opacity-25 text-info" style="width: 36px; height: 36px;">
                                                <i class="bi {{ $category->icon ?: 'bi-stars' }}"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-light">{{ $category->name }}</div>
                                                <div class="small text-muted">{{ $category->contents_count ?? 0 }} articles</div>
                                            </div>
                                        </div>
                                        <a href="{{ route('fandoms.show', $category->slug) }}" class="btn btn-sm btn-outline-light">
                                            Explore &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tab Pane 4: Feedback & Error Suggestions -->
            <div class="dash-tab-pane d-none" id="pane-feedback">
                <div class="fh-fan-stat-card p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h5 class="fw-bold text-light mb-1">
                                <i class="bi bi-chat-left-heart-fill text-danger me-2"></i>Feedback & Error Suggestions
                            </h5>
                            <p class="text-muted small mb-0">Submit ideas for new fandom realms, report bugs or broken features, and track status updates directly from the platform team.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="setFeedbackType('suggestion')">
                                <i class="bi bi-lightbulb-fill me-1"></i> New Suggestion
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="setFeedbackType('bug')">
                                <i class="bi bi-bug-fill me-1"></i> Report Error
                            </button>
                        </div>
                    </div>

                    <!-- Submission Form Container -->
                    <div class="p-3 p-md-4 rounded-3 border border-secondary border-opacity-25 mb-4 position-relative" style="background: rgba(255,255,255,0.02);">
                        <form id="dashFeedbackInlineForm" action="{{ route('feedback.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="redirect_to" value="dashboard">
                            
                            <!-- Type Selection Pills -->
                            <div class="mb-3">
                                <label class="form-label text-light small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Feedback Category</label>
                                <div class="d-flex flex-wrap gap-2" id="feedbackTypePills">
                                    <label class="feedback-type-pill active" data-type="suggestion">
                                        <input type="radio" name="type" value="suggestion" class="d-none" checked>
                                        <i class="bi bi-lightbulb-fill text-warning me-1"></i> Suggestion / Idea
                                    </label>
                                    <label class="feedback-type-pill" data-type="bug">
                                        <input type="radio" name="type" value="bug" class="d-none">
                                        <i class="bi bi-bug-fill text-danger me-1"></i> Error / Bug Report
                                    </label>
                                    <label class="feedback-type-pill" data-type="query">
                                        <input type="radio" name="type" value="query" class="d-none">
                                        <i class="bi bi-question-circle-fill text-info me-1"></i> Question / Help
                                    </label>
                                    <label class="feedback-type-pill" data-type="other">
                                        <input type="radio" name="type" value="other" class="d-none">
                                        <i class="bi bi-chat-heart-fill text-primary me-1"></i> General Feedback
                                    </label>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label for="fbSubject" class="form-label text-light small fw-semibold">Subject / Topic <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-dark border-secondary border-opacity-50 text-light" id="fbSubject" name="subject" required maxlength="500" placeholder="E.g., Suggest adding Demon Slayer universe lore or season 4 drop...">
                                </div>
                                <div class="col-md-5">
                                    <label for="fbContext" class="form-label text-light small fw-semibold">Affected Page / Realm <span class="text-muted">(Optional)</span></label>
                                    <input type="text" class="form-control bg-dark border-secondary border-opacity-50 text-light" id="fbContext" name="context" placeholder="E.g., /events, /multimedia, character archive...">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="fbMessage" class="form-label text-light small fw-semibold mb-0">Detailed Description <span class="text-danger">*</span></label>
                                    <span class="text-muted small" id="fbCharCount">0 / 3000</span>
                                </div>
                                <textarea class="form-control bg-dark border-secondary border-opacity-50 text-light" id="fbMessage" name="message" rows="4" required maxlength="3000" placeholder="Describe your suggestion or provide steps to reproduce the error you encountered..."></textarea>
                            </div>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
                                <span class="text-muted small">
                                    <i class="bi bi-shield-check text-success me-1"></i> Submitting as <strong class="text-light">{{ $user->name }}</strong> ({{ $user->email }})
                                </span>
                                <button type="submit" class="btn btn-primary-gradient px-4" id="fbSubmitBtn">
                                    <i class="bi bi-send-fill me-2"></i> Submit Feedback
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- User Feedback Submissions History -->
                    <h6 class="fw-bold text-light mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-info"></i> Your Previous Submissions & Status
                        <span class="badge bg-secondary bg-opacity-25 text-light" id="fbHistoryCount">{{ $userFeedback->count() }}</span>
                    </h6>

                    <div id="userFeedbackHistoryList" class="d-flex flex-column gap-3">
                        @forelse($userFeedback as $fb)
                            @php
                                $typeBadge = match($fb->type) {
                                    'bug' => ['class' => 'bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25', 'icon' => 'bi-bug-fill', 'label' => 'Bug Report'],
                                    'suggestion' => ['class' => 'bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25', 'icon' => 'bi-lightbulb-fill', 'label' => 'Suggestion'],
                                    'query' => ['class' => 'bg-info bg-opacity-25 text-info border border-info border-opacity-25', 'icon' => 'bi-question-circle-fill', 'label' => 'Question'],
                                    default => ['class' => 'bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25', 'icon' => 'bi-chat-heart-fill', 'label' => 'Feedback'],
                                };

                                $statusBadge = match($fb->status) {
                                    'read' => ['class' => 'bg-info bg-opacity-25 text-info border border-info border-opacity-25', 'icon' => 'bi-eye-fill', 'label' => 'Under Review'],
                                    'replied' => ['class' => 'bg-success bg-opacity-25 text-success border border-success border-opacity-25', 'icon' => 'bi-check2-circle', 'label' => 'Replied & Resolved'],
                                    default => ['class' => 'bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25', 'icon' => 'bi-hourglass-split', 'label' => 'Pending Review'],
                                };
                            @endphp
                            <div class="p-3 rounded-3 border border-secondary border-opacity-25 feedback-item-card" style="background: rgba(255,255,255,0.02); transition: all 0.2s ease;">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $typeBadge['class'] }} small">
                                            <i class="bi {{ $typeBadge['icon'] }} me-1"></i> {{ $typeBadge['label'] }}
                                        </span>
                                        <span class="badge {{ $statusBadge['class'] }} small">
                                            <i class="bi {{ $statusBadge['icon'] }} me-1"></i> {{ $statusBadge['label'] }}
                                        </span>
                                    </div>
                                    <span class="text-muted small" title="{{ $fb->created_at->format('M d, Y H:i') }}">
                                        {{ $fb->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <h6 class="text-light fw-bold mb-2">{{ $fb->subject }}</h6>
                                <p class="text-muted small mb-2 text-break">{{ $fb->message }}</p>

                                @if($fb->admin_reply)
                                    <div class="p-3 mt-3 rounded-3 border border-success border-opacity-25" style="background: rgba(16, 185, 129, 0.08);">
                                        <div class="d-flex align-items-center gap-2 text-success small fw-bold mb-1">
                                            <i class="bi bi-patch-check-fill"></i>
                                            Official Response from {{ $fb->repliedBy?->name ?? 'Platform Team' }}
                                            <span class="text-muted fw-normal ms-auto" style="font-size: 0.75rem;">
                                                {{ $fb->replied_at ? $fb->replied_at->diffForHumans() : 'Recently' }}
                                            </span>
                                        </div>
                                        <div class="text-light small text-break mb-0">
                                            {{ $fb->admin_reply }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-5 border border-dashed border-secondary border-opacity-25 rounded-3" id="fbEmptyState">
                                <i class="bi bi-chat-left-dots fs-2 text-danger mb-2 d-block"></i>
                                <h6 class="text-light fw-bold">No feedback submitted yet</h6>
                                <p class="small text-muted mb-3">Notice an error, missing character info, or have an idea to improve the Fan Hub experience? Let us know!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Recent Activity Feed & Feedback Action -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            <!-- Submit Feedback / Report Bug Quick Action Card -->
            <div class="fh-fan-stat-card p-4 position-relative overflow-hidden" style="background: linear-gradient(145deg, rgba(229, 9, 20, 0.08), rgba(20, 24, 38, 0.7)); border: 1px solid rgba(229, 9, 20, 0.25);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-danger flex-shrink-0"
                         style="width: 44px; height: 44px; background: rgba(229, 9, 20, 0.15); font-size: 1.4rem;">
                        <i class="bi bi-chat-left-dots-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-light mb-0">Help Shape the Multiverse</h6>
                        <small class="text-muted">Suggest features or report errors</small>
                    </div>
                </div>
                <p class="small text-muted mb-3">Found a broken link, visual glitch, or have an idea for a new fandom realm? Share it directly with our engineers.</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-primary-gradient flex-grow-1" onclick="switchDashboardTab('feedback'); setFeedbackType('suggestion');">
                        <i class="bi bi-lightbulb-fill me-1"></i> Give Idea
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="switchDashboardTab('feedback'); setFeedbackType('bug');" title="Report a Bug or Error">
                        <i class="bi bi-bug-fill me-1"></i> Report Error
                    </button>
                </div>
            </div>
            <div class="fh-fan-stat-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-light mb-0">
                        <i class="bi bi-clock-history text-info me-2"></i> Recent Activity
                    </h5>
                    <span class="badge bg-secondary bg-opacity-25 text-light small">Live Log</span>
                </div>

                @if($recentActivities->count() > 0)
                    <div class="list-group list-group-flush bg-transparent">
                        @foreach($recentActivities as $activity)
                            <div class="list-group-item bg-transparent text-light px-0 py-2 border-secondary border-opacity-25">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-25 text-primary flex-shrink-0 mt-1" style="width: 24px; height: 24px; font-size: 0.75rem;">
                                        <i class="bi bi-lightning-charge-fill"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="small text-light">{{ $activity->description }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $activity->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-journal-text fs-3 mb-2 d-block"></i>
                        <p class="small mb-0">No recorded activity yet. Explore lore, bookmark articles, or chat with the AI assistant!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Floating Quick-Feedback & Bug Launcher Button (Always visible on dashboard) -->
    <div class="position-fixed d-flex align-items-center" style="bottom: 90px; right: 20px; z-index: 1039;">
        <button type="button" 
                onclick="switchDashboardTab('feedback'); document.getElementById('dashboardMainTabs').scrollIntoView({behavior:'smooth'});"
                class="btn rounded-pill shadow-lg d-flex align-items-center gap-2 px-3 py-2 text-white border-0 fh-feedback-floating-btn"
                title="Give Feedback or Report a Bug">
            <i class="bi bi-chat-left-heart-fill"></i>
            <span class="d-none d-sm-inline fw-semibold" style="font-size: 0.85rem;">Feedback & Bugs</span>
            <span class="badge bg-white text-danger fw-bold ms-1" style="font-size: 0.72rem; border-radius: 9999px;" id="floatingFeedbackBadge">{{ $stats['feedback_count'] ?? 0 }}</span>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
function switchDashboardTab(tabName, shouldScroll = true) {
    const tabs = ['bookmarks', 'ratings', 'fandoms', 'feedback'];
    tabs.forEach(t => {
        const btn = document.getElementById(`tab-btn-${t}`);
        const pane = document.getElementById(`pane-${t}`);
        if (btn) {
            if (t === tabName) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        }
        if (pane) {
            if (t === tabName) {
                pane.classList.remove('d-none');
            } else {
                pane.classList.add('d-none');
            }
        }
    });

    try {
        history.replaceState(null, null, `#${tabName}`);
    } catch (e) {}

    if (shouldScroll) {
        const target = (tabName === 'feedback')
            ? (document.getElementById('dashFeedbackInlineForm') || document.getElementById('dashboardMainTabs'))
            : document.getElementById('dashboardMainTabs');
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
}

function setFeedbackType(type) {
    const pills = document.querySelectorAll('#feedbackTypePills .feedback-type-pill');
    pills.forEach(pill => {
        const input = pill.querySelector('input[type="radio"]');
        if (pill.dataset.type === type) {
            pill.classList.add('active');
            if (input) input.checked = true;
        } else {
            pill.classList.remove('active');
            if (input) input.checked = false;
        }
    });

    const subjectInput = document.getElementById('fbSubject');
    if (subjectInput) {
        if (type === 'bug') {
            subjectInput.placeholder = 'E.g., Broken video playback on mobile, character page missing stats, 404 error...';
        } else if (type === 'suggestion') {
            subjectInput.placeholder = 'E.g., Suggest adding Demon Slayer universe lore or watch progress sync...';
        } else if (type === 'query') {
            subjectInput.placeholder = 'E.g., How do I submit my fan art to the community hub?';
        } else {
            subjectInput.placeholder = 'E.g., My thoughts on the new cinematic streaming layout...';
        }
    }
}

function openFeedbackModal(type = 'suggestion') {
    switchDashboardTab('feedback');
    setFeedbackType(type);
    const formBox = document.getElementById('dashFeedbackInlineForm');
    if (formBox) {
        formBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => {
            const subject = document.getElementById('fbSubject');
            if (subject) subject.focus();
        }, 300);
    }
}

function showDashNotification(message, type = 'success') {
    let toast = document.getElementById('dashActionToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'dashActionToast';
        toast.style.cssText = 'position: fixed; bottom: 24px; right: 24px; z-index: 9999; min-width: 280px; padding: 12px 18px; border-radius: 8px; font-weight: 500; font-size: 0.875rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); backdrop-filter: blur(8px); transition: all 0.3s ease; opacity: 0; transform: translateY(12px); pointer-events: none;';
        document.body.appendChild(toast);
    }
    const bg = type === 'success' ? 'rgba(16, 185, 129, 0.95)' : 'rgba(239, 68, 68, 0.95)';
    toast.style.background = bg;
    toast.style.color = '#ffffff';
    toast.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} me-2"></i> ${message}`;
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    clearTimeout(window.__dashToastTimer);
    window.__dashToastTimer = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(12px)';
    }, 3200);
}

// Bind to window to guarantee global availability for inline onclick handlers
window.switchDashboardTab = switchDashboardTab;
window.setFeedbackType = setFeedbackType;
window.openFeedbackModal = openFeedbackModal;
window.showDashNotification = showDashNotification;

document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";

    // Hash tab initialization
    if (window.location.hash === '#ratings') {
        switchDashboardTab('ratings');
    } else if (window.location.hash === '#fandoms') {
        switchDashboardTab('fandoms');
    } else if (window.location.hash === '#feedback' || window.location.hash === '#error' || window.location.hash === '#suggestions') {
        switchDashboardTab('feedback', true);
        if (window.location.hash === '#error') setFeedbackType('bug');
        if (window.location.hash === '#suggestions') setFeedbackType('suggestion');
        setTimeout(() => {
            const formBox = document.getElementById('dashFeedbackInlineForm');
            if (formBox) formBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 120);
    } else if (window.location.hash === '#bookmarks') {
        switchDashboardTab('bookmarks');
    }

    // 1. AJAX Bookmark Removal
    document.querySelectorAll('.dash-remove-bm-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const title = this.getAttribute('data-title') || 'this item';
            const deleteUrl = this.getAttribute('data-delete-url');
            const metaCsrf = document.querySelector('meta[name="csrf-token"]')?.content;

            if (!confirm(`Remove "${title}" from your bookmarks?`)) return;

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const res = await fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': metaCsrf,
                        'Accept': 'application/json',
                    }
                });

                const data = await res.json();
                if (data.success || res.ok) {
                    const card = document.getElementById(`dash-bm-card-${id}`);
                    if (card) {
                        card.style.transition = 'all 0.35s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.92)';
                        setTimeout(() => {
                            card.remove();
                            // Update badge counters
                            const bmBadge = document.getElementById('dashBookmarksBadge');
                            const statCount = document.getElementById('statBookmarksCount');
                            if (bmBadge) {
                                const cur = parseInt(bmBadge.textContent) || 0;
                                const next = Math.max(0, cur - 1);
                                bmBadge.textContent = next;
                                if (statCount) statCount.textContent = next;
                            }
                        }, 350);
                    }
                    showDashNotification(`"${title}" removed from bookmarks.`);
                } else {
                    showDashNotification(data.error || 'Failed to remove bookmark.', 'error');
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-trash3"></i> Remove';
                }
            } catch(err) {
                console.error('Bookmark remove error:', err);
                showDashNotification('An error occurred. Please try again.', 'error');
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-trash3"></i> Remove';
            }
        });
    });

    // 2. AJAX Rating Deletion
    document.querySelectorAll('.dash-remove-rating-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const title = this.getAttribute('data-title') || 'this rating';
            const deleteUrl = this.getAttribute('data-delete-url');
            const metaCsrf = document.querySelector('meta[name="csrf-token"]')?.content;

            if (!confirm(`Delete your rating for "${title}"?`)) return;

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const res = await fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': metaCsrf,
                        'Accept': 'application/json',
                    }
                });

                const data = await res.json();
                if (data.success || res.ok) {
                    const card = document.getElementById(`dash-rating-card-${id}`);
                    if (card) {
                        card.style.transition = 'all 0.35s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.92)';
                        setTimeout(() => {
                            card.remove();
                            // Update badge counters
                            const rBadge = document.getElementById('dashRatingsBadge');
                            const statCount = document.getElementById('statRatingsCount');
                            if (rBadge) {
                                const cur = parseInt(rBadge.textContent) || 0;
                                const next = Math.max(0, cur - 1);
                                rBadge.textContent = next;
                                if (statCount) statCount.textContent = next;
                            }
                        }, 350);
                    }
                    showDashNotification(`Rating for "${title}" deleted.`);
                } else {
                    showDashNotification(data.error || 'Failed to delete rating.', 'error');
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-trash3"></i> Delete';
                }
            } catch(err) {
                console.error('Rating delete error:', err);
                showDashNotification('An error occurred. Please try again.', 'error');
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-trash3"></i> Delete';
            }
        });
    });

    // 3. Live Dashboard Star Rating Updates
    document.querySelectorAll('.dash-rating-stars-group').forEach(group => {
        const type = group.getAttribute('data-type');
        const itemId = parseInt(group.getAttribute('data-item-id'));
        const starBtns = group.querySelectorAll('.dash-star-btn');
        const feedbackLbl = group.querySelector('.rating-feedback-label');
        const ratingUrl = document.querySelector('meta[name="rating-url"]')?.content || '/ratings';
        const metaCsrf = document.querySelector('meta[name="csrf-token"]')?.content;

        starBtns.forEach(btn => {
            btn.addEventListener('click', async function() {
                const score = parseInt(this.getAttribute('data-score'));
                starBtns.forEach(b => b.disabled = true);

                try {
                    const res = await fetch(ratingUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': metaCsrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            ratable_type: type,
                            ratable_id: itemId,
                            score: score,
                        })
                    });

                    const data = await res.json();
                    if (data.success) {
                        starBtns.forEach(b => {
                            const sVal = parseInt(b.getAttribute('data-score'));
                            const icon = b.querySelector('i');
                            if (sVal <= score) {
                                b.className = 'btn btn-link p-0 text-decoration-none dash-star-btn text-warning';
                                if (icon) icon.className = 'bi bi-star-fill fs-5';
                            } else {
                                b.className = 'btn btn-link p-0 text-decoration-none dash-star-btn text-secondary';
                                if (icon) icon.className = 'bi bi-star fs-5';
                            }
                        });

                        if (feedbackLbl) {
                            feedbackLbl.innerHTML = `<strong class="text-success">${score}/5</strong> stars <span class="badge bg-success bg-opacity-25 text-success" style="font-size: 0.65rem;">Saved!</span>`;
                        }
                        showDashNotification(`Updated rating to ${score}/5 stars!`);
                    } else {
                        showDashNotification(data.error || 'Failed to update rating.', 'error');
                    }
                } catch(err) {
                    console.error('Rating update error:', err);
                    showDashNotification('Error updating rating.', 'error');
                } finally {
                    starBtns.forEach(b => b.disabled = false);
                }
            });
        });
    });

    // 4. User 14-Day Activity Sparkline
    const userActCtx = document.getElementById('userActivityChart');
    if (userActCtx) {
        new Chart(userActCtx, {
            type: 'line',
            data: {
                labels: @json($userTimeline['labels']),
                datasets: [{
                    label: 'Personal Actions',
                    data: @json($userTimeline['activities']),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.18)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.38,
                    pointBackgroundColor: '#6366f1',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { color: 'rgba(255, 255, 255, 0.05)' } },
                    y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { precision: 0 } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // 5. User Universe Affinity Radar
    const userRadarCtx = document.getElementById('userRadarChart');
    if (userRadarCtx) {
        new Chart(userRadarCtx, {
            type: 'radar',
            data: {
                labels: @json($userRadar['labels']),
                datasets: [{
                    label: 'Affinity Score',
                    data: @json($userRadar['scores']),
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6, 182, 212, 0.25)',
                    borderWidth: 2,
                    pointBackgroundColor: '#06b6d4'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        grid: { color: 'rgba(255, 255, 255, 0.08)' },
                        angleLines: { color: 'rgba(255, 255, 255, 0.08)' },
                        pointLabels: { color: '#cbd5e1', font: { size: 10, weight: 'bold' } },
                        ticks: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // 6. Interactive Feedback Type Pills Selection & Live Char Count
    document.querySelectorAll('#feedbackTypePills .feedback-type-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            setFeedbackType(this.dataset.type);
        });
    });

    const fbMsg = document.getElementById('fbMessage');
    const fbCount = document.getElementById('fbCharCount');
    if (fbMsg && fbCount) {
        fbMsg.addEventListener('input', () => {
            fbCount.textContent = `${fbMsg.value.length} / 3000`;
        });
    }

    // 7. AJAX Feedback & Error Report Form Submission
    const feedbackForm = document.getElementById('dashFeedbackInlineForm');
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('fbSubmitBtn');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Submitting...';

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                if (data.success) {
                    showDashNotification(data.message, 'success');
                    feedbackForm.reset();
                    if (fbCount) fbCount.textContent = '0 / 3000';
                    setFeedbackType('suggestion');

                    // Update badge counters
                    const badge = document.getElementById('dashFeedbackBadge');
                    if (badge) {
                        const cur = parseInt(badge.textContent.trim()) || 0;
                        badge.textContent = cur + 1;
                    }
                    const histBadge = document.getElementById('fbHistoryCount');
                    if (histBadge) {
                        const currHist = parseInt(histBadge.textContent.trim()) || 0;
                        histBadge.textContent = currHist + 1;
                    }
                    const floatBadge = document.getElementById('floatingFeedbackBadge');
                    if (floatBadge) {
                        const curFloat = parseInt(floatBadge.textContent.trim()) || 0;
                        floatBadge.textContent = curFloat + 1;
                    }
                    const statCount = document.getElementById('statFeedbackCount');
                    if (statCount) {
                        const curStat = parseInt(statCount.textContent.trim()) || 0;
                        statCount.textContent = curStat + 1;
                    }

                    // Remove empty state if present
                    const empty = document.getElementById('fbEmptyState');
                    if (empty) empty.remove();

                    // Prepend card to history with slide-down animation
                    const historyList = document.getElementById('userFeedbackHistoryList');
                    if (historyList && data.feedback) {
                        const fb = data.feedback;
                        const typeConfig = {
                            bug: { class: 'bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25', icon: 'bi-bug-fill', label: 'Bug Report' },
                            suggestion: { class: 'bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25', icon: 'bi-lightbulb-fill', label: 'Suggestion' },
                            query: { class: 'bg-info bg-opacity-25 text-info border border-info border-opacity-25', icon: 'bi-question-circle-fill', label: 'Question' },
                            other: { class: 'bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25', icon: 'bi-chat-heart-fill', label: 'Feedback' },
                        }[fb.type] || { class: 'bg-primary bg-opacity-25 text-primary', icon: 'bi-chat-heart-fill', label: 'Feedback' };

                        const newCard = document.createElement('div');
                        newCard.className = 'p-3 rounded-3 border border-secondary border-opacity-25 feedback-item-card';
                        newCard.style.cssText = 'background: rgba(255,255,255,0.02); animation: fbCardSlideIn 0.35s ease-out both;';
                        newCard.innerHTML = `
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge ${typeConfig.class} small">
                                        <i class="bi ${typeConfig.icon} me-1"></i> ${typeConfig.label}
                                    </span>
                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 small">
                                        <i class="bi bi-hourglass-split me-1"></i> Pending Review
                                    </span>
                                </div>
                                <span class="text-muted small">Just now</span>
                            </div>
                            <h6 class="text-light fw-bold mb-2">${escapeHtml(fb.subject)}</h6>
                            <p class="text-muted small mb-0 text-break">${escapeHtml(fb.message)}</p>
                        `;
                        historyList.prepend(newCard);
                    }
                } else {
                    showDashNotification(data.message || 'Error submitting feedback. Please try again.', 'error');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                showDashNotification('Submission error. Please verify your inputs and try again.', 'error');
            });
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endpush
