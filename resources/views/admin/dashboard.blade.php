@extends('layouts.app')

@section('title', 'Universe Control Center — Admin Console — Fan Hub Plus')

@push('styles')
<style>
    .fh-admin-card {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1rem;
        backdrop-filter: blur(12px);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        transition: transform 0.25s ease, border-color 0.25s ease;
    }
    .fh-admin-card:hover {
        border-color: rgba(99, 102, 241, 0.35);
    }
    .kpi-stat-card {
        position: relative;
        overflow: hidden;
    }
    .kpi-stat-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #6366f1, #06b6d4);
        opacity: 0.8;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-lg-5 py-4">
    <!-- Header with Quick Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25">
                    <i class="bi bi-cpu-fill me-1"></i> CONTROL HUB
                </span>
            </div>
            <h1 class="h2 fw-bold text-light mb-1">Universe Control Center</h1>
            <p class="text-muted small mb-0">Central governance, visual platform telemetry, moderation queues, and fandom operations</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.media.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Add Media
            </a>
            <a href="{{ route('admin.media.index') }}" class="btn btn-outline-info btn-sm">
                <i class="bi bi-play-circle me-1"></i> Media ({{ $metrics['total_media'] }})
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="btn btn-primary-gradient btn-sm">
                <i class="bi bi-graph-up-arrow me-1"></i> Full Analytics Command
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-info btn-sm">
                <i class="bi bi-grid me-1"></i> Categories
            </a>
            <a href="{{ route('admin.characters.index') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-people me-1"></i> Characters
            </a>
            <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-file-text me-1"></i> Articles
            </a>
            <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-chat-left-dots me-1"></i> Feedback
                @if(isset($metrics['new_feedback']) && $metrics['new_feedback'] > 0)
                    <span class="badge bg-danger ms-1">{{ $metrics['new_feedback'] }}</span>
                @endif
            </a>
            <a href="{{ route('admin.users') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-person-gear me-1"></i> Users ({{ $metrics['total_users'] }})
            </a>
        </div>
    </div>

    <!-- Admin KPI Telemetry Row with Graphical Accents -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="fh-admin-card kpi-stat-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Registered Users</span>
                    <i class="bi bi-people-fill text-purple"></i>
                </div>
                <div class="h3 fw-bold text-light mb-0">{{ number_format($metrics['total_users']) }}</div>
                <div class="small text-success mt-1" style="font-size: 0.72rem;"><i class="bi bi-check-circle"></i> {{ $metrics['active_users'] }} active</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="fh-admin-card kpi-stat-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-info text-uppercase fw-semibold" style="font-size: 0.7rem;">Fandom Universes</span>
                    <i class="bi bi-grid-fill text-info"></i>
                </div>
                <div class="h3 fw-bold text-info mb-0">{{ $metrics['total_categories'] }}</div>
                <div class="small mt-1"><a href="{{ route('admin.categories.index') }}" class="text-decoration-none text-light small" style="font-size: 0.72rem;"><i class="bi bi-arrow-right"></i> Manage Universes</a></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="fh-admin-card kpi-stat-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-primary text-uppercase fw-semibold" style="font-size: 0.7rem;">Character Lore</span>
                    <i class="bi bi-person-lines-fill text-primary"></i>
                </div>
                <div class="h3 fw-bold text-primary mb-0">{{ $metrics['total_characters'] }}</div>
                <div class="small mt-1"><a href="{{ route('admin.characters.index') }}" class="text-decoration-none text-light small" style="font-size: 0.72rem;"><i class="bi bi-people"></i> Profiles</a></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="fh-admin-card kpi-stat-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-warning text-uppercase fw-semibold" style="font-size: 0.7rem;">Moderation Queue</span>
                    <i class="bi bi-shield-exclamation text-warning"></i>
                </div>
                <div class="h3 fw-bold text-warning mb-0">{{ $metrics['pending_submissions'] }}</div>
                <div class="small mt-1"><a href="{{ route('admin.submissions.index') }}" class="text-decoration-none text-light small" style="font-size: 0.72rem;"><i class="bi bi-patch-check"></i> Pending Reviews</a></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="fh-admin-card kpi-stat-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-info text-uppercase fw-semibold" style="font-size: 0.7rem;">AI Chatbot KB</span>
                    <i class="bi bi-robot text-info"></i>
                </div>
                <div class="h3 fw-bold text-light mb-0">{{ $metrics['total_kb'] }}</div>
                <div class="small text-info mt-1" style="font-size: 0.72rem;"><i class="bi bi-chat-dots"></i> {{ $metrics['total_messages'] }} queries</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="fh-admin-card kpi-stat-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-warning text-uppercase fw-semibold" style="font-size: 0.7rem;">Community Score</span>
                    <i class="bi bi-star-fill text-warning"></i>
                </div>
                <div class="h3 fw-bold text-warning mb-0">{{ number_format($metrics['avg_rating'], 1) }}★</div>
                <div class="small mt-1 text-muted" style="font-size: 0.72rem;">{{ $metrics['total_ratings'] }} reviews</div>
            </div>
        </div>
    </div>

    <!-- Graphical Quick Overview Row (Weekly Sparkline + Universe Distribution Radar) -->
    <div class="row g-4 mb-4">
        <!-- 7-Day Platform Velocity Area Chart -->
        <div class="col-lg-8">
            <div class="fh-admin-card p-4 h-100">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h5 fw-bold text-light mb-1">
                            <i class="bi bi-activity text-primary me-2"></i>7-Day Platform Velocity Sparkline
                        </h2>
                        <p class="text-muted small mb-0">Daily activity flow, member signups, and AI inquiries over the last 7 days.</p>
                    </div>
                    <a href="{{ route('admin.analytics.index') }}" class="badge bg-primary bg-opacity-25 text-primary text-decoration-none small">
                        View Full Telemetry &rarr;
                    </a>
                </div>
                <div style="position: relative; height: 240px; width: 100%;">
                    <canvas id="adminVelocityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Moderation & Content Status Gauges -->
        <div class="col-lg-4">
            <div class="fh-admin-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold text-light mb-0">
                        <i class="bi bi-pie-chart text-info me-2"></i>Submission Moderation
                    </h2>
                    <span class="badge bg-info bg-opacity-25 text-info small">Live Pipeline</span>
                </div>
                <div style="position: relative; height: 180px; width: 100%;">
                    <canvas id="adminModerationDonut"></canvas>
                </div>
                <div class="d-flex justify-content-around mt-3 pt-2 border-top border-secondary border-opacity-25 text-center small">
                    <div>
                        <div class="text-success fw-bold">{{ $metrics['approved_submissions'] }}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">Approved</div>
                    </div>
                    <div>
                        <div class="text-warning fw-bold">{{ $metrics['pending_submissions'] }}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">Pending</div>
                    </div>
                    <div>
                        <div class="text-danger fw-bold">{{ $metrics['rejected_submissions'] }}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">Rejected</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Modules Grid -->
    <div class="row g-4 mb-4">
        <!-- Content & Lore Group -->
        <div class="col-md-6 col-lg-4">
            <div class="fh-admin-card p-4 h-100">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-25 text-primary" style="width: 36px; height: 36px;">
                        <i class="bi bi-journal-richtext"></i>
                    </div>
                    <h2 class="h5 fw-bold text-light mb-0">Universe Content & Lore</h2>
                </div>
                <div class="list-group list-group-flush bg-transparent">
                    <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-secondary border-opacity-25">
                        <span><i class="bi bi-grid me-2 text-info"></i> Fandom Categories</span>
                        <span class="badge bg-secondary bg-opacity-25 text-light">{{ $metrics['total_categories'] }}</span>
                    </a>
                    <a href="{{ route('admin.characters.index') }}" class="list-group-item list-group-item-action bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-secondary border-opacity-25">
                        <span><i class="bi bi-people me-2 text-primary"></i> Character Lore</span>
                        <span class="badge bg-secondary bg-opacity-25 text-light">{{ $metrics['total_characters'] }}</span>
                    </a>
                    <a href="{{ route('admin.articles.index') }}" class="list-group-item list-group-item-action bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-secondary border-opacity-25">
                        <span><i class="bi bi-file-text me-2 text-warning"></i> Lore Articles</span>
                        <span class="badge bg-secondary bg-opacity-25 text-light">{{ $metrics['total_articles'] }}</span>
                    </a>
                    <a href="{{ route('admin.content.index') }}" class="list-group-item list-group-item-action bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-secondary border-opacity-25">
                        <span><i class="bi bi-collection me-2 text-info"></i> All Content Hubs</span>
                        <span class="badge bg-secondary bg-opacity-25 text-light">{{ $metrics['total_contents'] }}</span>
                    </a>
                    <div class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <a href="{{ route('admin.media.index') }}" class="text-light text-decoration-none d-flex align-items-center">
                            <i class="bi bi-play-circle me-2 text-cyan" style="color: #06b6d4;"></i> Multimedia Assets
                            <span class="badge bg-secondary bg-opacity-25 text-light ms-2">{{ $metrics['total_media'] }}</span>
                        </a>
                        <a href="{{ route('admin.media.create') }}" class="badge bg-success bg-opacity-25 text-success text-decoration-none border border-success border-opacity-25 py-1 px-2">
                            <i class="bi bi-plus-circle me-1"></i> Add Media
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule & Community Moderation Group -->
        <div class="col-md-6 col-lg-4">
            <div class="fh-admin-card p-4 h-100">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-25 text-warning" style="width: 36px; height: 36px;">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h2 class="h5 fw-bold text-light mb-0">Radar, Media & Moderation</h2>
                </div>
                <div class="list-group list-group-flush bg-transparent">
                    <a href="{{ route('admin.events.index') }}" class="list-group-item list-group-item-action bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-secondary border-opacity-25">
                        <span><i class="bi bi-calendar-event me-2 text-danger"></i> Events & Conventions</span>
                        <span class="badge bg-secondary bg-opacity-25 text-light">{{ $metrics['total_events'] }}</span>
                    </a>
                    <a href="{{ route('admin.releases.index') }}" class="list-group-item list-group-item-action bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-secondary border-opacity-25">
                        <span><i class="bi bi-disc me-2 text-purple"></i> Media Releases</span>
                        <span class="badge bg-secondary bg-opacity-25 text-light">{{ $metrics['total_releases'] }}</span>
                    </a>
                    <a href="{{ route('admin.submissions.index') }}" class="list-group-item list-group-item-action bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-secondary border-opacity-25">
                        <span><i class="bi bi-palette me-2 text-warning"></i> Fan Moderation Queue</span>
                        @if($metrics['pending_submissions'] > 0)
                            <span class="badge bg-warning text-dark">{{ $metrics['pending_submissions'] }} pending</span>
                        @else
                            <span class="badge bg-success bg-opacity-25 text-success">Clean</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.feedback.index') }}" class="list-group-item list-group-item-action bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <span><i class="bi bi-chat-square-dots me-2 text-info"></i> Member Feedback</span>
                        @if($metrics['new_feedback'] > 0)
                            <span class="badge bg-danger">{{ $metrics['new_feedback'] }} new</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-25 text-light">0 new</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <!-- System Governance & Intelligence Group -->
        <div class="col-md-12 col-lg-4">
            <div class="fh-admin-card p-4 h-100">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-25 text-info" style="width: 36px; height: 36px;">
                        <i class="bi bi-robot"></i>
                    </div>
                    <h2 class="h5 fw-bold text-light mb-0">Governance & Intelligence</h2>
                </div>
                <div class="list-group list-group-flush bg-transparent">
                    <a href="{{ route('admin.chatbot.knowledge.index') }}" class="list-group-item list-group-item-action bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-secondary border-opacity-25">
                        <span><i class="bi bi-database-check me-2 text-info"></i> Chatbot Knowledge Base</span>
                        <span class="badge bg-secondary bg-opacity-25 text-light">{{ $metrics['total_kb'] }} items</span>
                    </a>
                    <a href="{{ route('admin.chatbot.conversations.index') }}" class="list-group-item list-group-item-action bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-secondary border-opacity-25">
                        <span><i class="bi bi-chat-left-text me-2 text-primary"></i> Chatbot Conversations</span>
                        <span class="badge bg-secondary bg-opacity-25 text-light">{{ $metrics['total_conversations'] }}</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-secondary border-opacity-25">
                        <span><i class="bi bi-person-gear me-2 text-purple"></i> User Management</span>
                        <span class="badge bg-secondary bg-opacity-25 text-light">{{ $metrics['total_users'] }}</span>
                    </a>
                    <a href="{{ route('admin.analytics.index') }}" class="list-group-item list-group-item-action bg-transparent text-light d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <span><i class="bi bi-graph-up-arrow me-2 text-success"></i> Telemetry & Visual Analytics</span>
                        <span class="badge bg-success bg-opacity-25 text-success">Dynamic Engine</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent System Audit Log & New Member Signups -->
    <div class="row g-4">
        <!-- Recent Users -->
        <div class="col-lg-5">
            <div class="fh-admin-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold text-light mb-0">
                        <i class="bi bi-person-plus text-primary me-2"></i>Recent Member Signups
                    </h2>
                    <a href="{{ route('admin.users') }}" class="small text-info text-decoration-none">View all &rarr;</a>
                </div>
                <div class="list-group list-group-flush bg-transparent">
                    @forelse($recentUsers as $recentUser)
                        <div class="list-group-item bg-transparent text-light px-0 py-2 d-flex justify-content-between align-items-center border-secondary border-opacity-25">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $recentUser->avatar_url }}" alt="{{ $recentUser->name }}" class="avatar-sm rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                <div>
                                    <a href="{{ route('admin.users.show', $recentUser) }}" class="text-light text-decoration-none fw-semibold">
                                        {{ $recentUser->name }}
                                    </a>
                                    <div class="text-muted small" style="font-size: 0.72rem;">{{ $recentUser->email }}</div>
                                </div>
                            </div>
                            <span class="badge {{ $recentUser->isAdmin() ? 'badge-role-admin' : 'badge-role-user' }} small">
                                {{ $recentUser->role }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No users found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Audit & Platform Activities -->
        <div class="col-lg-7">
            <div class="fh-admin-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold text-light mb-0">
                        <i class="bi bi-clock-history text-info me-2"></i>Live System Activity Feed
                    </h2>
                    <span class="badge bg-secondary bg-opacity-25 text-light small">Audit Stream</span>
                </div>
                <div class="list-group list-group-flush bg-transparent">
                    @forelse($recentActivities as $act)
                        <div class="list-group-item bg-transparent text-light px-0 py-2 border-secondary border-opacity-25">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    @php
                                        $actBadge = match($act->activity_type) {
                                            'submitted_feedback' => 'bg-danger bg-opacity-25 text-danger',
                                            'bookmarked' => 'bg-warning bg-opacity-25 text-warning',
                                            'rated' => 'bg-info bg-opacity-25 text-info',
                                            'submitted_fan_content' => 'bg-success bg-opacity-25 text-success',
                                            default => 'bg-primary bg-opacity-25 text-primary',
                                        };
                                    @endphp
                                    <span class="badge {{ $actBadge }} me-2 small text-capitalize">{{ str_replace('_', ' ', $act->activity_type) }}</span>
                                    <span class="text-light small">{{ $act->description }}</span>
                                </div>
                                <span class="text-muted small" style="font-size: 0.72rem;">{{ $act->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No recent activity logs recorded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";

    // 1. Weekly Velocity Line/Area Chart
    const velocityCtx = document.getElementById('adminVelocityChart');
    if (velocityCtx) {
        new Chart(velocityCtx, {
            type: 'line',
            data: {
                labels: @json($sparklineData['labels']),
                datasets: [
                    {
                        label: 'Activities',
                        data: @json($sparklineData['activities']),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.15)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35,
                    },
                    {
                        label: 'Bot Inquiries',
                        data: @json($sparklineData['bot_messages']),
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.10)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35,
                    },
                    {
                        label: 'New Users',
                        data: @json($sparklineData['signups']),
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.10)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: { grid: { color: 'rgba(255, 255, 255, 0.05)' } },
                    y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' } }
                },
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 10, padding: 8 } }
                }
            }
        });
    }

    // 2. Moderation Donut Chart
    const modCtx = document.getElementById('adminModerationDonut');
    if (modCtx) {
        new Chart(modCtx, {
            type: 'doughnut',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    data: [
                        {{ $metrics['approved_submissions'] }},
                        {{ $metrics['pending_submissions'] }},
                        {{ $metrics['rejected_submissions'] }}
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderColor: 'rgba(15, 23, 42, 0.95)',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>
@endpush
