@props([
    'category',
    'showCount' => true,
])

@php
    $realmBackdrops = [
        'anime' => 'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=600&q=80',
        'gaming' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=600&q=80',
        'movies' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=600&q=80',
        'tv-shows' => 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?auto=format&fit=crop&w=600&q=80',
        'comics' => 'https://images.unsplash.com/photo-1612036782180-6f0b6cd846fe?auto=format&fit=crop&w=600&q=80',
        'manga' => 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&w=600&q=80',
        'cosplay' => 'https://images.unsplash.com/photo-1563089145-599997674d42?auto=format&fit=crop&w=600&q=80',
        'k-pop' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=600&q=80',
    ];
    $bgImage = $realmBackdrops[$category->slug] ?? 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80';
@endphp

<a href="{{ route('fandoms.show', $category->slug) }}" 
   class="fh-fandom-tile position-relative d-block rounded overflow-hidden text-decoration-none shadow-sm h-100"
   style="min-height: 160px; background: #181818;">
    
    <!-- Background Artwork -->
    <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background-image: url('{{ $bgImage }}'); background-size: cover; background-position: center; transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);">
    </div>

    <!-- Dark Gradient Vignette Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background: linear-gradient(180deg, rgba(20,20,20,0.3) 0%, rgba(20,20,20,0.85) 65%, #141414 100%);">
    </div>

    <!-- Content Overlay -->
    <div class="position-relative p-3 p-md-4 d-flex flex-column justify-content-between h-100 z-2">
        <div class="d-flex align-items-center justify-content-between">
            <div class="fh-category-icon-box mb-0" style="width: 38px; height: 38px; font-size: 1.15rem; background: rgba(0,0,0,0.6); backdrop-filter: blur(6px); border-radius: 6px;">
                <i class="bi {{ $category->icon_class }} text-danger"></i>
            </div>
            @if($showCount && isset($category->contents_count))
                <span class="netflix-badge-pill" style="font-size: 0.7rem;">
                    {{ $category->contents_count }} Entries
                </span>
            @endif
        </div>

        <div class="mt-4">
            <h3 class="h5 fw-bold text-light mb-1">{{ $category->name }}</h3>
            <p class="small text-muted mb-0 text-truncate" style="font-size: 0.78rem;">
                {{ $category->description ?: 'Explore canon archives, media, and community lore.' }}
            </p>
            <!-- Slug hook for test suite -->
            <span class="d-none">{{ $category->slug }}</span>
        </div>
    </div>
</a>
