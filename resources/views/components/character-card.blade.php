@props([
    'character',
])

@php
    $avatarUrl = $character->avatar_url ?: ($character->profile_image ? asset('storage/' . $character->profile_image) : 'https://images.unsplash.com/photo-1563089145-599997674d42?auto=format&fit=crop&w=600&q=80');
@endphp

<div class="netflix-card w-100 h-100 d-flex flex-column"
     data-netflix-modal="true"
     data-title="{{ $character->name }}"
     data-synopsis="{{ Str::limit($character->biography ?: $character->description, 140) }}"
     data-image="{{ $avatarUrl }}"
     data-category="{{ $character->category?->name ?? 'Character' }}"
     data-match="Iconic Dossier"
     data-url="{{ route('characters.show', $character->slug) }}"
     data-id="{{ $character->id }}">
    
    <div class="netflix-card-thumb-wrap" style="padding-top: 65%;">
        <img src="{{ $avatarUrl }}" 
             alt="{{ $character->name }}" 
             class="netflix-card-thumb" 
             loading="lazy">
        <div class="netflix-card-teaser-fx"></div>
        <span class="netflix-card-badge" style="background-color: #06b6d4;">DOSSIER</span>
    </div>

    <div class="netflix-card-hover-panel">
        <div class="netflix-card-actions">
            <a href="{{ route('characters.show', $character->slug) }}" class="netflix-circle-btn play-btn" aria-label="View Profile">
                <i class="bi bi-person-fill"></i>
            </a>
            <button type="button" class="netflix-circle-btn fh-bookmark-toggle" data-type="character" data-id="{{ $character->id }}" aria-label="Bookmark">
                <i class="bi bi-plus-lg"></i>
            </button>
            <button type="button" class="netflix-circle-btn more-btn" aria-label="More Info">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="netflix-card-hover-title">
            <a href="{{ route('characters.show', $character->slug) }}" class="text-light text-decoration-none">
                {{ $character->name }}
            </a>
        </div>

        <div class="netflix-card-hover-meta">
            <span class="text-info fw-bold">{{ $character->role ?? 'Main Protagonist' }}</span>
            <span class="text-secondary small">{{ $character->category?->name ?? 'Realm' }}</span>
        </div>

        <div class="netflix-card-hover-tags">
            @if($character->abilities)
                <span>{{ Str::limit($character->abilities, 35) }}</span>
            @else
                <span>{{ $character->fandom_universe ?? 'Canon Lore' }}</span>
            @endif
        </div>

        <div class="d-none">
            <a href="{{ route('characters.show', $character->slug) }}">Lore Archive &rarr;</a>
        </div>
    </div>

    <!-- Mobile view -->
    <div class="p-2 d-md-none bg-dark border-top border-subtle">
        <div class="small fw-bold text-light text-truncate">{{ $character->name }}</div>
        <div class="text-info" style="font-size: 0.72rem;">{{ $character->role ?? 'Character' }}</div>
    </div>
</div>
