@extends('layouts.app')

@section('title', 'Smart AI Fan Assistant — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="row g-4">
        <!-- Sidebar: Assistant Identity, Prompts & History -->
        <div class="col-lg-4 col-xl-3">
            <div class="fh-card p-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--fh-primary), var(--fh-accent-cyan)); color: #fff;">
                        <i class="bi bi-robot fs-4"></i>
                    </div>
                    <div>
                        <h2 class="h6 fw-bold text-light mb-0">Fan Assistant</h2>
                        <span class="badge bg-success bg-opacity-25 text-success small">
                            <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Knowledge Base Active
                        </span>
                    </div>
                </div>
                <p class="small text-muted mb-3">
                    Your intelligent guide to lore, upcoming events, release drops, characters, and platform guidance across the universe.
                </p>

                <!-- New Conversation Button -->
                <form method="POST" action="{{ route('chatbot.new') }}" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100 btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> New Conversation
                    </button>
                </form>

                @auth
                    <a href="{{ route('chatbot.history') }}" class="btn btn-outline-secondary w-100 btn-sm">
                        <i class="bi bi-clock-history me-1"></i> View All History
                    </a>
                @endauth
            </div>

            <!-- Suggested Quick Prompts -->
            <div class="fh-card p-4 mb-4">
                <h3 class="h6 fw-bold text-light mb-3 text-uppercase" style="letter-spacing: 0.05em;">
                    <i class="bi bi-lightbulb text-warning me-1"></i> Suggested Queries
                </h3>
                <div class="d-flex flex-column gap-2">
                    @foreach($suggestedQuestions as $prompt)
                        <button type="button" 
                                class="btn btn-sm btn-outline-custom text-start text-truncate py-2 px-3 small suggested-prompt-btn"
                                onclick="fillPrompt('{{ addslashes($prompt) }}')">
                            <i class="bi bi-chat-left-text text-info me-1"></i> {{ $prompt }}
                        </button>
                    @endforeach
                </div>
            </div>

            @auth
                <!-- Recent Conversations -->
                @if($recentConversations->isNotEmpty())
                    <div class="fh-card p-4">
                        <h3 class="h6 fw-bold text-light mb-3 text-uppercase" style="letter-spacing: 0.05em;">
                            <i class="bi bi-clock-history text-secondary me-1"></i> Recent Chats
                        </h3>
                        <div class="list-group list-group-flush bg-transparent">
                            @foreach($recentConversations as $c)
                                <a href="{{ route('chatbot.index', ['c' => $c->id]) }}" 
                                   class="list-group-item list-group-item-action bg-transparent text-light border-secondary px-0 py-2 d-flex justify-content-between align-items-center {{ $conversation->id === $c->id ? 'fw-bold text-primary' : 'text-muted' }}">
                                    <span class="text-truncate" style="max-width: 170px;">{{ $c->title }}</span>
                                    <small class="text-secondary">{{ $c->updated_at->diffForHumans(null, true) }}</small>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Main Chat Area -->
        <div class="col-lg-8 col-xl-9">
            <div class="fh-card d-flex flex-column" style="height: 650px;">
                <!-- Chat Window Header -->
                <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center bg-dark bg-opacity-50">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-chat-dots-fill text-primary fs-5"></i>
                        <div>
                            <span class="fw-bold text-light d-block" id="convTitleHeader">{{ $conversation->title }}</span>
                            <small class="text-muted" style="font-size: 0.75rem;">Connected to Fan Hub Plus Engine</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        @if($conversation->messages->isNotEmpty())
                            <form method="POST" action="{{ route('chatbot.destroy', $conversation) }}" onsubmit="return confirm('Clear and delete this conversation?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Clear Conversation">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Messages Stream Area -->
                <div class="p-3 p-md-4 flex-grow-1 overflow-y-auto d-flex flex-column gap-3" id="messagesContainer" style="scroll-behavior: smooth;">
                    <!-- Initial Welcome Greeting if empty -->
                    @if($conversation->messages->isEmpty())
                        <div class="text-center my-auto py-4">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background: rgba(59, 130, 246, 0.15); color: var(--fh-primary);">
                                <i class="bi bi-stars fs-2"></i>
                            </div>
                            <h2 class="h5 fw-bold text-light mb-2">Welcome to Fan Hub Plus Assistant</h2>
                            <p class="text-muted small mx-auto" style="max-width: 460px;">
                                Ask me anything about Anime, Gaming, Movies, Comics, upcoming conventions, release drops, character lore, or how to navigate the platform!
                            </p>
                            <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="fillPrompt('What fandoms are available?')">
                                    What fandoms are available?
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="fillPrompt('What upcoming events are on the calendar?')">
                                    Upcoming Events
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="fillPrompt('How do I bookmark content?')">
                                    How to Bookmark
                                </button>
                            </div>
                        </div>
                    @else
                        @foreach($conversation->messages as $msg)
                            <div class="d-flex {{ $msg->isFromUser() ? 'justify-content-end' : 'justify-content-start' }} message-item">
                                <div class="p-3 rounded-3 {{ $msg->isFromUser() ? 'bg-primary text-white' : 'bg-dark border border-secondary text-light' }}" style="max-width: 82%;">
                                    @if($msg->isFromAssistant())
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-robot text-info small"></i>
                                            <span class="small fw-semibold text-info">Assistant</span>
                                            @if($msg->source === 'database')
                                                <span class="badge bg-success bg-opacity-25 text-success" style="font-size: 0.65rem;">Verified DB</span>
                                            @elseif($msg->source === 'knowledge_base')
                                                <span class="badge bg-info bg-opacity-25 text-info" style="font-size: 0.65rem;">FAQ Knowledge</span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="message-body" style="white-space: pre-wrap; line-height: 1.6;">{!! nl2br(e($msg->message)) !!}</div>
                                    <div class="text-end mt-1" style="font-size: 0.7rem; opacity: 0.7;">
                                        {{ $msg->created_at->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <!-- Dynamic Typing Indicator -->
                    <div class="d-none justify-content-start" id="typingIndicator">
                        <div class="p-3 rounded-3 bg-dark border border-secondary text-muted d-flex align-items-center gap-2">
                            <i class="bi bi-robot text-info"></i>
                            <span class="small">Thinking...</span>
                            <div class="spinner-grow spinner-grow-sm text-info" role="status" style="width: 0.6rem; height: 0.6rem;"></div>
                        </div>
                    </div>
                </div>

                <!-- Chat Input Form -->
                <div class="p-3 border-top border-secondary border-opacity-25 bg-dark bg-opacity-50">
                    <form id="chatForm" onsubmit="handleChatSubmit(event);">
                        <input type="hidden" id="conversationId" value="{{ $conversation->id }}">
                        <div class="input-group">
                            <input type="text" 
                                   id="messageInput" 
                                   class="form-control bg-dark border-secondary text-light px-3 py-2" 
                                   placeholder="Ask anything about fandoms, lore, events, or releases..." 
                                   autocomplete="off"
                                   required>
                            <button class="btn btn-primary px-4" type="submit" id="btnSend">
                                <i class="bi bi-send-fill me-1"></i> Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

document.addEventListener('DOMContentLoaded', scrollToBottom);

function fillPrompt(text) {
    const input = document.getElementById('messageInput');
    if (input) {
        input.value = text;
        input.focus();
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatMarkdownLinks(text) {
    let escaped = escapeHtml(text);
    // Replace [label](url) with actual links
    return escaped.replace(/\[([^\]]+)\]\((https?:\/\/[^\)]+)\)/g, '<a href="$2" target="_blank" class="text-info fw-semibold text-decoration-underline">$1</a>');
}

async function handleChatSubmit(event) {
    event.preventDefault();
    const input = document.getElementById('messageInput');
    const btn = document.getElementById('btnSend');
    const container = document.getElementById('messagesContainer');
    const typingIndicator = document.getElementById('typingIndicator');
    const convIdInput = document.getElementById('conversationId');

    const message = input.value.trim();
    if (!message) return;

    // Append User Message to UI
    const now = new Date();
    const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

    const userHtml = `
        <div class="d-flex justify-content-end message-item">
            <div class="p-3 rounded-3 bg-primary text-white" style="max-width: 82%;">
                <div class="message-body" style="white-space: pre-wrap; line-height: 1.6;">${escapeHtml(message)}</div>
                <div class="text-end mt-1" style="font-size: 0.7rem; opacity: 0.7;">${timeStr}</div>
            </div>
        </div>
    `;

    // Remove welcome placeholder if exists
    const welcome = container.querySelector('.text-center.my-auto');
    if (welcome) {
        welcome.remove();
    }

    container.insertAdjacentHTML('beforeend', userHtml);
    input.value = '';
    btn.disabled = true;
    typingIndicator.classList.remove('d-none');
    scrollToBottom();

    try {
        const response = await fetch('{{ route('chatbot.message') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                message: message,
                conversation_id: convIdInput.value,
            })
        });

        const data = await response.json();
        typingIndicator.classList.add('d-none');
        btn.disabled = false;

        if (response.ok && data.success) {
            if (data.title && document.getElementById('convTitleHeader')) {
                document.getElementById('convTitleHeader').textContent = data.title;
            }

            let sourceBadge = '';
            if (data.message.source === 'database') {
                sourceBadge = '<span class="badge bg-success bg-opacity-25 text-success ms-2" style="font-size: 0.65rem;">Verified DB</span>';
            } else if (data.message.source === 'knowledge_base') {
                sourceBadge = '<span class="badge bg-info bg-opacity-25 text-info ms-2" style="font-size: 0.65rem;">FAQ Knowledge</span>';
            }

            const assistantHtml = `
                <div class="d-flex justify-content-start message-item">
                    <div class="p-3 rounded-3 bg-dark border border-secondary text-light" style="max-width: 82%;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-robot text-info small"></i>
                            <span class="small fw-semibold text-info">Assistant</span>
                            ${sourceBadge}
                        </div>
                        <div class="message-body" style="white-space: pre-wrap; line-height: 1.6;">${formatMarkdownLinks(data.message.message)}</div>
                        <div class="text-end mt-1" style="font-size: 0.7rem; opacity: 0.7;">${data.message.created_at || timeStr}</div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', assistantHtml);
            scrollToBottom();
        } else {
            const errorHtml = `
                <div class="alert alert-danger bg-danger bg-opacity-10 border-danger small py-2 px-3 mb-2">
                    <i class="bi bi-exclamation-triangle me-1"></i> ${data.error || 'Failed to send message. Please try again.'}
                </div>
            `;
            container.insertAdjacentHTML('beforeend', errorHtml);
            scrollToBottom();
        }
    } catch (err) {
        typingIndicator.classList.add('d-none');
        btn.disabled = false;
        const errHtml = `
            <div class="alert alert-danger bg-danger bg-opacity-10 border-danger small py-2 px-3 mb-2">
                <i class="bi bi-wifi-off me-1"></i> Network error connecting to Fan Assistant. Please check connection.
            </div>
        `;
        container.insertAdjacentHTML('beforeend', errHtml);
        scrollToBottom();
    }
}
</script>
@endsection
