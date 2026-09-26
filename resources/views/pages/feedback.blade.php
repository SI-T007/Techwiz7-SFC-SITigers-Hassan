@extends('layouts.app')

@section('title', 'Platform Feedback & Bug Reports — Fan Hub Plus')

@push('styles')
<style>
    .fh-feedback-type-card {
        cursor: pointer;
        background: rgba(255, 255, 255, 0.03);
        border: 2px solid rgba(255, 255, 255, 0.08);
        border-radius: 1rem;
        padding: 1rem;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        user-select: none;
        position: relative;
        overflow: hidden;
    }
    .fh-feedback-type-card:hover {
        background: rgba(255, 255, 255, 0.06);
        transform: translateY(-2px);
        border-color: rgba(255, 255, 255, 0.2);
    }
    .fh-feedback-type-card.active {
        background: rgba(229, 9, 20, 0.12);
        border-color: rgba(229, 9, 20, 0.8);
        box-shadow: 0 0 20px rgba(229, 9, 20, 0.3);
    }
    .fh-feedback-type-card[data-type="suggestion"].active {
        background: rgba(245, 158, 11, 0.12);
        border-color: rgba(245, 158, 11, 0.8);
        box-shadow: 0 0 20px rgba(245, 158, 11, 0.3);
    }
    .fh-feedback-type-card[data-type="query"].active {
        background: rgba(6, 182, 212, 0.12);
        border-color: rgba(6, 182, 212, 0.8);
        box-shadow: 0 0 20px rgba(6, 182, 212, 0.3);
    }
    .fh-feedback-type-card[data-type="other"].active {
        background: rgba(139, 92, 246, 0.12);
        border-color: rgba(139, 92, 246, 0.8);
        box-shadow: 0 0 20px rgba(139, 92, 246, 0.3);
    }
    .fh-feedback-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
    .feedback-history-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 0.75rem;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .feedback-history-card:hover {
        border-color: rgba(255, 255, 255, 0.15);
    }
</style>
@endpush

