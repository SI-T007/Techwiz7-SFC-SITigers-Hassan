@props([
    'media',
])

<div class="fh-card fh-card-interactive h-100 d-flex flex-column overflow-hidden position-relative transition-all">
    
    <a href="{{ route('multimedia.show', $media->id) }}" class="text-decoration-none position-relative d-block overflow-hidden" style="height: 190px; background: #0a0a0c;">
        <img src="{{ $media->thumbnail_url }}" 
             alt="{{ $media->title }}" 
             class="w-100 h-100 object-fit-cover transition-transform"
             loading="lazy"
             onerror="window.handleImageFallback ? window.handleImageFallback(this) : null">

       
        <div class="position-absolute top-0 start-0 p-2 d-flex flex-wrap gap-1 z-2">
            <span class="fh-badge fh-badge-primary">
                {{ $media->media_type_label }}
            </span>
            @if($media->quality)
                <span class="badge {{ $media->quality === '4K' ? 'bg-danger' : ($media->quality === 'Full HD' ? 'bg-primary' : 'bg-secondary bg-opacity-75') }} text-light" style="font-size: 0.65rem;">
                    {{ $media->quality }}
                </span>
            @endif
        </div>

        @if($media->duration)
            <div class="position-absolute top-0 end-0 p-2 z-2">
                <span class="badge bg-black bg-opacity-75 text-light small fw-bold" style="font-size: 0.7rem;">
                    <i class="bi bi-clock me-1"></i> {{ $media->duration }}
                </span>
            </div>
        @endif

        <!-- Centered Play Icon Button with Pulse on Hover -->
        <div class="position-absolute top-50 start-50 translate-middle text-white z-2 opacity-90 transition-all">
            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                 style="width: 48px; height: 48px; background: rgba(229, 9, 20, 0.9); box-shadow: 0 0 20px rgba(229, 9, 20, 0.6);">
                <i class="bi bi-play-fill fs-3 text-white" style="margin-left: 3px;"></i>
            </div>
        </div>

        <!-- Bottom Gradient Vignette -->
        <div class="position-absolute bottom-0 start-0 w-100 h-50" style="background: linear-gradient(180deg, transparent 0%, rgba(10, 10, 12, 0.95) 100%);"></div>

        <!-- YouTube Source Indicator -->
        <div class="position-absolute bottom-0 start-0 p-2 text-white small z-2 d-flex align-items-center gap-1">
            <i class="bi bi-youtube text-danger"></i>
            <span style="font-size: 0.7rem; letter-spacing: 0.05em;" class="text-uppercase fw-semibold">YouTube HD</span>
        </div>
    </a>

    <!-- Content Body -->
    <div class="p-3 d-flex flex-column flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small text-info fw-semibold" style="font-size: 0.75rem;">
                {{ $media->category?->name ?? 'Universal Realm' }}
            </span>
            @if($media->ratings_avg)
                <div class="small text-warning fw-bold d-flex align-items-center gap-1" style="font-size: 0.8rem;">
                    <i class="bi bi-star-fill"></i>
                    <span>{{ number_format($media->ratings_avg, 1) }}</span>
                    <span class="text-muted fw-normal" style="font-size: 0.7rem;">({{ $media->ratings_count }})</span>
                </div>
            @else
                <span class="small text-muted" style="font-size: 0.7rem;">New Video</span>
            @endif
        </div>

        <h3 class="h6 fw-bold text-light mb-2 line-clamp-2" style="font-size: 0.95rem; min-height: 2.5rem;">
            <a href="{{ route('multimedia.show', $media->id) }}" class="text-light text-decoration-none hover-primary">
                {{ $media->title }}
            </a>
        </h3>

        @if($media->description)
            <p class="small text-muted mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.8rem;">
                {{ $media->description }}
            </p>
        @endif

        <!-- Card Footer -->
        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-subtle mt-auto small text-muted">
            <span style="font-size: 0.75rem;"><i class="bi bi-eye me-1"></i> {{ number_format($media->views) }} views</span>
            <a href="{{ route('multimedia.show', $media->id) }}" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold" style="font-size: 0.75rem;">
                Watch Now <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>
