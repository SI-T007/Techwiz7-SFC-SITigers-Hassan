@extends('layouts.app')

@section('title', 'Manage Users — Admin Console')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-info text-decoration-none small">&larr; Back to Dashboard</a>
            </div>
            <h2 class="h2 fw-bold text-light mb-0">User Directory & Access Control</h2>
            <p class="text-muted small mb-0">Review all registered accounts, modify access status, and enforce universe rules</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.users') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary border-opacity-25 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Standard User</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Deactivated</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-gradient flex-fill">Filter</button>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-custom">Reset</a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="fh-card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-muted small border-secondary border-opacity-25">
                        <th>ID</th>
                        <th>User Profile</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Fandoms</th>
                        <th>Joined</th>
                        <th class="text-end">Access Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-secondary border-opacity-10">
                            <td class="text-muted small">#{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar-img" width="38" height="38">
                                    <div>
                                        <div class="fw-bold text-light">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $user->isAdmin() ? 'badge-role-admin' : 'badge-role-user' }}">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success bg-opacity-25 text-success small">
                                        <i class="bi bi-check-circle-fill me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-25 text-danger small">
                                        <i class="bi bi-x-circle-fill me-1"></i> Deactivated
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-25 text-light small">
                                    {{ $user->favoriteCategories->count() }} selected
                                </span>
                            </td>
                            <td class="small text-muted">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="text-end">
                                @if($user->id !== Auth::id())
                                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} py-1 px-3">
                                            @if($user->is_active)
                                                <i class="bi bi-person-x me-1"></i> Deactivate
                                            @else
                                                <i class="bi bi-person-check me-1"></i> Activate
                                            @endif
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-muted">Current Session</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No users found matching the given filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
