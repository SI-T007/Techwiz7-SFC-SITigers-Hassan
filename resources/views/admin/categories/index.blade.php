@extends('layouts.app')

@section('title', 'Category Management — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h2 fw-bold text-light mb-0">Fandom Categories</h1>
            </div>
            <p class="text-muted small mb-0">Manage core fandom universes, slugs, cover banners, and status flags</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary-gradient btn-sm">
                <i class="bi bi-plus-circle me-1"></i> New Category
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm text-light">
                <i class="bi bi-speedometer2 me-1"></i> Admin Console
            </a>
        </div>
    </div>

    <!-- Quick Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-4 col-md-4">
            <div class="fh-card p-3">
                <div class="small text-muted mb-1">Total Categories</div>
                <div class="h3 fw-bold text-light mb-0">{{ $metrics['total'] }}</div>
            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="fh-card p-3 border-success border-opacity-50">
                <div class="small text-success mb-1">Active Universes</div>
                <div class="h3 fw-bold text-success mb-0">{{ $metrics['active'] }}</div>
            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="fh-card p-3 border-secondary border-opacity-50">
                <div class="small text-secondary mb-1">Inactive / Draft</div>
                <div class="h3 fw-bold text-secondary mb-0">{{ $metrics['inactive'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-2 align-items-center">
            <div class="col-md-7">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-dark border-secondary text-light" 
                           placeholder="Search by category name, slug, or description..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="fh-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="table-dark text-muted small text-uppercase" style="letter-spacing: 0.05em;">
                    <tr>
                        <th style="width: 60px;">Order</th>
                        <th>Category</th>
                        <th>Slug</th>
                        <th>Contents</th>
                        <th>Characters</th>
                        <th>Events/Drops</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="text-muted fw-bold">{{ $category->sort_order }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-25 text-primary" style="width: 32px; height: 32px;">
                                        <i class="bi {{ $category->icon ?: 'bi-stars' }}"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('fandoms.show', $category->slug) }}" target="_blank" class="fw-bold text-light text-decoration-none">
                                            {{ $category->name }}
                                        </a>
                                        @if($category->description)
                                            <div class="small text-muted text-truncate" style="max-width: 260px;">{{ $category->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-25 text-light">{{ $category->contents_count }} items</span>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-25 text-info">{{ $category->characters_count }} heroes</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $category->events_count }} events &bull; {{ $category->releases_count }} drops</small>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.categories.toggle', $category) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="badge border-0 {{ $category->status === 'active' ? 'bg-success bg-opacity-25 text-success' : 'bg-secondary text-light' }}" 
                                            title="Click to toggle status" style="cursor: pointer;">
                                        <i class="bi {{ $category->status === 'active' ? 'bi-check-circle-fill' : 'bi-dash-circle' }} me-1"></i>
                                        {{ ucfirst($category->status) }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-info" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete" 
                                            data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-dark border-secondary text-start">
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title text-light"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete Category</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-light">
                                                <p>Are you sure you want to permanently delete the fandom category <strong>{{ $category->name }}</strong>?</p>
                                                <div class="alert alert-warning bg-warning bg-opacity-10 border-warning small mb-0">
                                                    <i class="bi bi-shield-exclamation me-1"></i>
                                                    Categories with linked content, characters, events, or media cannot be deleted and should be deactivated instead.
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
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
                                <i class="bi bi-folder-x fs-1 d-block mb-2 opacity-50"></i>
                                No fandom categories found matching the criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
