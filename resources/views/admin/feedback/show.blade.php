@extends('layouts.app')

@section('title', 'Feedback Details: #' . $feedback->id . ' - Fan Hub Plus Admin')

@section('content')
<div class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.feedback.index') }}">Feedback Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">#{{ $feedback->id }}</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0 text-white">Feedback Ticket #{{ $feedback->id }}</h1>
    </div>
    <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Feedback List
    </a>
</div>

<div class="row g-4">
    <!-- Left: Ticket Information & Content -->
    <div class="col-lg-8">
        <div class="card bg-dark border-secondary shadow-sm mb-4">
            <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center py-3">
                <span class="badge bg-{{ $feedback->status === 'new' ? 'primary' : ($feedback->status === 'replied' ? 'success' : 'secondary') }} text-uppercase px-3 py-2">
                    <i class="bi bi-circle-fill me-1 small"></i> Status: {{ $feedback->status }}
                </span>
                <span class="text-secondary small">
                    <i class="bi bi-clock me-1"></i> Received {{ $feedback->created_at->format('M d, Y h:i A') }} ({{ $feedback->created_at->diffForHumans() }})
                </span>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <span class="badge bg-secondary text-uppercase me-2">{{ $feedback->type }}</span>
                    <h3 class="card-title text-white mt-2">{{ $feedback->subject }}</h3>
                </div>

                <div class="p-3 rounded bg-black bg-opacity-50 border border-secondary text-light mb-4" style="white-space: pre-wrap; font-size: 1.05rem; line-height: 1.6;">{{ $feedback->message }}</div>

                @if($feedback->admin_reply)
                    <div class="border border-success rounded p-4 bg-success bg-opacity-10 mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="text-success mb-0">
                                <i class="bi bi-reply-fill me-1"></i> Official Administrator Response
                            </h5>
                            <span class="text-secondary small">
                                Replied {{ $feedback->replied_at ? $feedback->replied_at->format('M d, Y h:i A') : 'Recently' }}
                                @if($feedback->repliedBy)
                                    by {{ $feedback->repliedBy->name }}
                                @endif
                            </span>
                        </div>
                        <div class="text-light" style="white-space: pre-wrap;">{{ $feedback->admin_reply }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Reply Form -->
        <div class="card bg-dark border-secondary shadow-sm">
            <div class="card-header bg-dark border-secondary py-3">
                <h5 class="card-title text-white mb-0">
                    <i class="bi bi-reply me-2 text-primary"></i>
                    {{ $feedback->admin_reply ? 'Update Administrator Response' : 'Reply to Feedback' }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.feedback.reply', $feedback) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="admin_reply" class="form-label text-light">Response Message <span class="text-danger">*</span></label>
                        <textarea class="form-control bg-black text-light border-secondary @error('admin_reply') is-invalid @enderror" 
                                  id="admin_reply" 
                                  name="admin_reply" 
                                  rows="5" 
                                  placeholder="Type your official administrative reply here..." 
                                  required>{{ old('admin_reply', $feedback->admin_reply) }}</textarea>
                        @error('admin_reply')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-secondary">
                            Submitting a response will automatically update the status of this ticket to <strong>Replied</strong>.
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-send me-1"></i> {{ $feedback->admin_reply ? 'Update Response' : 'Send & Save Response' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right: Metadata & Status Controls -->
    <div class="col-lg-4">
        <!-- User Info Card -->
        <div class="card bg-dark border-secondary shadow-sm mb-4">
            <div class="card-header bg-dark border-secondary py-3">
                <h5 class="card-title text-white mb-0">
                    <i class="bi bi-person-circle me-2 text-info"></i> Submitter Details
                </h5>
            </div>
            <div class="card-body p-3">
                <ul class="list-group list-group-flush bg-transparent">
                    <li class="list-group-item bg-transparent text-light border-secondary px-0">
                        <small class="text-secondary d-block">Sender Name</small>
                        <span class="fw-semibold">{{ $feedback->name }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-light border-secondary px-0">
                        <small class="text-secondary d-block">Email Address</small>
                        <a href="mailto:{{ $feedback->email }}" class="text-info text-decoration-none">
                            {{ $feedback->email }}
                        </a>
                    </li>
                    <li class="list-group-item bg-transparent text-light border-secondary px-0">
                        <small class="text-secondary d-block">Account Status</small>
                        @if($feedback->user)
                            <span class="badge bg-success">Registered Member</span>
                            <span class="text-secondary small ms-1">(ID: #{{ $feedback->user_id }})</span>
                        @else
                            <span class="badge bg-secondary">Guest Visitor</span>
                        @endif
                    </li>
                    <li class="list-group-item bg-transparent text-light border-secondary px-0">
                        <small class="text-secondary d-block">Category / Type</small>
                        <span class="text-uppercase fw-semibold">{{ $feedback->type }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Quick Status Update Card -->
        <div class="card bg-dark border-secondary shadow-sm">
            <div class="card-header bg-dark border-secondary py-3">
                <h5 class="card-title text-white mb-0">
                    <i class="bi bi-gear me-2 text-warning"></i> Quick Status Update
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.feedback.status', $feedback) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="status" class="form-label text-light">Ticket Status</label>
                        <select name="status" id="status" class="form-select bg-black text-light border-secondary">
                            <option value="new" {{ $feedback->status === 'new' ? 'selected' : '' }}>New</option>
                            <option value="read" {{ $feedback->status === 'read' ? 'selected' : '' }}>Read</option>
                            <option value="replied" {{ $feedback->status === 'replied' ? 'selected' : '' }}>Replied</option>
                            <option value="archived" {{ $feedback->status === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-warning w-100">
                        <i class="bi bi-check-circle me-1"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
