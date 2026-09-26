@extends('layouts.app')

@section('title', 'User Feedback Supervision — Admin Console')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h2 fw-bold text-light mb-0">Platform Feedback Supervision</h1>
            </div>
            <p class="text-muted small mb-0">Review bug reports, feature suggestions, and community inquiries</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm text-light">
                <i class="bi bi-speedometer2 me-1"></i> Admin Console
            </a>
        </div>
    </div>

    <!-- Status KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.feedback.index', ['status' => 'new']) }}" class="text-decoration-none">
                <div class="fh-card p-3 {{ request('status') === 'new' ? 'border-warning border-opacity-75' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">New Unread</span>
                        <i class="bi bi-envelope-exclamation text-warning fs-5"></i>
                    </div>
                    <div class="h3 fw-bold text-light mb-0">{{ $counts['new'] }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.feedback.index', ['status' => 'read']) }}" class="text-decoration-none">
                <div class="fh-card p-3 {{ request('status') === 'read' ? 'border-info border-opacity-75' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Read</span>
                        <i class="bi bi-envelope-open text-info fs-5"></i>
                    </div>
                    <div class="h3 fw-bold text-light mb-0">{{ $counts['read'] }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.feedback.index', ['status' => 'replied']) }}" class="text-decoration-none">
                <div class="fh-card p-3 {{ request('status') === 'replied' ? 'border-success border-opacity-75' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Replied</span>
                        <i class="bi bi-reply-fill text-success fs-5"></i>
                    </div>
                    <div class="h3 fw-bold text-light mb-0">{{ $counts['replied'] }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.feedback.index') }}" class="text-decoration-none">
                <div class="fh-card p-3 {{ !request('status') ? 'border-primary border-opacity-75' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Total Received</span>
                        <i class="bi bi-chat-square-text text-primary fs-5"></i>
                    </div>
                    <div class="h3 fw-bold text-light mb-0">{{ $counts['total'] }}</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.feedback.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-subtle text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search subject, message, or sender..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="bug" {{ request('type') === 'bug' ? 'selected' : '' }}>Bug Report</option>
                    <option value="suggestion" {{ request('type') === 'suggestion' ? 'selected' : '' }}>Feature Suggestion</option>
                    <option value="query" {{ request('type') === 'query' ? 'selected' : '' }}>General Query</option>
                    <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                    <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="fh-btn-primary btn-sm flex-fill">Filter</button>
                <a href="{{ route('admin.feedback.index') }}" class="fh-btn-ghost btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <!-- Feedback Table -->
    <div class="fh-card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-muted small border-secondary border-opacity-25">
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Sender</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $fb)
                        <tr class="border-secondary border-opacity-10">
                            <td>
                                @if($fb->type === 'bug')
                                    <span class="badge bg-danger bg-opacity-25 text-danger small">
                                        <i class="bi bi-bug me-1"></i> Bug
                                    </span>
                                @elseif($fb->type === 'suggestion')
                                    <span class="badge bg-primary bg-opacity-25 text-primary small">
                                        <i class="bi bi-lightbulb me-1"></i> Idea
                                    </span>
                                @else
                                    <span class="badge bg-info bg-opacity-25 text-info small">
                                        <i class="bi bi-question-circle me-1"></i> Query
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-light text-truncate" style="max-width: 320px;">
                                    <a href="{{ route('admin.feedback.show', $fb->id) }}" class="text-light text-decoration-none">
                                        {{ $fb->subject }}
                                    </a>
                                </div>
                                <div class="small text-muted text-truncate" style="max-width: 320px; font-size: 0.75rem;">
                                    {{ Str::limit($fb->message, 80) }}
                                </div>
                            </td>
                            <td>
                                <div class="small fw-semibold text-light">{{ $fb->name ?: 'Visitor' }}</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">{{ $fb->email ?: ($fb->user?->email ?? 'No email') }}</div>
                            </td>
                            <td>
                                @if($fb->status === 'new')
                                    <span class="badge bg-warning bg-opacity-25 text-warning small">New</span>
                                @elseif($fb->status === 'read')
                                    <span class="badge bg-info bg-opacity-25 text-info small">Read</span>
                                @elseif($fb->status === 'replied')
                                    <span class="badge bg-success bg-opacity-25 text-success small">Replied</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-muted small">Archived</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                {{ $fb->created_at->format('M d, Y') }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.feedback.show', $fb->id) }}" class="btn btn-sm btn-outline-info py-0 px-2 small">
                                    <i class="bi bi-eye me-1"></i> Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No feedback entries found matching your query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($feedbacks->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
