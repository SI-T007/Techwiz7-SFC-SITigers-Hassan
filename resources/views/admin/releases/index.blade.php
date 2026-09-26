@extends('layouts.app')

@section('title', 'Releases Radar CMS — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Releases Management</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-light mb-1">Fandom Release Calendar & Drops</h1>
        <p class="text-muted small mb-0">Manage upcoming video games, anime premieres, movies, comics, manga, and collector drops</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('releases.index') }}" target="_blank" class="btn btn-outline-info btn-sm">
            <i class="bi bi-box-arrow-up-right me-1"></i> Public Radar
        </a>
        <a href="{{ route('admin.releases.create') }}" class="btn btn-primary-gradient btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Add New Release
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="fh-card p-3">
            <div class="small text-muted mb-1">Total Scheduled Drops</div>
            <div class="h4 fw-bold text-light mb-0">{{ $counts['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="fh-card p-3 border-warning border-opacity-50">
            <div class="small text-warning mb-1">Upcoming Drops</div>
            <div class="h4 fw-bold text-warning mb-0">{{ $counts['upcoming'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="fh-card p-3 border-success border-opacity-50">
            <div class="small text-success mb-1">Available / Released</div>
            <div class="h4 fw-bold text-success mb-0">{{ $counts['released'] }}</div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="fh-card p-3 mb-4">
    <form method="GET" action="{{ route('admin.releases.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm bg-dark border-secondary text-light" 
                   placeholder="Search title, platform, publisher..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">All Formats</option>
                <option value="game" {{ request('type') === 'game' ? 'selected' : '' }}>Video Game</option>
                <option value="anime" {{ request('type') === 'anime' ? 'selected' : '' }}>Anime / Movie</option>
                <option value="movie" {{ request('type') === 'movie' ? 'selected' : '' }}>Theatrical Film</option>
                <option value="tv_series" {{ request('type') === 'tv_series' ? 'selected' : '' }}>TV / Streaming Series</option>
                <option value="comic" {{ request('type') === 'comic' ? 'selected' : '' }}>Comic Book</option>
                <option value="manga" {{ request('type') === 'manga' ? 'selected' : '' }}>Manga Volume</option>
                <option value="merchandise" {{ request('type') === 'merchandise' ? 'selected' : '' }}>Merchandise / Figure</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">All Fandoms</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
            <a href="{{ route('admin.releases.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- Releases Table -->
<div class="fh-card p-0 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
            <thead>
                <tr class="text-muted small border-secondary border-opacity-25">
                    <th style="width: 50px;">Cover</th>
                    <th>Title & Fandom</th>
                    <th>Format</th>
                    <th>Release Date</th>
                    <th>Platform / Medium</th>
                    <th>Tags</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($releases as $release)
                    <tr class="border-secondary border-opacity-10">
                        <td>
                            <div class="rounded overflow-hidden bg-dark" style="width: 44px; height: 44px;">
                                <img src="{{ $release->cover_image_url }}" alt="{{ $release->title }}" class="w-100 h-100 object-fit-cover">
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-light text-truncate" style="max-width: 250px;">
                                {{ $release->title }}
                            </div>
                            <div class="small text-info">
                                {{ $release->category?->name ?? 'General' }}
                            </div>
                        </td>
                        <td>
                            <span class="fh-badge fh-badge-primary small">
                                {{ strtoupper($release->type) }}
                            </span>
                        </td>
                        <td class="small text-light">
                            {{ $release->release_date->format('M d, Y') }}
                        </td>
                        <td class="small text-muted text-truncate" style="max-width: 150px;">
                            {{ $release->platform ?? 'All Platforms' }}
                        </td>
                        <td>
                            @if(!empty($release->tags))
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach(array_slice($release->tags, 0, 2) as $tag)
                                        <span class="badge bg-secondary bg-opacity-50 text-light" style="font-size: 0.65rem;">{{ $tag }}</span>
                                    @endforeach
                                    @if(count($release->tags) > 2)
                                        <span class="badge bg-secondary" style="font-size: 0.65rem;">+{{ count($release->tags) - 2 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>
                        <td>
                            @if($release->isReleased())
                                <span class="badge bg-success small">Released</span>
                            @else
                                <span class="badge bg-warning text-dark small">Upcoming</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('releases.show', $release->slug) }}" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2 small" title="View Public Page">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <a href="{{ route('admin.releases.edit', $release) }}" class="btn btn-sm btn-outline-primary py-0 px-2 small" title="Edit Release">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.releases.destroy', $release) }}" onsubmit="return confirm('Delete this release drop permanently?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 small" title="Delete Release">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            No releases currently match your search criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($releases->hasPages())
        <div class="p-3 border-top border-secondary border-opacity-25 d-flex justify-content-center">
            {{ $releases->links() }}
        </div>
    @endif
</div>
</div>
@endsection
