@extends('layouts.app')

@section('title', 'Watch History — Fan Hub Plus')

@push('styles')
<style>
    /* ── Page Hero ─────────────────────────────── */
    .wh-hero {
        background: linear-gradient(135deg,
            rgba(6,182,212,0.12) 0%,
            rgba(20,20,20,0) 60%);
        border: 1px solid rgba(6,182,212,0.15);
        border-radius: 1.25rem;
        padding: 2rem 2.25rem;
        margin-bottom: 2rem;
    }

    /* ── Stat Pills ─────────────────────────────── */
    .wh-stat {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        text-align: center;
        transition: border-color 0.2s;
    }
    .wh-stat:hover { border-color: rgba(6,182,212,0.3); }

    /* ── Filter Bar ─────────────────────────────── */
    .wh-filter-bar {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 0.875rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }

    .wh-filter-input {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 0.5rem;
        color: #e5e5e5;
        padding: 0.42rem 0.85rem;
        font-size: 0.875rem;
        transition: border-color 0.2s, background 0.2s;
        outline: none;
    }
    .wh-filter-input:focus {
        border-color: rgba(6,182,212,0.5);
        background: rgba(255,255,255,0.09);
        color: #fff;
    }

    .wh-period-btn {
        padding: 0.38rem 0.9rem;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 99px;
        border: 1px solid rgba(255,255,255,0.12);
        background: transparent;
        color: #94a3b8;
        cursor: pointer;
        transition: all 0.18s;
        text-decoration: none;
        display: inline-block;
    }
    .wh-period-btn:hover,
    .wh-period-btn.active {
        background: rgba(6,182,212,0.18);
        border-color: rgba(6,182,212,0.45);
        color: #06b6d4;
    }

    /* ── History Grid Cards ─────────────────────── */
    .wh-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .wh-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 0.875rem;
        overflow: hidden;
        transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
        position: relative;
        cursor: pointer;
    }
    .wh-card:hover {
        transform: translateY(-4px);
        border-color: rgba(6,182,212,0.3);
        box-shadow: 0 12px 32px -8px rgba(0,0,0,0.6);
    }

    .wh-card-img-wrap {
        position: relative;
        aspect-ratio: 16/9;
        overflow: hidden;
        background: #1a1a2e;
    }
    .wh-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }
    .wh-card:hover .wh-card-img {
        transform: scale(1.06);
    }

    /* Play overlay on hover */
    .wh-card-play-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: background 0.25s, opacity 0.25s;
    }
    .wh-card:hover .wh-card-play-overlay {
        background: rgba(0,0,0,0.45);
        opacity: 1;
    }
    .wh-card-play-overlay i {
        font-size: 2.5rem;
        color: #fff;
        filter: drop-shadow(0 2px 8px rgba(0,0,0,0.7));
    }

    .wh-card-body {
        padding: 0.75rem;
    }

    .wh-card-title {
        font-size: 0.82rem;
        font-weight: 600;
        color: #e5e5e5;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 0.4rem;
        text-decoration: none;
    }
    .wh-card-title:hover { color: #06b6d4; }

    .wh-card-meta {
        font-size: 0.72rem;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.4rem;
    }

    .wh-type-badge {
        font-size: 0.6rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 99px;
        background: rgba(6,182,212,0.15);
        color: #06b6d4;
        border: 1px solid rgba(6,182,212,0.3);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }

    /* Remove button */
    .wh-remove-btn {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(0,0,0,0.7);
        border: none;
        color: #94a3b8;
        font-size: 0.8rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s, color 0.2s, background 0.2s;
        z-index: 5;
        backdrop-filter: blur(4px);
    }
    .wh-card:hover .wh-remove-btn {
        opacity: 1;
    }
    .wh-remove-btn:hover {
        background: rgba(239,68,68,0.85);
        color: #fff;
    }

    /* Empty state */
    .wh-empty {
        text-align: center;
        padding: 5rem 2rem;
        border: 1px dashed rgba(255,255,255,0.1);
        border-radius: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-lg-5 py-4">

    {{-- ── Hero Header ── --}}
    <div class="wh-hero">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h3 fw-bold text-light mb-1">
                    <i class="bi bi-clock-history text-info me-2"></i>Watch History
                </h1>
                <p class="text-muted small mb-0">
                    Every piece of lore, trailer, and media you've explored — all in one place.
                </p>
            </div>
            @if($totalWatched > 0)
            <form method="POST" action="{{ route('watch-history.clear') }}"
                  onsubmit="return confirm('Clear your entire watch history? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="btn btn-sm"
                        style="background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.3);">
                    <i class="bi bi-trash3 me-1"></i> Clear All History
                </button>
            </form>
            @endif
        </div>

        {{-- Stats ─────────────────── --}}
        <div class="row g-3 mt-3">
            <div class="col-6 col-md-4">
                <div class="wh-stat">
                    <div class="h4 fw-bold text-info mb-0">{{ number_format($totalWatched) }}</div>
                    <div class="small text-muted">Total Viewed</div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="wh-stat">
                    <div class="h4 fw-bold text-success mb-0">{{ $watchedToday }}</div>
                    <div class="small text-muted">Watched Today</div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="wh-stat">
                    <div class="h4 fw-bold text-warning mb-0">{{ $watchedThisWeek }}</div>
                    <div class="small text-muted">This Week</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('watch-history.index') }}" class="wh-filter-bar" id="filterForm">

        {{-- Search --}}
        <div class="flex-grow-1" style="min-width: 180px; max-width: 320px;">
            <div class="position-relative">
                <i class="bi bi-search position-absolute text-muted"
                   style="left: 0.75rem; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
                <input type="text"
                       name="q"
                       value="{{ $search }}"
                       placeholder="Search your history..."
                       class="wh-filter-input w-100"
                       style="padding-left: 2.1rem;"
                       autocomplete="off">
            </div>
        </div>

        {{-- Period Pills --}}
        <div class="d-flex flex-wrap gap-1 align-items-center">
            <span class="text-muted small me-1">Period:</span>
            @foreach(['all' => 'All Time', 'today' => 'Today', 'week' => 'This Week', 'month' => 'This Month'] as $val => $label)
                <a href="{{ route('watch-history.index', array_merge(request()->except('period', 'page'), ['period' => $val])) }}"
                   class="wh-period-btn {{ $period === $val ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Type Filter --}}
        @if($availableTypes->isNotEmpty())
        <div>
            <select name="type" class="wh-filter-input" onchange="this.form.submit()">
                <option value="">All Types</option>
                @foreach($availableTypes as $t)
                    <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $t)) }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <button type="submit" class="btn btn-sm"
                style="background: rgba(6,182,212,0.15); color: #06b6d4; border: 1px solid rgba(6,182,212,0.3);">
            <i class="bi bi-funnel me-1"></i> Filter
        </button>
    </form>

    {{-- ── History Grid ── --}}
    @if($history->isEmpty())
        <div class="wh-empty">
            <i class="bi bi-film fs-1 text-muted d-block mb-3"></i>
            <h5 class="text-light">Your history is empty</h5>
            <p class="text-muted small mb-4">
                @if($search || $type || $period !== 'all')
                    No results match your current filters. Try adjusting them.
                @else
                    Start exploring content, trailers, and lore — everything you view will be tracked here automatically.
                @endif
            </p>
            @if($search || $type || $period !== 'all')
                <a href="{{ route('watch-history.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="bi bi-x me-1"></i> Clear Filters
                </a>
            @endif
            <a href="{{ route('explore') }}" class="btn btn-sm"
               style="background: rgba(6,182,212,0.18); color: #06b6d4; border: 1px solid rgba(6,182,212,0.35);">
                <i class="bi bi-compass me-1"></i> Explore Lore
            </a>
        </div>
    @else
        {{-- Item count --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="text-muted small">
                Showing <strong class="text-light">{{ $history->firstItem() }}</strong>–<strong class="text-light">{{ $history->lastItem() }}</strong>
                of <strong class="text-light">{{ $history->total() }}</strong> entries
            </span>
            @if($search || $type || $period !== 'all')
                <a href="{{ route('watch-history.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size: 0.78rem;">
                    <i class="bi bi-x me-1"></i> Clear Filters
                </a>
            @endif
        </div>

        <div class="wh-grid">
            @foreach($history as $entry)
            <div class="wh-card" id="wh-entry-{{ $entry->id }}">

                {{-- Remove Button --}}
                <form method="POST"
                      action="{{ route('watch-history.destroy', $entry) }}"
                      onsubmit="return removeEntry(event, {{ $entry->id }})">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="wh-remove-btn" title="Remove from history">
                        <i class="bi bi-x"></i>
                    </button>
                </form>

                {{-- Thumbnail --}}
                <a href="{{ $entry->url ?? '#' }}" class="d-block wh-card-img-wrap" title="{{ $entry->title }}">
                    <img src="{{ $entry->thumbnail ?? 'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=800&q=60' }}"
                         alt="{{ $entry->title }}"
                         class="wh-card-img"
                         loading="lazy"
                         onerror="window.handleImageFallback ? window.handleImageFallback(this) : null">
                    <div class="wh-card-play-overlay">
                        <i class="bi bi-play-circle-fill"></i>
                    </div>
                </a>

                {{-- Body --}}
                <div class="wh-card-body">
                    <a href="{{ $entry->url ?? '#' }}" class="wh-card-title d-block">
                        {{ $entry->title }}
                    </a>
                    <div class="wh-card-meta">
                        <span class="wh-type-badge">{{ $entry->type_label }}</span>
                        <span title="{{ $entry->watched_at->format('D, d M Y H:i') }}">
                            {{ $entry->watched_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($history->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $history->links() }}
        </div>
        @endif
    @endif

</div>
@endsection

@push('scripts')
<script>
function removeEntry(event, id) {
    event.preventDefault();
    const card = document.getElementById('wh-entry-' + id);
    if (!card) return;

    // Animate out
    card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    card.style.opacity = '0';
    card.style.transform = 'scale(0.9)';

    setTimeout(() => {
        // Submit the form after animation
        event.target.closest('form').submit();
    }, 280);
}
</script>
@endpush
