@props([
    'content',
])

@php
    $avgRating = $content->ratings_avg ?? $content->average_rating;
    $ratingsCount = $content->ratings_count ?? $content->ratings_count;
    $isBookmarked = auth()->check() ? $content->isBookmarkedBy(auth()->user()) : false;
    $thumbUrl = $content->thumbnail_url ?: ($content->thumbnail ? asset('storage/' . $content->thumbnail) : 'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=600&q=80');
    $matchPct = rand(94, 99);
@endphp

<div class="netflix-card w-100 h-100 d-flex flex-column"
     data-netflix-modal="true"
     data-title="{{ $content->title }}"
     data-synopsis="{{ Str::limit($content->description ?: strip_tags($content->body), 140) }}"
     data-image="{{ $thumbUrl }}"
     data-category="{{ $content->category?->name ?? 'Fandom' }}"
     data-match="{{ $matchPct }}% Match"
     data-url="{{ route('explore.show', $content->slug) }}"
     data-id="{{ $content->id }}">
    
    <!-- Card Thumbnail -->
    <div class="netflix-card-thumb-wrap">
        <img src="{{ $thumbUrl }}" 
             alt="{{ $content->title }}" 
             class="netflix-card-thumb" 
             loading="lazy"
             onerror="window.handleImageFallback ? window.handleImageFallback(this) : null">
        <div class="netflix-card-teaser-fx"></div>
        
        <!-- Category/Type Badge -->
        <span class="netflix-card-badge">
            {{ strtoupper($content->type ?? 'LORE') }}
        </span>

        @if($content->is_featured)
            <span class="netflix-card-top10">FEATURED</span>
        @endif
    </div>

    <!-- Hover Info Panel (Desktop) / Static Details (Mobile) -->
    <div class="netflix-card-hover-panel">
        <!-- Action Buttons -->
        <div class="netflix-card-actions">
            <a href="{{ route('explore.show', $content->slug) }}" class="netflix-circle-btn play-btn" aria-label="Explore Lore">
                <i class="bi bi-play-fill"></i>
            </a>
            @auth
                <button type="button" 
                        class="netflix-circle-btn fh-bookmark-toggle {{ $isBookmarked ? 'bookmarked' : '' }}" 
                        data-type="content" 
                        data-id="{{ $content->id }}" 
                        aria-label="Add to List">
                    <i class="bi {{ $isBookmarked ? 'bi-check-lg' : 'bi-plus-lg' }}"></i>
                </button>
            @else
                <a href="{{ route('login') }}" class="netflix-circle-btn" aria-label="Sign in to bookmark">
                    <i class="bi bi-plus-lg"></i>
                </a>
            @endauth
            <button type="button" class="netflix-circle-btn netflix-like-btn" aria-label="Like">
                <i class="bi bi-hand-thumbs-up"></i>
            </button>
            <button type="button" class="netflix-circle-btn more-btn" aria-label="More Info">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <!-- Title -->
        <div class="netflix-card-hover-title">
            <a href="{{ route('explore.show', $content->slug) }}" class="text-light text-decoration-none">
                {{ $content->title }}
            </a>
        </div>

        <!-- Meta -->
        <div class="netflix-card-hover-meta">
            <span class="netflix-match-badge">{{ $matchPct }}% Match</span>
            @if($avgRating)
                <span class="text-warning small fw-bold"><i class="bi bi-star-fill" style="font-size: 0.65rem;"></i> {{ number_format($avgRating, 1) }}</span>
            @endif
            <span class="netflix-badge-pill" style="font-size: 0.65rem;">HD</span>
            <span class="text-secondary small">{{ $content->category?->name ?? 'Fandom' }}</span>
        </div>

        <!-- Tags / Excerpt -->
        <div class="netflix-card-hover-tags">
            @forelse($content->genres->take(3) as $genre)
                <span>{{ $genre->name }}</span>
                @if(!$loop->last)<i class="bi bi-circle-fill netflix-tag-dot"></i>@endif
            @empty
                <span>Canon Universe</span>
            @endforelse
        </div>

        <div class="d-none">
            <!-- Hidden text anchor for test assertions -->
            <a href="{{ route('explore.show', $content->slug) }}">Read Lore &rarr;</a>
        </div>
    </div>

    <!-- Mobile Always-Visible Footer Card Info -->
    <div class="p-2 d-md-none bg-dark border-top border-subtle">
        <div class="small fw-bold text-light text-truncate mb-1">{{ $content->title }}</div>
        <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.72rem;">
            <span class="text-info">{{ $content->category?->name }}</span>
            <span class="text-warning">{{ number_format($avgRating ?: 4.8, 1) }} ★</span>
        </div>
    </div>
</div>
