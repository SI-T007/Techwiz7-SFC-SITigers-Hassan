@extends('layouts.app')

@section('title', 'Fandom Realms — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <span class="small text-info fw-bold text-uppercase" style="letter-spacing: 0.08em;">Categories Taxonomy</span>
            <h1 class="h2 fw-bold text-light mb-1">Fandom Universes</h1>
            <p class="text-muted small mb-0">Discover canon timelines, character biographies, and community submissions across each realm</p>
        </div>
        <span class="badge bg-secondary bg-opacity-25 text-light">
            {{ $categories->count() }} Fandoms Active
        </span>
    </div>

    <div class="row g-4">
        @foreach($categories as $category)
            <div class="col-sm-6 col-lg-3">
                <x-category-card :category="$category" :showCount="true" />
            </div>
        @endforeach
    </div>
</div>
@endsection
