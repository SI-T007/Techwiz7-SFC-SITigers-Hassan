@extends('layouts.app')

@section('title', 'User Accounts & Roles — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h2 fw-bold text-light mb-0">User Directory</h1>
            </div>
            <p class="text-muted small mb-0">Registered User Accounts, security clearances, active account statuses, and member engagement</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm text-light">
            <i class="bi bi-speedometer2 me-1"></i> Admin Console
        </a>
    </div>

    <!-- Quick Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="fh-card p-3">
                <div class="small text-muted mb-1">Total Registered</div>
                <div class="h3 fw-bold text-light mb-0">{{ $metrics['total'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fh-card p-3 border-danger border-opacity-50">
                <div class="small text-danger mb-1">Administrators</div>
                <div class="h3 fw-bold text-danger mb-0">{{ $metrics['admins'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fh-card p-3 border-success border-opacity-50">
                <div class="small text-success mb-1">Active Accounts</div>
                <div class="h3 fw-bold text-success mb-0">{{ $metrics['active'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fh-card p-3 border-secondary border-opacity-50">
                <div class="small text-secondary mb-1">Deactivated</div>
                <div class="h3 fw-bold text-secondary mb-0">{{ $metrics['inactive'] }}</div>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.users') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-dark border-secondary text-light" 
                           placeholder="Search by user name, handle, or email address..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Registered User</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Deactivated</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- User Table -->
    <div class="fh-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="table-dark text-muted small text-uppercase" style="letter-spacing: 0.05em;">
                    <tr>
                        <th>User Profile</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Activity Summary</th>
                        <th>Joined</th>
                        <th class="text-end" style="width: 170px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-25 text-primary fw-bold" style="width: 36px; height: 36px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users.show', $user) }}" class="fw-bold text-light text-decoration-none">
                                            {{ $user->name }}
                                        </a>
                                        @if($user->profile && $user->profile->handle)
                                            <div class="small text-muted">{{ '@' . $user->profile->handle }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><small class="text-light">{{ $user->email }}</small></td>
                            <td>
                                @if($user->isAdmin())
                                    <span class="badge badge-role-admin"><i class="bi bi-shield-fill me-1"></i> Admin</span>
                                @else
                                    <span class="badge badge-role-user"><i class="bi bi-person me-1"></i> Member</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success bg-opacity-25 text-success"><i class="bi bi-check-circle-fill me-1"></i> Active</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-25 text-danger"><i class="bi bi-x-circle-fill me-1"></i> Deactivated</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $user->bookmarks_count ?? 0 }} bookmarks &bull; {{ $user->ratings_count ?? 0 }} ratings &bull; {{ $user->fan_submissions_count ?? 0 }} fan works
                                </small>
                            </td>
                            <td><small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info" title="View Audit Profile">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if(Auth::id() !== $user->id)
                                        <!-- Role Change Trigger -->
                                        <button type="button" class="btn btn-outline-warning" title="Modify Clearance Role"
                                                data-bs-toggle="modal" data-bs-target="#roleModal{{ $user->id }}">
                                            <i class="bi bi-shield-shaded"></i>
                                        </button>

                                        <!-- Status Toggle -->
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" 
                                                    title="{{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}">
                                                <i class="bi {{ $user->is_active ? 'bi-lock' : 'bi-unlock' }}"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="btn btn-outline-secondary disabled" title="Current Session (Self)">
                                            <i class="bi bi-person-check-fill"></i>
                                        </span>
                                    @endif
                                </div>

                                <!-- Role Change Modal -->
                                @if(Auth::id() !== $user->id)
                                    <div class="modal fade" id="roleModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content bg-dark border-secondary text-start">
                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title text-light"><i class="bi bi-shield-shaded text-warning me-2"></i>Change Role: {{ $user->name }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                                    @csrf
                                                    <div class="modal-body text-light">
                                                        <p class="small text-muted mb-3">Adjusting user clearance changes access to administrative consoles and moderation queues.</p>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold">Select Security Role</label>
                                                            <select name="role" class="form-select bg-dark border-secondary text-light">
                                                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Registered Member (Regular User)</option>
                                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator (Full Management Clearance)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-secondary">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-warning btn-sm">Update Role</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                                No user accounts found matching the criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
