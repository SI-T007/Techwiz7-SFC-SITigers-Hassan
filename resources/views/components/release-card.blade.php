@props([
    'release',
])

<div class="fh-card fh-card-interactive h-100 d-flex flex-column overflow-hidden transition-all">
    <!-- Image Header -->
    <div class="position-relative" style="height: 180px; background: #0a0a0c; overflow: hidden;">
        <img src="{{ $release->cover_image_url }}" 
             alt="{{ $release->title }}" 
             class="w-100 h-100 object-fit-cover"
             loading="lazy">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(10,10,12,0.2) 0%, rgba(10,10,12,0.9) 100%);"></div>

        <!-- Top Badges -->
        <div class="position-absolute top-0 start-0 p-3 d-flex flex-wrap gap-1">
            <span class="fh-badge fh-badge-primary">
                {{ strtoupper($release->type) }}
            </span>
            @if($release->category)
                <span class="badge bg-secondary bg-opacity-75 text-light" style="font-size: 0.7rem;">
                    {{ $release->category->name }}
                </span>
            @endif
        </div>

        <div class="position-absolute top-0 end-0 p-3">
            @if($release->isReleased())
                <span class="badge bg-success bg-opacity-90">
                    <i class="bi bi-check-circle me-1"></i> Available Now
                </span>
            @else
                <span class="badge bg-danger bg-opacity-90 text-white fw-bold">
                    <i class="bi bi-hourglass-split me-1"></i> 
                    @if($release->days_until_release === 0)
                        Drops Today!
                    @elseif($release->days_until_release === 1)
                        Drops Tomorrow!
                    @else
                        In {{ $release->days_until_release }} Days
                    @endif
                </span>
            @endif
        </div>

        <!-- Release Date on Image Bottom -->
        <div class="position-absolute bottom-0 start-0 p-3 text-truncate w-100">
            <span class="text-white small fw-semibold text-truncate d-block" style="text-shadow: 0 1px 4px rgba(0,0,0,0.8);">
                <i class="bi bi-calendar-event text-danger me-1"></i> {{ $release->release_date->format('F d, Y') }}
            </span>
        </div>
    </div>

    <!-- Card Body -->
    <div class="p-3 p-md-4 d-flex flex-column flex-grow-1">
        <h3 class="h6 fw-bold text-light mb-2">
            <a href="{{ route('releases.show', $release->slug) }}" class="text-light text-decoration-none hover-primary">
                {{ $release->title }}
            </a>
        </h3>

        <!-- Tags if available -->
        @if(!empty($release->tags))
            <div class="d-flex flex-wrap gap-1 mb-2">
                @foreach($release->tags as $tag)
                    <span class="badge bg-secondary bg-opacity-25 text-info" style="font-size: 0.7rem;">
                        #{{ $tag }}
                    </span>
                @endforeach
            </div>
        @endif

        <p class="small text-muted mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
            {{ Str::limit($release->description, 110) }}
        </p>

        <!-- Platform & Publisher Metadata -->
        <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 border-top border-subtle mt-auto small">
            <span class="text-muted">
                <i class="bi bi-cpu me-1"></i> {{ $release->platform ?? 'All Platforms' }}
            </span>
            <a href="{{ route('releases.show', $release->slug) }}" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold">
                Preview <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>
