@extends('layouts.app')

@section('title', $media->title . ' — Multimedia Center — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="row g-4 justify-content-center">
        <!-- Main Video Theater & Player Column -->
        <div class="col-lg-8">
            <article class="fh-card fh-card-glow p-4 p-md-5 mb-4">
                <!-- Metadata Badges Header -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="fh-badge fh-badge-primary">
                            {{ $media->media_type_label }}
                        </span>
                        @if($media->quality)
                            <span class="badge {{ $media->quality === '4K' ? 'bg-danger' : ($media->quality === 'Full HD' ? 'bg-primary' : 'bg-secondary bg-opacity-75') }} text-light">
                                {{ $media->quality }}
                            </span>
                        @endif
                        @if($media->category)
                            <a href="{{ route('fandoms.show', $media->category->slug) }}" class="fh-badge fh-badge-cyan text-decoration-none">
                                {{ $media->category->name }}
                            </a>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <span class="small text-muted">
                            <i class="bi bi-eye text-info me-1"></i> {{ number_format($media->views) }} views
                        </span>
                        @if($media->external_url)
                            <a href="{{ $media->external_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger py-0 px-2 small">
                                <i class="bi bi-youtube me-1"></i> Watch on YouTube ↗
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Video Title -->
                <h1 class="h2 fw-bold text-light mb-3">{{ $media->title }}</h1>

                <!-- 16:9 Responsive Video Player -->
                <div class="mb-4 rounded-3 overflow-hidden border border-subtle shadow-lg" style="background: #000;">
                    @if($media->youtube_video_id || ($media->external_url && Str::contains($media->external_url, ['youtube.com', 'youtu.be'])))
                        @php
                            $ytId = $media->youtube_video_id ?: \App\Models\Media::extractYouTubeId($media->external_url);
                            $embedUrl = 'https://www.youtube.com/embed/' . urlencode($ytId) . '?rel=0&modestbranding=1&autoplay=0';
                        @endphp
                        <div class="ratio ratio-16x9">
                            <iframe src="{{ $embedUrl }}" 
                                    title="{{ $media->title }}" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    loading="lazy"></iframe>
                        </div>
                    @elseif($media->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($media->file_path))
                        <video controls class="w-100" style="max-height: 520px;">
                            <source src="{{ asset('storage/' . $media->file_path) }}">
                            Your browser does not support the video tag.
                        </video>
                    @elseif($media->external_url)
                        <div class="p-5 text-center">
                            <i class="bi bi-play-circle text-danger fs-1 mb-2 d-block"></i>
                            <h4 class="text-light mb-3">External Stream Asset</h4>
                            <a href="{{ $media->external_url }}" target="_blank" rel="noopener" class="fh-btn-primary">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Stream on Source Platform &rarr;
                            </a>
                        </div>
                    @else
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-film fs-1 mb-2 d-block"></i>
                            <p>Video asset is currently undergoing processing.</p>
                        </div>
                    @endif
                </div>

                <!-- 5-Star Rating & Feedback Component -->
                <div class="p-4 rounded-3 border border-subtle mb-4" style="background: rgba(255, 255, 255, 0.02);">
                    <div class="row align-items-center g-3">
                        <!-- Community Aggregate Rating -->
                        <div class="col-md-6 border-end-md border-subtle">
                            <h2 class="h6 fw-bold text-light mb-1">
                                <i class="bi bi-star-fill text-warning me-1"></i> Fan Hub Rating
                            </h2>
                            <div class="d-flex align-items-baseline gap-2">
                                @if($media->ratings_avg)
                                    <span class="display-6 fw-bold text-light" id="mediaAggregateScore">
                                        {{ number_format($media->ratings_avg, 1) }}
                                    </span>
                                    <span class="text-muted">/ 5.0</span>
                                    <span class="small text-muted ms-2" id="mediaAggregateCount">
                                        ({{ $media->ratings_count }} {{ Str::plural('rating', $media->ratings_count) }})
                                    </span>
                                @else
                                    <span class="text-muted small" id="mediaAggregateScore">Not rated yet</span>
                                    <span class="small text-muted ms-1" id="mediaAggregateCount">(Be the first to rate)</span>
                                @endif
                            </div>
                        </div>

                        <!-- User Interactive Rating Component -->
                        <div class="col-md-6 ps-md-4">
                            <h3 class="h6 fw-bold text-light mb-1">Your Rating</h3>
                            @auth
                                <div class="d-flex align-items-center gap-1" id="ratingStarGroup">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" 
                                                class="btn btn-link p-0 text-decoration-none fh-star-btn {{ $userRating && $userRating >= $i ? 'text-warning' : 'text-secondary' }}" 
                                                data-score="{{ $i }}" 
                                                data-type="media"
                                                data-id="{{ $media->id }}"
                                                title="Rate {{ $i }} star{{ $i > 1 ? 's' : '' }}"
                                                aria-label="Rate {{ $i }} star{{ $i > 1 ? 's' : '' }}">
                                            <i class="bi {{ $userRating && $userRating >= $i ? 'bi-star-fill' : 'bi-star' }} fs-4"></i>
                                        </button>
                                    @endfor
                                </div>
                                <div class="small mt-1" id="userRatingFeedback">
                                    @if($userRating)
                                        <span class="text-info">You rated this video {{ $userRating }}/5 stars</span>
                                        <button type="button" class="btn btn-link p-0 text-danger small ms-2" id="removeRatingBtn" data-type="media" data-id="{{ $media->id }}">Clear</button>
                                    @else
                                        <span class="text-muted">Click a star to record your rating</span>
                                    @endif
                                </div>
                            @else
                                <div class="small text-muted">
                                    <a href="{{ route('login') }}" class="text-info text-decoration-none fw-semibold">Sign in</a> to rate this media asset.
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Video Technical Details & Metadata -->
                <div class="row g-3 mb-4 text-secondary small">
                    @if($media->duration)
                        <div class="col-6 col-md-3">
                            <div class="p-2 rounded bg-dark bg-opacity-50 border border-subtle">
                                <span class="text-muted d-block">Duration:</span>
                                <span class="text-light fw-semibold"><i class="bi bi-clock me-1"></i> {{ $media->duration }}</span>
                            </div>
                        </div>
                    @endif
                    @if($media->language)
                        <div class="col-6 col-md-3">
                            <div class="p-2 rounded bg-dark bg-opacity-50 border border-subtle">
                                <span class="text-muted d-block">Audio Language:</span>
                                <span class="text-light fw-semibold"><i class="bi bi-translate me-1"></i> {{ $media->language }}</span>
                            </div>
                        </div>
                    @endif
                    @if($media->release_date)
                        <div class="col-6 col-md-3">
                            <div class="p-2 rounded bg-dark bg-opacity-50 border border-subtle">
                                <span class="text-muted d-block">Release Date:</span>
                                <span class="text-light fw-semibold"><i class="bi bi-calendar3 me-1"></i> {{ $media->release_date->format('M d, Y') }}</span>
                            </div>
                        </div>
                    @endif
                    <div class="col-6 col-md-3">
                        <div class="p-2 rounded bg-dark bg-opacity-50 border border-subtle">
                            <span class="text-muted d-block">Platform Source:</span>
                            <span class="text-light fw-semibold"><i class="bi bi-youtube text-danger me-1"></i> YouTube Official</span>
                        </div>
                    </div>
                </div>

                <!-- Tags if available -->
                @if(!empty($media->tags))
                    <div class="d-flex flex-wrap gap-1 mb-4">
                        @foreach($media->tags as $t)
                            <span class="badge bg-secondary bg-opacity-25 text-info border border-subtle small">
                                #{{ $t }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <!-- Description / Synopsis -->
                @if($media->description)
                    <div class="p-4 rounded-3 border border-subtle mb-4" style="background: rgba(255,255,255,0.02);">
                        <h4 class="h6 fw-bold text-light mb-2">
                            <i class="bi bi-info-circle text-info me-1"></i> About This Video
                        </h4>
                        <p class="text-secondary mb-0" style="line-height: 1.8;">
                            {{ $media->description }}
                        </p>
                    </div>
                @endif

                <!-- Associated Canon Content Link -->
                @if($media->content)
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border border-danger border-opacity-25 mb-4" style="background: rgba(229, 9, 20, 0.05);">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-journal-richtext text-danger fs-3"></i>
                            <div>
                                <span class="small text-muted d-block">Featured Lore Canon:</span>
                                <a href="{{ route('explore.show', $media->content->slug) }}" class="fw-bold text-light text-decoration-none">
                                    {{ $media->content->title }}
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('explore.show', $media->content->slug) }}" class="fh-btn-primary btn-sm">
                            Read Lore &rarr;
                        </a>
                    </div>
                @endif

                <!-- Footer Navigation -->
                <div class="pt-4 border-top border-subtle d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <a href="{{ route('multimedia.index') }}" class="fh-btn-ghost">
                        &larr; Back to Multimedia Center
                    </a>
                    @if($media->uploader)
                        <span class="small text-muted">
                            Curated by {{ $media->uploader->name }} &bull; Added {{ $media->created_at->format('M Y') }}
                        </span>
                    @endif
                </div>
            </article>
        </div>

        <!-- Sidebar / Related Media -->
        <div class="col-lg-4">
            <div class="fh-card p-4 mb-4">
                <h3 class="h6 fw-bold text-light mb-3">
                    <i class="bi bi-collection-play text-danger me-2"></i> Related in {{ $media->category?->name ?? 'Universe' }}
                </h3>
                <div class="d-flex flex-column gap-3">
                    @forelse($relatedMedia as $rel)
                        <div class="p-2 rounded-3 border border-subtle d-flex gap-2 align-items-center" style="background: rgba(255,255,255,0.02);">
                            <a href="{{ route('multimedia.show', $rel->id) }}" class="flex-shrink-0 position-relative rounded overflow-hidden" style="width: 80px; height: 50px; background: #000;">
                                <img src="{{ $rel->thumbnail_url }}" alt="{{ $rel->title }}" class="w-100 h-100 object-fit-cover" onerror="window.handleImageFallback ? window.handleImageFallback(this) : null">
                                <div class="position-absolute top-50 start-50 translate-middle text-white" style="text-shadow: 0 1px 4px rgba(0,0,0,0.8);">
                                    <i class="bi bi-play-circle-fill text-danger fs-6"></i>
                                </div>
                            </a>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fh-badge fh-badge-primary small py-0 px-1" style="font-size: 0.6rem;">
                                        {{ $rel->media_type_label }}
                                    </span>
                                    @if($rel->ratings_avg)
                                        <span class="small text-warning" style="font-size: 0.7rem;"><i class="bi bi-star-fill"></i> {{ number_format($rel->ratings_avg, 1) }}</span>
                                    @endif
                                </div>
                                <div class="small fw-semibold text-truncate">
                                    <a href="{{ route('multimedia.show', $rel->id) }}" class="text-light text-decoration-none hover-primary">
                                        {{ $rel->title }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <span class="small text-muted">No additional media in this category yet.</span>
                    @endforelse
                </div>
            </div>

            <!-- Fandom Link Card -->
            @if($media->category)
                <div class="fh-card p-4 text-center">
                    <div class="fh-category-icon-box mx-auto mb-2">
                        <i class="bi {{ $media->category->icon ?: 'bi-stars' }}"></i>
                    </div>
                    <h4 class="h6 fw-bold text-light mb-1">{{ $media->category->name }}</h4>
                    <p class="small text-muted mb-3">{{ $media->category->description }}</p>
                    <a href="{{ route('fandoms.show', $media->category->slug) }}" class="fh-btn-primary w-100">
                        Explore Fandom Realm &rarr;
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
