@extends('layouts.app')

@section('title', 'Character Management — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h2 fw-bold text-light mb-0">Character Lore Directory</h1>
            </div>
            <p class="text-muted small mb-0">Manage character database profiles, franchises, abilities, and lore spotlights</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.characters.create') }}" class="btn btn-primary-gradient btn-sm">
                <i class="bi bi-plus-circle me-1"></i> New Character
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
                <div class="small text-muted mb-1">Total Profiles</div>
                <div class="h3 fw-bold text-light mb-0">{{ $metrics['total'] }}</div>
            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="fh-card p-3 border-success border-opacity-50">
                <div class="small text-success mb-1">Active Characters</div>
                <div class="h3 fw-bold text-success mb-0">{{ $metrics['active'] }}</div>
            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="fh-card p-3 border-warning border-opacity-50">
                <div class="small text-warning mb-1">Featured Spotlights</div>
                <div class="h3 fw-bold text-warning mb-0">{{ $metrics['featured'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.characters.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-dark border-secondary text-light" 
                           placeholder="Search character name, franchise, biography..." value="{{ request('search') }}">
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
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                @if(request()->hasAny(['search', 'category_id', 'status']))
                    <a href="{{ route('admin.characters.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Characters Table -->
    <div class="fh-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="table-dark text-muted small text-uppercase" style="letter-spacing: 0.05em;">
                    <tr>
                        <th style="width: 60px;">Avatar</th>
                        <th>Name & Franchise</th>
                        <th>Fandom Category</th>
                        <th>Abilities</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($characters as $character)
                        <tr>
                            <td>
                                @if($character->profile_image)
                                    <img src="{{ filter_var($character->profile_image, FILTER_VALIDATE_URL) ? $character->profile_image : asset('storage/' . $character->profile_image) }}" 
                                         alt="{{ $character->name }}" class="rounded-circle object-fit-cover border border-secondary" style="width: 38px; height: 38px;">
                                @else
                                    <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center text-muted" style="width: 38px; height: 38px;">
                                        <i class="bi bi-person fs-5"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('characters.show', $character->slug) }}" target="_blank" class="fw-bold text-light text-decoration-none d-block">
                                    {{ $character->name }}
                                </a>
                                <small class="text-info">{{ $character->franchise }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-25 text-light">
                                    <i class="bi {{ $character->category->icon ?? 'bi-stars' }} me-1"></i>
                                    {{ $character->category->name ?? 'Unassigned' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-block" style="max-width: 220px;">
                                    {{ $character->abilities ?: 'None listed' }}
                                </small>
                            </td>
                            <td>
                                @if($character->is_featured)
                                    <span class="badge bg-warning bg-opacity-25 text-warning"><i class="bi bi-star-fill me-1"></i> Featured</span>
                                @else
                                    <span class="text-muted small">Standard</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $character->status === 'active' ? 'bg-success bg-opacity-25 text-success' : 'bg-secondary text-light' }}">
                                    {{ ucfirst($character->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.characters.edit', $character) }}" class="btn btn-outline-info" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete" 
                                            data-bs-toggle="modal" data-bs-target="#deleteCharModal{{ $character->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteCharModal{{ $character->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-dark border-secondary text-start">
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title text-light"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete Character</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-light">
                                                Are you sure you want to permanently delete character profile <strong>{{ $character->name }}</strong>?
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <form method="POST" action="{{ route('admin.characters.destroy', $character) }}">
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
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                                No character profiles found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($characters->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $characters->links() }}
        </div>
    @endif
</div>
@endsection
