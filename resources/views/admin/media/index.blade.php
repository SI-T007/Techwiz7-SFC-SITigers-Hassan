@extends('layouts.app')

@section('title', 'Manage Multimedia Assets — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
            </div>
            <h1 class="h2 fw-bold text-light mb-1">Multimedia Assets Management</h1>
            <p class="text-muted small mb-0">Manage trailers, audio tracks, gameplay videos, and high-definition galleries</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.media.create') }}" class="fh-btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> New Media Asset
            </a>
            <a href="{{ route('multimedia.index') }}" target="_blank" class="btn btn-outline-info btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i> Public Media Center
            </a>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.media.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-subtle text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search title, YouTube ID..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="media_type" class="form-select form-select-sm">
                    <option value="">All Media Types</option>
                    @foreach(\App\Models\Media::MEDIA_TYPES as $key => $label)
                        <option value="{{ $key }}" {{ request('media_type') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="quality" class="form-select form-select-sm">
                    <option value="">All Qualities</option>
                    @foreach(\App\Models\Media::QUALITIES as $q)
                        <option value="{{ $q }}" {{ request('quality') === $q ? 'selected' : '' }}>
                            {{ $q }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Fandoms</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="fh-btn-primary btn-sm flex-fill">Filter</button>
                <a href="{{ route('admin.media.index') }}" class="fh-btn-ghost btn-sm" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>

    <!-- Media Data Table -->
    <div class="fh-card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-muted small border-secondary border-opacity-25">
                        <th style="width: 70px;">Preview</th>
                        <th>Title & Media Info</th>
                        <th>Type</th>
                        <th>Quality</th>
                        <th>Fandom</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mediaItems as $media)
                        <tr class="border-secondary border-opacity-10">
                            <td>
                                <div class="position-relative rounded overflow-hidden" style="width: 64px; height: 38px; background: #000;">
                                    <img src="{{ $media->thumbnail_url }}" alt="{{ $media->title }}" class="w-100 h-100 object-fit-cover" loading="lazy" onerror="window.handleImageFallback ? window.handleImageFallback(this) : null">
                                    @if($media->youtube_video_id)
                                        <div class="position-absolute bottom-0 end-0 p-0 text-danger" style="line-height: 1;">
                                            <i class="bi bi-youtube" style="font-size: 0.65rem;"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-light text-truncate" style="max-width: 260px;">
                                    <a href="{{ route('multimedia.show', $media->id) }}" target="_blank" class="text-light text-decoration-none hover-primary">
                                        {{ $media->title }}
                                    </a>
                                </div>
                                <div class="small text-muted d-flex align-items-center gap-2" style="font-size: 0.75rem;">
                                    @if($media->youtube_video_id)
                                        <span><code>{{ $media->youtube_video_id }}</code></span>
                                    @endif
                                    @if($media->duration)
                                        <span><i class="bi bi-stopwatch"></i> {{ $media->duration }}</span>
                                    @endif
                                    @if($media->is_featured)
                                        <span class="badge bg-warning bg-opacity-25 text-warning" style="font-size: 0.65rem;">⭐ Featured</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="fh-badge fh-badge-primary small" style="font-size: 0.7rem;">
                                    {{ $media->media_type_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $media->quality === '4K' ? 'bg-danger' : ($media->quality === 'Full HD' ? 'bg-primary' : 'bg-secondary bg-opacity-50') }}" style="font-size: 0.7rem;">
                                    {{ $media->quality }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-25 text-light small">
                                    {{ $media->category?->name ?? 'General' }}
                                </span>
                            </td>
                            <td>
                                @if($media->ratings_avg)
                                    <div class="small text-warning fw-bold">
                                        <i class="bi bi-star-fill"></i> {{ number_format($media->ratings_avg, 1) }}
                                        <span class="text-muted fw-normal">({{ $media->ratings_count }})</span>
                                    </div>
                                @else
                                    <span class="small text-muted">Unrated</span>
                                @endif
                            </td>
                            <td>
                                @if($media->status === 'published')
                                    <span class="badge bg-success bg-opacity-25 text-success small">Published</span>
                                @elseif($media->status === 'draft')
                                    <span class="badge bg-warning bg-opacity-25 text-warning small">Draft</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-muted small">Archived</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                <i class="bi bi-eye"></i> {{ number_format($media->views) }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('multimedia.show', $media->id) }}" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2 small" title="View Public Video">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <a href="{{ route('admin.media.edit', $media->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2 small" title="Edit Asset">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.media.destroy', $media->id) }}" onsubmit="return confirm('Delete this media asset?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 small" title="Delete Asset">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                No media records found. Click "New Media Asset" to add YouTube trailers, songs, or cinematics.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mediaItems->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $mediaItems->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
