@extends('layouts.app')

@section('title', 'Chatbot Conversations & Telemetry — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h2 fw-bold text-light mb-0">Chatbot Conversations & Volume</h1>
            </div>
            <p class="text-muted small mb-0">Inspect user and guest conversational interactions, questions, and telemetry</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.chatbot.knowledge.index') }}" class="btn btn-outline-info btn-sm">
                <i class="bi bi-book me-1"></i> Knowledge Base CMS
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm text-light">
                <i class="bi bi-speedometer2 me-1"></i> Admin Console
            </a>
        </div>
    </div>

    <!-- Quick Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="fh-card p-3">
                <div class="small text-muted mb-1">Total Threads</div>
                <div class="h3 fw-bold text-light mb-0">{{ $metrics['total_conversations'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fh-card p-3 border-info border-opacity-50">
                <div class="small text-info mb-1">Total Messages Exchanged</div>
                <div class="h3 fw-bold text-info mb-0">{{ $metrics['total_messages'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fh-card p-3 border-primary border-opacity-50">
                <div class="small text-primary mb-1">Registered Users</div>
                <div class="h3 fw-bold text-primary mb-0">{{ $metrics['registered_conversations'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fh-card p-3 border-secondary border-opacity-50">
                <div class="small text-secondary mb-1">Guest Visitors</div>
                <div class="h3 fw-bold text-secondary mb-0">{{ $metrics['guest_conversations'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.chatbot.conversations.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-dark border-secondary text-light" 
                           placeholder="Search conversation title, user name, or email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Types (User & Guest)</option>
                    <option value="registered" {{ request('type') === 'registered' ? 'selected' : '' }}>Registered Users Only</option>
                    <option value="guest" {{ request('type') === 'guest' ? 'selected' : '' }}>Guest Visitors Only</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                @if(request()->hasAny(['search', 'type']))
                    <a href="{{ route('admin.chatbot.conversations.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Conversations Table -->
    <div class="fh-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="table-dark text-muted small text-uppercase" style="letter-spacing: 0.05em;">
                    <tr>
                        <th>Conversation Title</th>
                        <th>User Identity</th>
                        <th>Messages</th>
                        <th>Last Active</th>
                        <th>Created</th>
                        <th class="text-end" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($conversations as $conv)
                        <tr>
                            <td>
                                <a href="{{ route('admin.chatbot.conversations.show', $conv) }}" class="fw-bold text-light text-decoration-none d-block text-truncate" style="max-width: 320px;">
                                    <i class="bi bi-chat-dots-fill text-info me-1"></i>
                                    {{ $conv->title }}
                                </a>
                                @if($conv->latestMessage)
                                    <small class="text-muted text-truncate d-block" style="max-width: 320px;">
                                        {{ $conv->latestMessage->isFromUser() ? 'User: ' : 'Bot: ' }} {{ $conv->latestMessage->message }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($conv->user)
                                    <span class="badge bg-primary bg-opacity-25 text-primary">
                                        <i class="bi bi-person-fill me-1"></i> {{ $conv->user->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-muted">
                                        <i class="bi bi-incognito me-1"></i> Guest Session
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-25 text-light">{{ $conv->messages_count }} messages</span>
                            </td>
                            <td><small class="text-light">{{ $conv->updated_at->diffForHumans() }}</small></td>
                            <td><small class="text-muted">{{ $conv->created_at->format('M d, Y') }}</small></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.chatbot.conversations.show', $conv) }}" class="btn btn-outline-info" title="Inspect Transcript">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete Conversation" 
                                            data-bs-toggle="modal" data-bs-target="#deleteConvModal{{ $conv->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteConvModal{{ $conv->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-dark border-secondary text-start">
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title text-light"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete Conversation</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-light">
                                                Are you sure you want to permanently delete conversation <strong>{{ $conv->title }}</strong> and all associated message records?
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <form method="POST" action="{{ route('admin.chatbot.conversations.destroy', $conv) }}">
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
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-square-dots fs-1 d-block mb-2 opacity-50"></i>
                                No chatbot conversations found matching the criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($conversations->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $conversations->links() }}
        </div>
    @endif
</div>
@endsection
