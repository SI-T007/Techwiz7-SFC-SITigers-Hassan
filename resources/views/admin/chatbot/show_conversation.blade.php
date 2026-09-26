@extends('layouts.app')

@section('title', 'Conversation Transcript — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4" style="max-width: 860px;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h3 fw-bold text-light mb-0">{{ $conversation->title }}</h1>
            </div>
            <p class="text-muted small mb-0">
                Initiated by {{ $conversation->user ? $conversation->user->name : 'Guest Visitor' }} &bull; {{ $conversation->created_at->format('M d, Y H:i') }}
            </p>
        </div>
        <a href="{{ route('admin.chatbot.conversations.index') }}" class="btn btn-outline-secondary btn-sm text-light">
            <i class="bi bi-arrow-left me-1"></i> Back to Conversations
        </a>
    </div>

    <!-- Transcript Box -->
    <div class="fh-card p-4 d-flex flex-column gap-3">
        @forelse($conversation->messages as $msg)
            @if($msg->isFromUser())
                <!-- User Message -->
                <div class="d-flex justify-content-end">
                    <div class="p-3 rounded-3 bg-primary text-white" style="max-width: 80%;">
                        <div class="small fw-bold mb-1 opacity-75 d-flex justify-content-between">
                            <span>User</span>
                            <span>{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                        <div style="white-space: pre-wrap; line-height: 1.6;">{{ $msg->message }}</div>
                    </div>
                </div>
            @else
                <!-- Assistant Message -->
                <div class="d-flex justify-content-start">
                    <div class="p-3 rounded-3 bg-dark border border-secondary text-light" style="max-width: 80%;">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                            <div class="d-flex align-items-center gap-1">
                                <i class="bi bi-robot text-info small"></i>
                                <span class="small fw-bold text-info">Assistant</span>
                                @if($msg->source === 'database')
                                    <span class="badge bg-success bg-opacity-25 text-success" style="font-size: 0.65rem;">DB Verified</span>
                                @elseif($msg->source === 'knowledge_base')
                                    <span class="badge bg-info bg-opacity-25 text-info" style="font-size: 0.65rem;">FAQ Knowledge</span>
                                @elseif($msg->source === 'ai')
                                    <span class="badge bg-purple bg-opacity-25 text-light" style="font-size: 0.65rem;">AI Generated</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-muted" style="font-size: 0.65rem;">Fallback</span>
                                @endif
                            </div>
                            <span class="small text-muted" style="font-size: 0.7rem;">{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                        <div style="white-space: pre-wrap; line-height: 1.6;">{{ $msg->message }}</div>
                    </div>
                </div>
            @endif
        @empty
            <div class="text-center py-4 text-muted">
                No messages recorded in this conversation.
            </div>
        @endforelse
    </div>
</div>
@endsection
