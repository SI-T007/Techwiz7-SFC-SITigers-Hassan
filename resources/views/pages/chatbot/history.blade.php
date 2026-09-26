@extends('layouts.app')

@section('title', 'Conversation History — Smart AI Assistant — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-light mb-1">
                <i class="bi bi-clock-history text-info me-2"></i> Chatbot Conversation History
            </h1>
            <p class="text-muted small mb-0">Review previous queries, recommendations, and platform guidance sessions</p>
        </div>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('chatbot.new') }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> New Chat Session
                </button>
            </form>
            <a href="{{ route('chatbot.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-chat-dots me-1"></i> Active Assistant
            </a>
        </div>
    </div>

    @if($conversations->isEmpty())
        <div class="fh-card p-5 text-center">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: rgba(59, 130, 246, 0.1); color: var(--fh-primary);">
                <i class="bi bi-chat-left-dots fs-2"></i>
            </div>
            <h2 class="h5 fw-bold text-light mb-2">No Past Conversations Found</h2>
            <p class="text-muted small mx-auto mb-4" style="max-width: 420px;">
                You haven't had any chat sessions yet. Ask the assistant about fandoms, upcoming conventions, or release drops!
            </p>
            <a href="{{ route('chatbot.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-stars me-1"></i> Start First Chat
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach($conversations as $c)
                <div class="col-md-6 col-lg-4">
                    <div class="fh-card p-3 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h3 class="h6 fw-bold text-light mb-0 text-truncate" style="max-width: 220px;" title="{{ $c->title }}">
                                    {{ $c->title }}
                                </h3>
                                <span class="badge bg-secondary bg-opacity-50 text-light small">
                                    {{ $c->messages_count }} {{ Str::plural('msg', $c->messages_count) }}
                                </span>
                            </div>
                            <p class="small text-muted mb-3 line-clamp-2">
                                {{ $c->latestMessage?->message ?? 'No messages yet.' }}
                            </p>
                        </div>
                        <div class="pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                            <span class="small text-secondary" style="font-size: 0.75rem;">
                                {{ $c->updated_at->format('M d, Y H:i') }}
                            </span>
                            <div class="d-flex gap-2">
                                <a href="{{ route('chatbot.index', ['c' => $c->id]) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-box-arrow-in-right"></i> Open
                                </a>
                                <form method="POST" action="{{ route('chatbot.destroy', $c) }}" onsubmit="return confirm('Delete this conversation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($conversations->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $conversations->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