@section('content')
<div class="container py-4 py-lg-5">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            
            <!-- Hero Header -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-danger bg-opacity-15 border border-danger border-opacity-25 text-danger small fw-semibold mb-2">
                    <i class="bi bi-chat-heart-fill"></i> Community Voice & Bug Tracker
                </div>
                <h1 class="display-6 fw-bold text-light mb-2">Send Feedback or Report an Error</h1>
                <p class="text-muted mx-auto" style="max-width: 620px;">
                    Have an idea for a new fandom realm, found a technical bug, or need platform assistance? Your ticket goes directly to our administration and engineering team.
                </p>
                @auth
                    <div class="mt-2">
                        <a href="{{ route('user.dashboard') }}#feedback" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-speedometer2 me-1"></i> Open User Dashboard Feedback Panel &rarr;
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Confirmation Alert Banner (Session / AJAX) -->
            <div id="feedbackPageAlertContainer">
                @if(session('success'))
                    <div class="alert alert-success border-success border-opacity-25 shadow-lg d-flex align-items-start gap-3 p-3 mb-4 rounded-3" style="background: rgba(16, 185, 129, 0.12);" role="alert">
                        <i class="bi bi-check-circle-fill text-success fs-4 flex-shrink-0 mt-1"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-light mb-1">Feedback Submitted Successfully!</div>
                            <div class="text-light small">{{ session('success') }}</div>
                            @if(session('ticket_id'))
                                <div class="mt-2">
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">Ticket Reference #{{ session('ticket_id') }}</span>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            <!-- Main Interactive Form Card -->
            <div class="fh-card p-4 p-md-5 mb-5 shadow-lg position-relative overflow-hidden">
                <form id="publicFeedbackForm" method="POST" action="{{ route('feedback.store') }}">
                    @csrf
                    <input type="hidden" name="type" id="feedbackTypeInput" value="{{ old('type', $initialType ?? 'suggestion') }}">

                    <!-- 1. Interactive Visual Category Selector -->
                    <div class="mb-4">
                        <label class="form-label text-light fw-bold small text-uppercase" style="letter-spacing: 0.05em;">
                            1. Select Report Category <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3" id="feedbackCategoryCards">
                            <!-- Suggestion -->
                            <div class="col-sm-6 col-md-3">
                                <div class="fh-feedback-type-card h-100 {{ (old('type', $initialType ?? 'suggestion') === 'suggestion') ? 'active' : '' }}" data-type="suggestion">
                                    <div class="fh-feedback-card-icon bg-warning bg-opacity-15 text-warning mb-2">
                                        <i class="bi bi-lightbulb-fill"></i>
                                    </div>
                                    <div class="fw-bold text-light small mb-1">Feature / Idea</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Request new fandom, canon lore, or feature</div>
                                </div>
                            </div>

                            <!-- Bug Report -->
                            <div class="col-sm-6 col-md-3">
                                <div class="fh-feedback-type-card h-100 {{ (old('type', $initialType ?? 'suggestion') === 'bug') ? 'active' : '' }}" data-type="bug">
                                    <div class="fh-feedback-card-icon bg-danger bg-opacity-15 text-danger mb-2">
                                        <i class="bi bi-bug-fill"></i>
                                    </div>
                                    <div class="fw-bold text-light small mb-1">Bug / Glitch</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Report broken player, error, or styling bug</div>
                                </div>
                            </div>

                            <!-- General Query -->
                            <div class="col-sm-6 col-md-3">
                                <div class="fh-feedback-type-card h-100 {{ (old('type', $initialType ?? 'suggestion') === 'query') ? 'active' : '' }}" data-type="query">
                                    <div class="fh-feedback-card-icon bg-info bg-opacity-15 text-info mb-2">
                                        <i class="bi bi-question-circle-fill"></i>
                                    </div>
                                    <div class="fw-bold text-light small mb-1">General Query</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Account, moderation, or universe questions</div>
                                </div>
                            </div>

                            <!-- General Feedback -->
                            <div class="col-sm-6 col-md-3">
                                <div class="fh-feedback-type-card h-100 {{ (old('type', $initialType ?? 'suggestion') === 'other') ? 'active' : '' }}" data-type="other">
                                    <div class="fh-feedback-card-icon bg-purple bg-opacity-15 text-purple mb-2">
                                        <i class="bi bi-chat-heart-fill"></i>
                                    </div>
                                    <div class="fw-bold text-light small mb-1">Platform Feedback</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">General thoughts, impressions, and praise</div>
                                </div>
                            </div>
                        </div>
                        @error('type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 2. Sender Information -->
                    @auth
                        <div class="p-3 rounded-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2 border border-secondary border-opacity-25" style="background: rgba(255,255,255,0.02);">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                <div>
                                    <div class="text-light fw-bold small">Submitting as: {{ Auth::user()->name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ Auth::user()->email }} &bull; Responses will appear in your User Dashboard</div>
                                </div>
                            </div>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 small">
                                <i class="bi bi-patch-check-fill me-1"></i> Verified Fan
                            </span>
                        </div>
                    @else
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label text-light fw-semibold small">Your Name / Handle</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control bg-dark text-light border-secondary @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       placeholder="E.g. Eren Yeager">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label text-light fw-semibold small">Email Address</label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control bg-dark text-light border-secondary @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" 
                                       placeholder="name@example.com">
                                <div class="form-text text-muted" style="font-size: 0.72rem;">Optional for guests. Provide if you would like an email response.</div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endauth

                    <!-- 3. Headline / Subject -->
                    <div class="mb-3">
                        <label for="pageSubject" class="form-label text-light fw-bold small text-uppercase" style="letter-spacing: 0.05em;">
                            2. Subject / Issue Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="subject" 
                               id="pageSubject" 
                               class="form-control form-control-lg bg-dark text-light border-secondary @error('subject') is-invalid @enderror" 
                               value="{{ old('subject') }}" 
                               placeholder="E.g., Suggest adding Demon Slayer universe lore or watch progress sync..." 
                               required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 4. Detailed Description -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="pageMessage" class="form-label text-light fw-bold small text-uppercase mb-0" style="letter-spacing: 0.05em;">
                                3. Details & Explanation <span class="text-danger">*</span>
                            </label>
                            <span class="text-muted small" id="pageCharCounter" style="font-size: 0.75rem;">0 / 3000</span>
                        </div>
                        <textarea name="message" 
                                  id="pageMessage" 
                                  class="form-control bg-dark text-light border-secondary @error('message') is-invalid @enderror" 
                                  rows="6" 
                                  maxlength="3000"
                                  placeholder="Describe the problem, feature request, or feedback in detail. If reporting a glitch, include what you were doing and what device/browser you are using..." 
                                  required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 5. Action Buttons -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 border-top border-secondary border-opacity-25">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back to Universe
                        </a>
                        <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold d-flex align-items-center gap-2" id="pageSubmitBtn" style="background: linear-gradient(135deg, #e50914 0%, #b20710 100%); border: none; box-shadow: 0 4px 15px rgba(229, 9, 20, 0.4);">
                            <i class="bi bi-send-fill"></i>
                            <span>Send Ticket to Admin</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- 6. Logged-in User's Previous Ticket History Section -->
            @auth
                @if(isset($userFeedbacks) && $userFeedbacks->count() > 0)
                    <div class="fh-card p-4 mb-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                            <div>
                                <h5 class="fw-bold text-light mb-1">
                                    <i class="bi bi-clock-history text-info me-2"></i> Your Recent Feedback Tickets
                                </h5>
                                <p class="text-muted small mb-0">Track responses and resolution status from the administration team</p>
                            </div>
                            <a href="{{ route('user.dashboard') }}#feedback" class="btn btn-sm btn-outline-light">
                                View Full Dashboard Panel &rarr;
                            </a>
                        </div>

                        <div class="d-flex flex-column gap-3" id="pageFeedbackHistoryList">
                            @foreach($userFeedbacks as $fb)
                                @php
                                    $typeBadge = match($fb->type) {
                                        'bug' => ['class' => 'bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25', 'icon' => 'bi-bug-fill', 'label' => 'Bug Report'],
                                        'suggestion' => ['class' => 'bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25', 'icon' => 'bi-lightbulb-fill', 'label' => 'Suggestion'],
                                        'query' => ['class' => 'bg-info bg-opacity-25 text-info border border-info border-opacity-25', 'icon' => 'bi-question-circle-fill', 'label' => 'Question'],
                                        default => ['class' => 'bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25', 'icon' => 'bi-chat-heart-fill', 'label' => 'Feedback'],
                                    };

                                    $statusBadge = match($fb->status) {
                                        'read' => ['class' => 'bg-info bg-opacity-25 text-info border border-info border-opacity-25', 'icon' => 'bi-eye-fill', 'label' => 'Under Review'],
                                        'replied' => ['class' => 'bg-success bg-opacity-25 text-success border border-success border-opacity-25', 'icon' => 'bi-check2-circle', 'label' => 'Replied & Resolved'],
                                        default => ['class' => 'bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25', 'icon' => 'bi-hourglass-split', 'label' => 'Pending Review'],
                                    };
                                @endphp
                                <div class="p-3 feedback-history-card">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ $typeBadge['class'] }} small">
                                                <i class="bi {{ $typeBadge['icon'] }} me-1"></i> {{ $typeBadge['label'] }}
                                            </span>
                                            <span class="badge {{ $statusBadge['class'] }} small">
                                                <i class="bi {{ $statusBadge['icon'] }} me-1"></i> {{ $statusBadge['label'] }}
                                            </span>
                                            <span class="text-secondary small">#{{ $fb->id }}</span>
                                        </div>
                                        <span class="text-muted small">{{ $fb->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h6 class="text-light fw-bold mb-1">{{ $fb->subject }}</h6>
                                    <p class="text-muted small mb-2 text-break">{{ $fb->message }}</p>

                                    @if($fb->admin_reply)
                                        <div class="p-3 mt-3 rounded-3 border border-success border-opacity-25" style="background: rgba(16, 185, 129, 0.08);">
                                            <div class="d-flex align-items-center gap-2 text-success small fw-bold mb-1">
                                                <i class="bi bi-patch-check-fill"></i>
                                                Official Response from {{ $fb->repliedBy?->name ?? 'Platform Team' }}
                                                <span class="text-muted fw-normal ms-auto" style="font-size: 0.75rem;">
                                                    {{ $fb->replied_at ? $fb->replied_at->diffForHumans() : 'Recently' }}
                                                </span>
                                            </div>
                                            <div class="text-light small text-break mb-0">
                                                {{ $fb->admin_reply }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endauth

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeCards = document.querySelectorAll('#feedbackCategoryCards .fh-feedback-type-card');
    const typeInput = document.getElementById('feedbackTypeInput');
    const subjectInput = document.getElementById('pageSubject');
    const messageInput = document.getElementById('pageMessage');
    const charCounter = document.getElementById('pageCharCounter');
    const form = document.getElementById('publicFeedbackForm');
    const submitBtn = document.getElementById('pageSubmitBtn');
    const alertContainer = document.getElementById('feedbackPageAlertContainer');

    const placeholders = {
        suggestion: 'E.g., Suggest adding Demon Slayer universe lore or watch progress sync...',
        bug: 'E.g., Broken video playback on mobile, character page missing stats, 404 error...',
        query: 'E.g., How do I submit my fan art to the community hub?',
        other: 'E.g., My thoughts on the new cinematic streaming layout...'
    };

    // Category Card Selection
    typeCards.forEach(card => {
        card.addEventListener('click', function() {
            const selectedType = this.dataset.type;
            typeCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            if (typeInput) typeInput.value = selectedType;
            if (subjectInput && placeholders[selectedType]) {
                subjectInput.placeholder = placeholders[selectedType];
            }
        });
    });

    // Character Counter
    if (messageInput && charCounter) {
        messageInput.addEventListener('input', function() {
            charCounter.textContent = `${this.value.length} / 3000`;
        });
        charCounter.textContent = `${messageInput.value.length} / 3000`;
    }

    // Interactive AJAX Submission with graceful fallback
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sending to Admin...';

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                if (data.success) {
                    const ticketId = data.ticket_id || (data.feedback ? data.feedback.id : '');
                    
                    alertContainer.innerHTML = `
                        <div class="alert alert-success border-success border-opacity-25 shadow-lg d-flex align-items-start gap-3 p-3 mb-4 rounded-3" style="background: rgba(16, 185, 129, 0.15); animation: fadeIn 0.3s ease-out;" role="alert">
                            <i class="bi bi-check-circle-fill text-success fs-3 flex-shrink-0 mt-1"></i>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold text-light mb-1">Ticket Sent to Administration!</h5>
                                <div class="text-light small">${escapeHtml(data.message)}</div>
                                ${ticketId ? `<div class="mt-2"><span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">Ticket Reference #${ticketId}</span></div>` : ''}
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // Reset fields
                    form.reset();
                    if (charCounter) charCounter.textContent = '0 / 3000';
                    typeCards.forEach(c => c.classList.remove('active'));
                    const defaultCard = document.querySelector('#feedbackCategoryCards [data-type="suggestion"]');
                    if (defaultCard) defaultCard.classList.add('active');
                    if (typeInput) typeInput.value = 'suggestion';

                    // If history list exists and feedback object returned, prepend it
                    const historyList = document.getElementById('pageFeedbackHistoryList');
                    if (historyList && data.feedback) {
                        const fb = data.feedback;
                        const typeConfig = {
                            bug: { class: 'bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25', icon: 'bi-bug-fill', label: 'Bug Report' },
                            suggestion: { class: 'bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25', icon: 'bi-lightbulb-fill', label: 'Suggestion' },
                            query: { class: 'bg-info bg-opacity-25 text-info border border-info border-opacity-25', icon: 'bi-question-circle-fill', label: 'Question' },
                            other: { class: 'bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25', icon: 'bi-chat-heart-fill', label: 'Feedback' },
                        }[fb.type] || { class: 'bg-primary bg-opacity-25 text-primary', icon: 'bi-chat-heart-fill', label: 'Feedback' };

                        const newCard = document.createElement('div');
                        newCard.className = 'p-3 feedback-history-card';
                        newCard.innerHTML = `
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge ${typeConfig.class} small">
                                        <i class="bi ${typeConfig.icon} me-1"></i> ${typeConfig.label}
                                    </span>
                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 small">
                                        <i class="bi bi-hourglass-split me-1"></i> Pending Review
                                    </span>
                                    <span class="text-secondary small">#${fb.id}</span>
                                </div>
                                <span class="text-muted small">Just now</span>
                            </div>
                            <h6 class="text-light fw-bold mb-1">${escapeHtml(fb.subject)}</h6>
                            <p class="text-muted small mb-2 text-break">${escapeHtml(fb.message)}</p>
                        `;
                        historyList.prepend(newCard);
                    }
                } else {
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger border-danger border-opacity-25 shadow-lg d-flex align-items-center gap-3 p-3 mb-4 rounded-3" style="background: rgba(239, 68, 68, 0.15);" role="alert">
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-4 flex-shrink-0"></i>
                            <div class="flex-grow-1 text-light small">${escapeHtml(data.message || 'Error sending feedback. Please check your inputs.')}</div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                }
            })
            .catch(err => {
                // If fetch fails (e.g. offline/CSRF issue), fallback to standard form submit
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                form.submit();
            });
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endpush
