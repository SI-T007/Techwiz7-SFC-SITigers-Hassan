@extends('layouts.app')

@section('title', 'Lore Articles Management — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h2 fw-bold text-light mb-0">Lore Articles & Deep Dives</h1>
            </div>
            <p class="text-muted small mb-0">Manage featured canon lore, franchise encyclopedias, and editorial deep-dives</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.articles.create') }}" class="btn btn-primary-gradient btn-sm">
                <i class="bi bi-plus-circle me-1"></i> New Lore Article
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm text-light">
                <i class="bi bi-speedometer2 me-1"></i> Admin Console
            </a>
        </div>
    </div>

    <!-- Quick Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-3 col-md-3">
            <div class="fh-card p-3">
                <div class="small text-muted mb-1">Total Articles</div>
                <div class="h3 fw-bold text-light mb-0">{{ $metrics['total'] }}</div>
            </div>
        </div>
        <div class="col-3 col-md-3">
            <div class="fh-card p-3 border-success border-opacity-50">
                <div class="small text-success mb-1">Published</div>
                <div class="h3 fw-bold text-success mb-0">{{ $metrics['published'] }}</div>
            </div>
        </div>
        <div class="col-3 col-md-3">
            <div class="fh-card p-3 border-warning border-opacity-50">
                <div class="small text-warning mb-1">Featured</div>
                <div class="h3 fw-bold text-warning mb-0">{{ $metrics['featured'] }}</div>
            </div>
        </div>
        <div class="col-3 col-md-3">
            <div class="fh-card p-3 border-secondary border-opacity-50">
                <div class="small text-secondary mb-1">Drafts</div>
                <div class="h3 fw-bold text-secondary mb-0">{{ $metrics['draft'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.articles.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-dark border-secondary text-light" 
                           placeholder="Search article title, synopsis, or body..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Fandoms</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                @if(request()->hasAny(['search', 'category_id', 'status', 'featured']))
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Articles Table -->
    <div class="fh-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="table-dark text-muted small text-uppercase" style="letter-spacing: 0.05em;">
                    <tr>
                        <th style="width: 50px;">Thumb</th>
                        <th>Article Title</th>
                        <th>Fandom</th>
                        <th>Author / Curator</th>
                        <th>Views</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td>
                                @if($article->thumbnail)
                                    <img src="{{ filter_var($article->thumbnail, FILTER_VALIDATE_URL) ? $article->thumbnail : asset('storage/' . $article->thumbnail) }}" 
                                         alt="{{ $article->title }}" class="rounded object-fit-cover border border-secondary" style="width: 42px; height: 42px;">
                                @else
                                    <div class="rounded bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center text-muted" style="width: 42px; height: 42px;">
                                        <i class="bi bi-file-text"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('explore.show', $article->slug) }}" target="_blank" class="fw-bold text-light text-decoration-none d-block text-truncate" style="max-width: 320px;">
                                    {{ $article->title }}
                                </a>
                                <small class="text-muted">{{ $article->published_at ? $article->published_at->format('M d, Y') : 'Draft' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-25 text-light">
                                    <i class="bi {{ $article->category->icon ?? 'bi-stars' }} me-1"></i>
                                    {{ $article->category->name ?? 'Unassigned' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-light">{{ $article->user->name ?? 'Platform Editorial' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-25 text-muted">{{ number_format($article->views) }}</span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.articles.toggle-featured', $article) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="badge border-0 {{ $article->is_featured ? 'bg-warning bg-opacity-25 text-warning' : 'bg-secondary bg-opacity-25 text-muted' }}" 
                                            title="Click to toggle featured spotlight" style="cursor: pointer;">
                                        <i class="bi {{ $article->is_featured ? 'bi-star-fill' : 'bi-star' }} me-1"></i>
                                        {{ $article->is_featured ? 'Featured' : 'Standard' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <span class="badge {{ $article->status === 'published' ? 'bg-success bg-opacity-25 text-success' : ($article->status === 'draft' ? 'bg-warning bg-opacity-25 text-warning' : 'bg-secondary text-light') }}">
                                    {{ ucfirst($article->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-outline-info" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete" 
                                            data-bs-toggle="modal" data-bs-target="#deleteArticleModal{{ $article->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteArticleModal{{ $article->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-dark border-secondary text-start">
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title text-light"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete Article</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-light">
                                                Are you sure you want to permanently delete article <strong>{{ $article->title }}</strong>?
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <form method="POST" action="{{ route('admin.articles.destroy', $article) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Confirm Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-50"></i>
                                No lore articles found matching the criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($articles->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $articles->links() }}
        </div>
    @endif
</div>
@endsection
