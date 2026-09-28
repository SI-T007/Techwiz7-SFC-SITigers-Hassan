@extends('layouts.app')

@section('title', 'Manage Universe Content — Admin Console')

@section('content')
<div class="container py-4">
    
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h2 fw-bold text-light mb-0">Universe Content Hub CMS</h1>
            </div>
            <p class="text-muted small mb-0">Manage lore articles, official media, canon entries, status workflows, and metadata</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.content.create') }}" class="fh-btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> New Content Entry
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm text-light">
                <i class="bi bi-speedometer2 me-1"></i> Admin Console
            </a>
        </div>
    </div>

   
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.content.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-subtle text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="Search by title, description..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
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
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="fh-btn-primary btn-sm flex-fill">Filter</button>
                <a href="{{ route('admin.content.index') }}" class="fh-btn-ghost btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <!-- Content Table -->
    <div class="fh-card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-muted small border-secondary border-opacity-25">
                        <th style="width: 50px;">Media</th>
                        <th>Title & Slug</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Views / Pop</th>
                        <th>Updated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contents as $c)
                        <tr class="border-secondary border-opacity-10">
                            <td>
                                @if($c->thumbnail_url)
                                    <img src="{{ $c->thumbnail_url }}" alt="{{ $c->title }}" class="rounded" width="44" height="44" style="object-fit: cover;" onerror="window.handleImageFallback ? window.handleImageFallback(this) : null">
                                @else
                                    <div class="rounded d-flex align-items-center justify-content-center bg-secondary bg-opacity-25" style="width: 44px; height: 44px;">
                                        <i class="bi {{ $c->category?->icon ?: 'bi-journal-text' }} text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-light text-truncate" style="max-width: 280px;">
                                    {{ $c->title }}
                                </div>
                                <div class="small text-muted font-monospace" style="font-size: 0.75rem;">
                                    /{{ $c->slug }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-25 text-light small">
                                    {{ $c->category?->name ?? 'None' }}
                                </span>
                            </td>
                            <td>
                                <span class="fh-badge fh-badge-primary small">
                                    {{ ucfirst($c->type) }}
                                </span>
                            </td>
                            <td>
                                @if($c->status === 'published')
                                    <span class="badge bg-success bg-opacity-25 text-success small">Published</span>
                                @elseif($c->status === 'draft')
                                    <span class="badge bg-warning bg-opacity-25 text-warning small">Draft</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-muted small">Archived</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                <div><i class="bi bi-eye"></i> {{ number_format($c->views) }}</div>
                                <div><i class="bi bi-fire text-danger"></i> {{ number_format($c->popularity) }}</div>
                            </td>
                            <td class="small text-muted">
                                {{ $c->updated_at->format('M d, Y') }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('explore.show', $c->slug) }}" class="btn btn-sm btn-outline-info py-0 px-2 small" target="_blank" title="View Public Page">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <a href="{{ route('admin.content.edit', $c->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2 small" title="Edit Content">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.content.destroy', $c->id) }}" onsubmit="return confirm('Archive/delete this content entry?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 small" title="Delete Content">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No content records found. Click "New Content Entry" to publish lore or guides.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contents->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $contents->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
