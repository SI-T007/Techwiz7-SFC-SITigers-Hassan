@extends('layouts.app')

@section('title', $content->title . ' — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="row g-4 justify-content-center">
        <!-- Main Article / Content Column -->
        <div class="col-lg-8">
            <article class="fh-card fh-card-glow p-4 p-md-5 mb-4">
                <!-- Metadata & Badges Header -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="fh-badge fh-badge-primary">
                            {{ ucfirst($content->type) }}
                        </span>
                        <a href="{{ route('fandoms.show', $content->category?->slug ?? 'all') }}" class="fh-badge fh-badge-cyan text-decoration-none">
                            {{ $content->category?->name ?? 'General' }}
                        </a>
                        @if($content->release_date)
                            <span class="small text-muted">
                                <i class="bi bi-calendar3 me-1"></i> {{ $content->release_date->format('M d, Y') }}
                            </span>
                        @endif
                    </div>

                    <!-- Bookmark Quick Action -->
                    <div>
                        @auth
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" 
                                        id="detailBookmarkBtn"
                                        class="btn btn-sm {{ $isBookmarked ? 'btn-warning text-dark' : 'btn-outline-light' }} d-inline-flex align-items-center gap-1 fh-bookmark-toggle"
                                        data-type="content"
                                        data-id="{{ $content->id }}"
                                        aria-label="Bookmark lore">
                                    <i class="bi {{ $isBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
                                    <span id="detailBookmarkText">{{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
                                </button>
                                
                                <button type="button" 
                                        class="btn btn-sm btn-outline-secondary text-light {{ $isBookmarked ? '' : 'd-none' }}" 
                                        id="bookmarkNoteToggleBtn"
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#bookmarkNoteBox"
                                        title="Personal note on this bookmark">
                                    <i class="bi bi-journal-text"></i> Note
                                </button>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary text-light" title="Sign in to bookmark">
                                <i class="bi bi-bookmark me-1"></i> Bookmark
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Personal Note Collapsible (For authenticated bookmarked item) -->
                @auth
                    <div class="collapse mb-4 {{ $isBookmarked && $userBookmark?->note ? 'show' : '' }}" id="bookmarkNoteBox">
                        <div class="p-3 rounded-3 border border-warning border-opacity-50" style="background: rgba(245, 158, 11, 0.05);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold text-warning">
                                    <i class="bi bi-journal-text me-1"></i> Your Personal Lore Note
                                </span>
                                <span class="small text-muted" id="noteSaveStatus"></span>
                            </div>
                            <form id="detailNoteForm" method="POST" action="{{ $userBookmark ? route('bookmarks.note', $userBookmark->id) : '#' }}">
                                @csrf
                                <textarea name="note" id="detailNoteInput" class="form-control form-control-sm mb-2" rows="2" placeholder="Write thoughts, theories, or reading notes...">{{ $userBookmark?->note }}</textarea>
                                <button type="submit" class="fh-btn-primary btn-sm py-1 px-3" id="saveNoteBtn">Save Note</button>
                            </form>
                        </div>
                    </div>
                @endauth

                <!-- Main Title -->
                <h1 class="h2 fw-bold text-light mb-3">{{ $content->title }}</h1>

                <!-- Genres Badges -->
                @if($content->genres->isNotEmpty())
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        @foreach($content->genres as $genre)
                            <span class="badge bg-secondary bg-opacity-25 text-light border border-subtle small">
                                #{{ $genre->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <!-- Hero / Cover Media or Video Embed -->
                @if($content->media_url)
                    <div class="mb-4 rounded-3 overflow-hidden border border-subtle" style="background: #000;">
                        @if(Str::contains($content->media_url, ['youtube.com', 'youtu.be']))
                            @php
                                $embedUrl = Str::replace('watch?v=', 'embed/', $content->media_url);
                                $embedUrl = Str::replace('youtu.be/', 'youtube.com/embed/', $embedUrl);
                            @endphp
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ $embedUrl }}" title="{{ $content->title }}" allowfullscreen></iframe>
                            </div>
                        @else
                            <div class="p-3 text-center">
                                <a href="{{ $content->media_url }}" target="_blank" rel="noopener" class="fh-btn-primary">
                                    <i class="bi bi-play-circle me-1"></i> View External Media Asset &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                @elseif($content->thumbnail)
                    <div class="mb-4 rounded-3 overflow-hidden border border-subtle">
                        <img src="{{ asset('storage/' . $content->thumbnail) }}" alt="{{ $content->title }}" class="img-fluid w-100" style="max-height: 440px; object-fit: cover;">
                    </div>
                @endif

                <!-- Lead Description -->
                <p class="lead text-secondary mb-4 pb-3 border-bottom border-subtle">
                    {{ $content->description }}
                </p>

                <!-- Body Content -->
                <div class="text-light mb-5" style="line-height: 1.85; font-size: 1.05rem;">
                    {!! nl2br(e($content->body ?: $content->description)) !!}
                </div>

                <!-- 5-Star Rating & Feedback Section -->
                <div class="p-4 rounded-3 border border-subtle mb-4" style="background: rgba(255, 255, 255, 0.02);">
                    <div class="row align-items-center g-3">
                        <!-- Aggregate Rating -->
                        <div class="col-md-6 border-end-md border-subtle">
                            <h2 class="h6 fw-bold text-light mb-1">
                                <i class="bi bi-star-fill text-warning me-1"></i> Community Rating
                            </h2>
                            <div class="d-flex align-items-baseline gap-2">
                                @if($content->ratings_avg)
                                    <span class="display-6 fw-bold text-light" id="aggregateScoreText">
                                        {{ number_format($content->ratings_avg, 1) }}
                                    </span>
                                    <span class="text-muted">/ 5.0</span>
                                    <span class="small text-muted ms-2" id="aggregateCountText">
                                        ({{ $content->ratings_count }} {{ Str::plural('rating', $content->ratings_count) }})
                                    </span>
                                @else
                                    <span class="text-muted small" id="aggregateScoreText">Not rated yet</span>
                                    <span class="small text-muted ms-1" id="aggregateCountText">(Be the first to rate)</span>
                                @endif
                            </div>
                            <!-- Mini Visual Rating Breakdown Progress Bars -->
                            <div class="mt-3 pt-2 border-top border-secondary border-opacity-25" style="max-width: 260px;">
                                @php
                                    $avg = $content->ratings_avg ?: 4.8;
                                    $pct5 = min(100, round(($avg / 5.0) * 85));
                                    $pct4 = min(100, round(($avg / 5.0) * 15));
                                @endphp
                                <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.72rem;">
                                    <span class="text-warning">5★</span>
                                    <div class="progress flex-grow-1" style="height: 4px; background: rgba(255,255,255,0.08);">
                                        <div class="progress-bar bg-warning" style="width: {{ $pct5 }}%;"></div>
                                    </div>
                                    <span class="text-muted">{{ $pct5 }}%</span>
                                </div>
                                <div class="d-flex align-items-center gap-2" style="font-size: 0.72rem;">
                                    <span class="text-warning">4★</span>
                                    <div class="progress flex-grow-1" style="height: 4px; background: rgba(255,255,255,0.08);">
                                        <div class="progress-bar bg-warning bg-opacity-75" style="width: {{ $pct4 }}%;"></div>
                                    </div>
                                    <span class="text-muted">{{ $pct4 }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- User Rating Interactive Component -->
                        <div class="col-md-6 ps-md-4">
                            <h3 class="h6 fw-bold text-light mb-1">Your Rating</h3>
                            @auth
                                <div class="d-flex align-items-center gap-1" id="ratingStarGroup">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" 
                                                class="btn btn-link p-0 text-decoration-none fh-star-btn {{ $userRating && $userRating >= $i ? 'text-warning' : 'text-secondary' }}" 
                                                data-score="{{ $i }}" 
                                                data-type="content"
                                                data-id="{{ $content->id }}"
                                                title="Rate {{ $i }} star{{ $i > 1 ? 's' : '' }}"
                                                aria-label="Rate {{ $i }} star{{ $i > 1 ? 's' : '' }}">
                                            <i class="bi {{ $userRating && $userRating >= $i ? 'bi-star-fill' : 'bi-star' }} fs-4"></i>
                                        </button>
                                    @endfor
                                </div>
                                <div class="small mt-1" id="userRatingFeedback">
                                    @if($userRating)
                                        <span class="text-info">You rated this {{ $userRating }}/5 stars</span>
                                        <button type="button" class="btn btn-link p-0 text-danger small ms-2" id="removeRatingBtn" data-type="content" data-id="{{ $content->id }}">Clear</button>
                                    @else
                                        <span class="text-muted">Click a star to record your vote</span>
                                    @endif
                                </div>
                            @else
                                <div class="small text-muted">
                                    <a href="{{ route('login') }}" class="text-info text-decoration-none fw-semibold">Sign in</a> to rate this canon entry.
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Footer Metadata & Share -->
                <div class="pt-4 border-top border-subtle d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted">Curated by:</span>
                        <span class="small fw-semibold text-light">{{ $content->author?->name ?? 'Platform Architect' }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="small text-muted">
                            <i class="bi bi-eye text-info me-1"></i> {{ number_format($content->views) }} views
                        </span>
                        <span class="small text-muted">
                            <i class="bi bi-fire text-danger me-1"></i> {{ number_format($content->popularity) }} score
                        </span>
                        <a href="{{ route('explore') }}" class="fh-btn-ghost">
                            &larr; Back to Explore
                        </a>
                    </div>
                </div>
            </article>
        </div>

        <!-- Sidebar / Related Items & Fandom Info -->
        <div class="col-lg-4">
            <!-- Related Lore Items -->
            <div class="fh-card p-4 mb-4">
                <h3 class="h6 fw-bold text-light mb-3">
                    <i class="bi bi-collection text-info me-2"></i> Related in {{ $content->category?->name }}
                </h3>
                <div class="d-flex flex-column gap-3">
                    @forelse($relatedContents as $rel)
                        <div class="p-3 rounded-3 border border-subtle" style="background: rgba(255,255,255,0.02);">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fh-badge fh-badge-primary py-0 px-2 small" style="font-size: 0.7rem;">
                                    {{ ucfirst($rel->type) }}
                                </span>
                                @if($rel->ratings_avg)
                                    <span class="small text-warning">
                                        <i class="bi bi-star-fill"></i> {{ number_format($rel->ratings_avg, 1) }}
                                    </span>
                                @endif
                            </div>
                            <div class="small fw-semibold mb-1">
                                <a href="{{ route('explore.show', $rel->slug) }}" class="text-light text-decoration-none">
                                    {{ $rel->title }}
                                </a>
                            </div>
                            <div class="small text-muted text-truncate">{{ $rel->description }}</div>
                        </div>
                    @empty
                        <span class="small text-muted">No additional items in this category yet.</span>
                    @endforelse
                </div>
            </div>

            <!-- Fandom Quick Hub Card -->
            @if($content->category)
                <div class="fh-card p-4 text-center">
                    <div class="fh-category-icon-box mx-auto mb-2">
                        <i class="bi {{ $content->category->icon ?: 'bi-stars' }}"></i>
                    </div>
                    <h4 class="h6 fw-bold text-light mb-1">{{ $content->category->name }}</h4>
                    <p class="small text-muted mb-3">{{ $content->category->description }}</p>
                    <a href="{{ route('fandoms.show', $content->category->slug) }}" class="fh-btn-primary w-100">
                        Enter Fandom Hub &rarr;
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
