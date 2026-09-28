@extends('layouts.app')

@section('title', 'Character Lore Archives — Fan Hub Plus')

@section('content')
<div class="container py-4">
   
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <span class="small text-info fw-bold text-uppercase" style="letter-spacing: 0.08em;">Character Archives</span>
            <h1 class="h2 fw-bold text-light mb-1">Fandom Heroes & Villains</h1>
            <p class="text-muted small mb-0">Explore canonical biographies, abilities, and origins across universes</p>
        </div>
        <span class="badge bg-secondary bg-opacity-25 text-light">
            {{ $characters->total() }} Characters Cataloged
        </span>
    </div>

    
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('characters.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-subtle text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search character name or franchise..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <select name="category" class="form-select">
                    <option value="">All Fandoms</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="fh-btn-primary flex-fill">Filter</button>
                <a href="{{ route('characters.index') }}" class="fh-btn-ghost">Reset</a>
            </div>
        </form>
    </div>

    <!-- Characters Grid -->
    <div class="row g-4">
        @forelse($characters as $char)
            <div class="col-sm-6 col-lg-3">
                <x-character-card :character="$char" />
            </div>
        @empty
            <div class="col-12">
                <x-empty-state 
                    icon="bi-person-x" 
                    title="No Characters Found" 
                    message="No character profiles match your query. Try broadening your search or resetting filters."
                    actionText="View All Characters"
                    :actionUrl="route('characters.index')"
                />
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($characters->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $characters->links() }}
        </div>
    @endif
</div>
@endsection
