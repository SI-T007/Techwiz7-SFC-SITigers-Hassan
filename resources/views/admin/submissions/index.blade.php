@extends('layouts.app')

@section('title', 'Moderation Queue — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
   
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h2 fw-bold text-light mb-0">Fan Content Moderation Queue</h1>
            </div>
            <p class="text-muted small mb-0">Review, approve, or reject user-submitted fan art, cosplay photos, and writing</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm text-light">
                <i class="bi bi-speedometer2 me-1"></i> Admin Console
            </a>
            <a href="{{ route('community.index') }}" target="_blank" class="btn btn-outline-info btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i> Public Showcase
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.submissions.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="fh-card p-3 {{ $status === 'pending' ? 'border-warning border-opacity-75' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Pending Review</span>
                        <i class="bi bi-hourglass-split text-warning fs-5"></i>
                    </div>
                    <div class="h3 fw-bold text-light mb-0">{{ $counts['pending'] }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.submissions.index', ['status' => 'approved']) }}" class="text-decoration-none">
                <div class="fh-card p-3 {{ $status === 'approved' ? 'border-success border-opacity-75' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Approved</span>
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    </div>
                    <div class="h3 fw-bold text-light mb-0">{{ $counts['approved'] }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.submissions.index', ['status' => 'rejected']) }}" class="text-decoration-none">
                <div class="fh-card p-3 {{ $status === 'rejected' ? 'border-danger border-opacity-75' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Rejected</span>
                        <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                    </div>
                    <div class="h3 fw-bold text-light mb-0">{{ $counts['rejected'] }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.submissions.index', ['status' => 'all']) }}" class="text-decoration-none">
                <div class="fh-card p-3 {{ $status === 'all' ? 'border-info border-opacity-75' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">All Entries</span>
                        <i class="bi bi-collection text-info fs-5"></i>
                    </div>
                    <div class="h3 fw-bold text-light mb-0">{{ $counts['all'] }}</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.submissions.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-subtle text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by title or body..." value="{{ request('search') }}">
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
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Mediums</option>
                    <option value="fan_art" {{ request('type') === 'fan_art' ? 'selected' : '' }}>Fan Art</option>
                    <option value="cosplay" {{ request('type') === 'cosplay' ? 'selected' : '' }}>Cosplay</option>
                    <option value="fan_fiction" {{ request('type') === 'fan_fiction' ? 'selected' : '' }}>Fan Fiction</option>
                    <option value="fan_video" {{ request('type') === 'fan_video' ? 'selected' : '' }}>Fan Video</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="fh-btn-primary btn-sm flex-fill">Filter</button>
                <a href="{{ route('admin.submissions.index', ['status' => $status]) }}" class="fh-btn-ghost btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <!-- Moderation Table -->
    <div class="fh-card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-muted small border-secondary border-opacity-25">
                        <th style="width: 50px;">Media</th>
                        <th>Title</th>
                        <th>Submitter</th>
                        <th>Fandom</th>
                        <th>Medium</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $sub)
                        <tr class="border-secondary border-opacity-10">
                            <td>
                                @if($sub->featured_image)
                                    <img src="{{ asset('storage/' . $sub->featured_image) }}" alt="{{ $sub->title }}" class="rounded" width="44" height="44" style="object-fit: cover;">
                                @else
                                    <div class="rounded d-flex align-items-center justify-content-center bg-secondary bg-opacity-25" style="width: 44px; height: 44px;">
                                        <i class="bi bi-brush text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-light text-truncate" style="max-width: 250px;">
                                    <a href="{{ route('admin.submissions.show', $sub->id) }}" class="text-light text-decoration-none">
                                        {{ $sub->title }}
                                    </a>
                                </div>
                                @if($sub->external_url)
                                    <a href="{{ $sub->external_url }}" target="_blank" class="small text-info text-decoration-none" style="font-size: 0.75rem;">
                                        <i class="bi bi-link-45deg"></i> External Media
                                    </a>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $sub->user?->avatar_url }}" alt="{{ $sub->user?->name }}" class="avatar-img" width="24" height="24">
                                    <span class="small text-light">{{ $sub->user?->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-25 text-light small">
                                    {{ $sub->category?->name ?? 'General' }}
                                </span>
                            </td>
                            <td>
                                <span class="fh-badge fh-badge-primary small" style="font-size: 0.75rem;">
                                    {{ ucwords(str_replace('_', ' ', $sub->type)) }}
                                </span>
                            </td>
                            <td>
                                @if($sub->status === 'approved')
                                    <span class="badge bg-success bg-opacity-25 text-success small">Approved</span>
                                @elseif($sub->status === 'pending')
                                    <span class="badge bg-warning bg-opacity-25 text-warning small">Pending</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-25 text-danger small">Rejected</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                {{ $sub->created_at->format('M d, Y') }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.submissions.show', $sub->id) }}" class="btn btn-sm btn-outline-info py-0 px-2 small" title="Inspect Submission">
                                        <i class="bi bi-eye"></i> Review
                                    </a>

                                    @if($sub->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.submissions.approve', $sub->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success py-0 px-2 small" title="Approve Submission">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($sub->status !== 'rejected')
                                        <form method="POST" action="{{ route('admin.submissions.reject', $sub->id) }}" class="d-inline" onsubmit="return confirm('Reject this submission?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 small" title="Reject Submission">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No submissions found under current status filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
