@extends('layouts.app')

@section('title', 'My Saved Bookmarks — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <span class="small text-warning fw-bold text-uppercase" style="letter-spacing: 0.08em;">Personal Collection</span>
            <h1 class="h2 fw-bold text-light mb-1">My Saved Bookmarks</h1>
            <p class="text-muted small mb-0">Manage your saved lore articles, character dossiers, upcoming drops, and personal reading notes</p>
        </div>
        <span class="badge bg-warning bg-opacity-25 text-warning px-3 py-2 fs-6 border border-warning border-opacity-25">
            {{ $bookmarks->total() }} Saved Items
        </span>
    </div>

    <!-- Filter Tabs & Search -->
    <div class="fh-card p-3 mb-4">
        <div class="row g-2 align-items-center justify-content-between">
            <!-- Type Tabs -->
            <div class="col-md-8 d-flex flex-wrap gap-2">
                <a href="{{ route('bookmarks.index') }}" 
                   class="btn btn-sm {{ !request('type') ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                    All Items
                </a>
                <a href="{{ route('bookmarks.index', ['type' => 'content']) }}" 
                   class="btn btn-sm {{ request('type') === 'content' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                    Lore & Articles
                </a>
                <a href="{{ route('bookmarks.index', ['type' => 'character']) }}" 
                   class="btn btn-sm {{ request('type') === 'character' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                    Characters
                </a>
                <a href="{{ route('bookmarks.index', ['type' => 'release']) }}" 
                   class="btn btn-sm {{ request('type') === 'release' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                    Releases
                </a>
                <a href="{{ route('bookmarks.index', ['type' => 'event']) }}" 
                   class="btn btn-sm {{ request('type') === 'event' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                    Events
                </a>
            </div>

            <!-- Search inside notes -->
            <div class="col-md-4">
                <form method="GET" action="{{ route('bookmarks.index') }}">
                    @if(request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search notes..." value="{{ request('search') }}">
                        <button type="submit" class="fh-btn-primary btn-sm">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bookmarks Grid -->
    <div class="row g-4">
        @forelse($bookmarks as $bm)
            @php
                $item = $bm->bookmarkable;
                $isContent = $item instanceof \App\Models\Content;
                $isCharacter = $item instanceof \App\Models\Character;
                $isRelease = $item instanceof \App\Models\Release;
                $isEvent = $item instanceof \App\Models\Event;

                $title = $item?->title ?? $item?->name ?? 'Saved Universe Item';
                $url = '#';
                $typeLabel = class_basename($bm->bookmarkable_type);

                if ($isContent) {
                    $url = route('explore.show', $item->slug);
                    $typeLabel = ucfirst($item->type);
                } elseif ($isCharacter) {
                    $url = route('characters.show', $item->slug);
                    $typeLabel = 'Character';
                } elseif ($isRelease) {
                    $url = route('releases.index');
                    $typeLabel = 'Release Drop';
                } elseif ($isEvent) {
                    $url = route('events.index');
                    $typeLabel = 'Event';
                }
            @endphp

            <div class="col-md-6 col-lg-4" id="bookmark-card-{{ $bm->id }}">
                <div class="fh-card h-100 d-flex flex-column p-4">
                    <!-- Top Bar -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fh-badge fh-badge-primary">
                            {{ $typeLabel }}
                        </span>
                        <form method="POST" action="{{ route('bookmarks.destroy', $bm->id) }}" onsubmit="return confirm('Remove this bookmark?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" title="Remove Bookmark">
                                <i class="bi bi-trash3"></i> Remove
                            </button>
                        </form>
                    </div>

                    <!-- Item Title -->
                    <h3 class="h6 fw-bold text-light mb-2">
                        @if($item)
                            <a href="{{ $url }}" class="text-light text-decoration-none">
                                {{ $title }}
                            </a>
                        @else
                            <span class="text-muted">(Item no longer available)</span>
                        @endif
                    </h3>

                    <!-- Category / Date -->
                    <div class="small text-muted mb-3">
                        @if($item && isset($item->category))
                            <span class="text-info fw-semibold">{{ $item->category?->name }}</span> &bull;
                        @endif
                        <span>Saved {{ $bm->created_at->diffForHumans() }}</span>
                    </div>

                    <!-- Personal Note Section -->
                    <div class="mt-auto pt-3 border-top border-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-warning">
                                <i class="bi bi-pencil-square me-1"></i> Personal Note
                            </span>
                            <button type="button" 
                                    class="btn btn-link p-0 text-info small text-decoration-none"
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#edit-note-{{ $bm->id }}">
                                {{ $bm->note ? 'Edit' : 'Add Note' }}
                            </button>
                        </div>

                        <!-- Current Note Preview -->
                        <div class="small text-secondary mb-2" id="note-preview-{{ $bm->id }}">
                            {{ $bm->note ?: 'No personal notes attached.' }}
                        </div>

                        <!-- Collapsible Note Edit Form -->
                        <div class="collapse" id="edit-note-{{ $bm->id }}">
                            <form method="POST" action="{{ route('bookmarks.note', $bm->id) }}" class="note-update-form" data-id="{{ $bm->id }}">
                                @csrf
                                <textarea name="note" class="form-control form-control-sm mb-2" rows="2" placeholder="Write thoughts, theories, or review notes...">{{ $bm->note }}</textarea>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="fh-btn-primary btn-sm py-1 px-3">Save</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-1" data-bs-toggle="collapse" data-bs-target="#edit-note-{{ $bm->id }}">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <x-empty-state 
                    icon="bi-bookmark-dash" 
                    title="No Bookmarks Found" 
                    message="You haven't saved any entries yet. Explore lore articles, character dossiers, and release drops to save your favorites."
                    actionText="Explore Content"
                    :actionUrl="route('explore')"
                />
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($bookmarks->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $bookmarks->links() }}
        </div>
    @endif
</div>
@endsection
