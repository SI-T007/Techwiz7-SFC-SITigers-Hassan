@extends('layouts.app')

@section('title', 'User Audit & Details — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h3 fw-bold text-light mb-0">User Profile Audit: {{ $user->name }}</h1>
            </div>
            <p class="text-muted small mb-0">Inspect account history, activity logs, bookmarks, and security roles</p>
        </div>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm text-light">
            <i class="bi bi-arrow-left me-1"></i> Back to Users Directory
        </a>
    </div>

    <div class="row g-4">
        <!-- User Info Card -->
        <div class="col-lg-4">
            <div class="fh-card p-4 text-center mb-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-25 text-primary fw-bold mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 class="h5 fw-bold text-light mb-1">{{ $user->name }}</h2>
                <div class="text-muted small mb-2">{{ $user->email }}</div>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    @if($user->isAdmin())
                        <span class="badge badge-role-admin"><i class="bi bi-shield-fill me-1"></i> Administrator</span>
                    @else
                        <span class="badge badge-role-user"><i class="bi bi-person me-1"></i> Registered Member</span>
                    @endif

                    @if($user->is_active)
                        <span class="badge bg-success bg-opacity-25 text-success"><i class="bi bi-check-circle-fill me-1"></i> Active</span>
                    @else
                        <span class="badge bg-danger bg-opacity-25 text-danger"><i class="bi bi-x-circle-fill me-1"></i> Deactivated</span>
                    @endif
                </div>

                @if(Auth::id() !== $user->id)
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} w-100">
                                <i class="bi {{ $user->is_active ? 'bi-lock me-1' : 'bi-unlock me-1' }}"></i>
                                {{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Platform Stats Card -->
            <div class="fh-card p-4">
                <h3 class="h6 fw-bold text-light mb-3 text-uppercase" style="letter-spacing: 0.05em;">Engagement Telemetry</h3>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li class="d-flex justify-content-between text-muted border-bottom border-secondary border-opacity-25 pb-2">
                        <span>Bookmarks Saved:</span>
                        <strong class="text-light">{{ $user->bookmarks_count }}</strong>
                    </li>
                    <li class="d-flex justify-content-between text-muted border-bottom border-secondary border-opacity-25 pb-2">
                        <span>5-Star Ratings Cast:</span>
                        <strong class="text-light">{{ $user->ratings_count }}</strong>
                    </li>
                    <li class="d-flex justify-content-between text-muted border-bottom border-secondary border-opacity-25 pb-2">
                        <span>Fan Submissions:</span>
                        <strong class="text-light">{{ $user->fan_submissions_count }}</strong>
                    </li>
                    <li class="d-flex justify-content-between text-muted border-bottom border-secondary border-opacity-25 pb-2">
                        <span>Chatbot Conversations:</span>
                        <strong class="text-light">{{ $user->conversations_count }}</strong>
                    </li>
                    <li class="d-flex justify-content-between text-muted">
                        <span>Registration Date:</span>
                        <strong class="text-light">{{ $user->created_at->format('M d, Y H:i') }}</strong>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Activity Timeline Card -->
        <div class="col-lg-8">
            <div class="fh-card p-4">
                <h3 class="h5 fw-bold text-light mb-3">Recent Activity Logs</h3>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead class="text-muted small text-uppercase">
                            <tr>
                                <th>Timestamp</th>
                                <th>Activity Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $act)
                                <tr>
                                    <td><small class="text-muted">{{ $act->created_at->format('M d, Y H:i:s') }}</small></td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-25 text-light">{{ str_replace('_', ' ', ucfirst($act->activity_type)) }}</span>
                                    </td>
                                    <td><small class="text-light">{{ $act->description }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="bi bi-clock-history fs-3 d-block mb-1 opacity-50"></i>
                                        No recent activity records logged for this user.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
