@props([
    'event',
])

<div class="fh-card fh-card-interactive h-100 d-flex flex-column overflow-hidden transition-all">
   
    <div class="position-relative" style="height: 180px; background: #0a0a0c; overflow: hidden;">
        <img src="{{ $event->cover_image_url }}" 
             alt="{{ $event->title }}" 
             class="w-100 h-100 object-fit-cover"
             loading="lazy"
             onerror="window.handleImageFallback ? window.handleImageFallback(this) : null">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(10,10,12,0.2) 0%, rgba(10,10,12,0.9) 100%);"></div>

        <div class="position-absolute top-0 start-0 p-3 d-flex flex-wrap gap-1">
            <span class="fh-badge fh-badge-purple">
                <i class="bi bi-tag-fill me-1"></i> {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
            </span>
            @if($event->category)
                <span class="badge bg-secondary bg-opacity-75 text-light" style="font-size: 0.7rem;">
                    {{ $event->category->name }}
                </span>
            @endif
        </div>

        <div class="position-absolute top-0 end-0 p-3">
            @if($event->isCancelled())
                <span class="badge bg-danger">
                    <i class="bi bi-x-circle me-1"></i> Cancelled
                </span>
            @elseif($event->isOngoing())
                <span class="badge bg-danger pulse-dot">
                    <i class="bi bi-broadcast me-1"></i> Live Now
                </span>
            @elseif($event->isCompleted())
                <span class="badge bg-secondary">
                    <i class="bi bi-check2-circle me-1"></i> Completed
                </span>
            @else
                <span class="badge bg-primary bg-opacity-75 text-light">
                    <i class="bi bi-calendar3 me-1"></i> Upcoming
                </span>
            @endif
        </div>

        <!-- Location Badge on Image Bottom -->
        <div class="position-absolute bottom-0 start-0 p-3 text-truncate w-100">
            <span class="text-white small fw-semibold text-truncate d-block" style="text-shadow: 0 1px 4px rgba(0,0,0,0.8);">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $event->location_display }}
            </span>
        </div>
    </div>

    <!-- Card Body -->
    <div class="p-3 p-md-4 d-flex flex-column flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-1 text-muted small">
            <span class="text-info fw-semibold">
                <i class="bi bi-clock me-1"></i> {{ $event->starts_at->format('M d, Y &bull; H:i') }}
            </span>
            @if(isset($event->distance))
                <span class="badge bg-info bg-opacity-25 text-info">
                    <i class="bi bi-compass me-1"></i> {{ round($event->distance, 1) }} km away
                </span>
            @endif
        </div>

        <h3 class="h6 fw-bold text-light mb-2">
            <a href="{{ route('events.show', $event->slug) }}" class="text-light text-decoration-none hover-primary">
                {{ $event->title }}
            </a>
        </h3>

        <p class="small text-muted mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
            {{ Str::limit($event->description, 110) }}
        </p>

        <!-- Footer Actions -->
        <div class="pt-3 border-top border-subtle mt-auto d-flex justify-content-between align-items-center">
            <a href="{{ route('events.show', $event->slug) }}" class="btn btn-sm btn-outline-danger py-1 px-2 fw-semibold">
                Details & Map <i class="bi bi-arrow-right ms-1"></i>
            </a>

            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('events.ics', $event) }}" class="btn btn-sm btn-link text-secondary p-0 text-decoration-none" title="Download .ics Calendar File">
                    <i class="bi bi-calendar-plus fs-6"></i>
                </a>
                @if($event->ticket_url)
                    <a href="{{ $event->ticket_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success py-1 px-2" title="Official Tickets / Registration">
                        <i class="bi bi-ticket-perforated me-1"></i> Tickets
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
