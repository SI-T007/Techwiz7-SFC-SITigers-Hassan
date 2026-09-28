@extends('layouts.app')

@section('title', 'Chatbot Knowledge Base CMS — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
   
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> PRIVILEGED</span>
                <h1 class="h2 fw-bold text-light mb-0">Chatbot Knowledge Base CMS</h1>
            </div>
            <p class="text-muted small mb-0">Manage verified Q&A pairs, keyword triggers, and platform guidance for the smart assistant</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.chatbot.knowledge.create') }}" class="btn btn-primary-gradient btn-sm">
                <i class="bi bi-plus-circle me-1"></i> New Knowledge Entry
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm text-light">
                <i class="bi bi-speedometer2 me-1"></i> Admin Console
            </a>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-4 col-md-4">
            <div class="fh-card p-3">
                <div class="small text-muted mb-1">Total Entries</div>
                <div class="h3 fw-bold text-light mb-0">{{ $metrics['total'] }}</div>
            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="fh-card p-3 border-success border-opacity-50">
                <div class="small text-success mb-1">Active Q&A</div>
                <div class="h3 fw-bold text-success mb-0">{{ $metrics['active'] }}</div>
            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="fh-card p-3 border-secondary border-opacity-50">
                <div class="small text-secondary mb-1">Inactive / Draft</div>
                <div class="h3 fw-bold text-secondary mb-0">{{ $metrics['inactive'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.chatbot.knowledge.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-dark border-secondary text-light" placeholder="Search by question, answer, or keywords...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Fandoms</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('admin.chatbot.knowledge.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Knowledge Table -->
    <div class="fh-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-muted small border-secondary border-opacity-25">
                        <th>Question / Keywords</th>
                        <th>Fandom Scope</th>
                        <th>Status</th>
                        <th>Helpful</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $kb)
                        <tr class="border-secondary border-opacity-25">
                            <td>
                                <div class="fw-semibold text-light">{{ $kb->question }}</div>
                                <div class="small text-muted text-truncate" style="max-width: 400px;">
                                    {{ Str::limit($kb->answer, 90) }}
                                </div>
                                @if(!empty($kb->keywords))
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        @foreach((array)$kb->keywords as $kw)
                                            <span class="badge bg-secondary bg-opacity-25 text-info" style="font-size: 0.65rem;">
                                                {{ $kw }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($kb->category)
                                    <span class="badge bg-info bg-opacity-25 text-info">
                                        {{ $kb->category->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-secondary">General Platform</span>
                                @endif
                            </td>
                            <td>
                                @if($kb->is_active)
                                    <span class="badge bg-success bg-opacity-25 text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="bi bi-hand-thumbs-up text-info me-1"></i> {{ $kb->helpful_count }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $kb->created_at->format('M d, Y') }}
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <form method="POST" action="{{ route('admin.chatbot.knowledge.toggle', $kb) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $kb->is_active ? 'warning' : 'success' }}" title="{{ $kb->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.chatbot.knowledge.edit', $kb) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.chatbot.knowledge.destroy', $kb) }}" onsubmit="return confirm('Delete this knowledge entry?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                                No knowledge entries found matching the filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($entries->hasPages())
            <div class="p-3 border-top border-secondary border-opacity-25 d-flex justify-content-center">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
