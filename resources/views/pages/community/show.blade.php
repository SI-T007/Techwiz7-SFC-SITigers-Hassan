@extends('layouts.app')

@section('title', $submission->title . ' — Fan Community — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="row g-4 justify-content-center">
        <!-- Main Creation Column -->
        <div class="col-lg-8">
            <article class="fh-card fh-card-glow p-4 p-md-5 mb-4">
                <!-- Header Badges -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fh-badge fh-badge-primary">
                            {{ ucwords(str_replace('_', ' ', $submission->type)) }}
                        </span>
                        <a href="{{ route('fandoms.show', $submission->category?->slug ?? 'all') }}" class="fh-badge fh-badge-cyan text-decoration-none">
                            {{ $submission->category?->name ?? 'General Fandom' }}
                        </a>
                        <span class="small text-muted">
                            <i class="bi bi-clock me-1"></i> {{ $submission->published_at ? $submission->published_at->format('M d, Y') : $submission->created_at->format('M d, Y') }}
                        </span>
                    </div>

                    @if(! $submission->isApproved())
                        <span class="badge bg-warning bg-opacity-25 text-warning">
                            <i class="bi bi-shield-exclamation me-1"></i> Preview Mode ({{ ucfirst($submission->status) }})
                        </span>
                    @endif
                </div>

                <h1 class="h2 fw-bold text-light mb-3">{{ $submission->title }}</h1>

                <!-- Submitter Profile Bar -->
                <div class="d-flex align-items-center gap-3 p-3 rounded-3 border border-subtle mb-4" style="background: rgba(255,255,255,0.02);">
                    <img src="{{ $submission->user?->avatar_url }}" alt="{{ $submission->user?->name }}" class="avatar-img" width="44" height="44" style="border: 2px solid var(--fh-primary);">
                    <div>
                        <div class="fw-semibold text-light">{{ $submission->user?->name ?? 'Fandom Creator' }}</div>
                        <div class="small text-muted">Verified Community Contributor</div>
                    </div>
                </div>

                <!-- Featured Image Showcase -->
                @if($submission->featured_image)
                    <div class="mb-4 rounded-3 overflow-hidden border border-subtle text-center" style="background: #000;">
                        <img src="{{ asset('storage/' . $submission->featured_image) }}" alt="{{ $submission->title }}" class="img-fluid" style="max-height: 600px; object-fit: contain;">
                    </div>
                @endif

                <!-- External Video Player if applicable -->
                @if($submission->external_url)
                    <div class="mb-4 rounded-3 overflow-hidden border border-subtle" style="background: #000;">
                        @if(Str::contains($submission->external_url, ['youtube.com', 'youtu.be']))
                            @php
                                $embedUrl = Str::replace('watch?v=', 'embed/', $submission->external_url);
                                $embedUrl = Str::replace('youtu.be/', 'youtube.com/embed/', $embedUrl);
                            @endphp
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ $embedUrl }}" title="{{ $submission->title }}" allowfullscreen></iframe>
                            </div>
                        @else
                            <div class="p-3 text-center">
                                <a href="{{ $submission->external_url }}" target="_blank" rel="noopener" class="fh-btn-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> View External Project Link &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Body / Creative Narrative -->
                @if($submission->body)
                    <div class="text-light mb-4" style="line-height: 1.85; font-size: 1.05rem;">
                        {!! nl2br(e($submission->body)) !!}
                    </div>
                @endif

                <!-- Footer Back Navigation -->
                <div class="pt-4 border-top border-subtle d-flex justify-content-between align-items-center">
                    <a href="{{ route('community.index') }}" class="fh-btn-ghost">
                        &larr; Back to Community Hub
                    </a>
                    <a href="{{ route('community.submit') }}" class="fh-btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Submit Your Creation
                    </a>
                </div>
            </article>
        </div>

        <!-- Sidebar / Related Creations -->
        <div class="col-lg-4">
            <div class="fh-card p-4 mb-4">
                <h3 class="h6 fw-bold text-light mb-3">
                    <i class="bi bi-collection text-info me-2"></i> More in {{ $submission->category?->name ?? 'Fandom' }}
                </h3>
                <div class="d-flex flex-column gap-3">
                    @forelse($relatedSubmissions as $rel)
                        <div class="p-2 rounded-3 border border-subtle" style="background: rgba(255,255,255,0.02);">
                            <div class="small fw-semibold mb-1">
                                <a href="{{ route('community.submissions.show', $rel->id) }}" class="text-light text-decoration-none">
                                    {{ $rel->title }}
                                </a>
                            </div>
                            <div class="small text-muted">
                                By {{ $rel->user?->name }} &bull; {{ ucwords(str_replace('_', ' ', $rel->type)) }}
                            </div>
                        </div>
                    @empty
                        <span class="small text-muted">No additional creations in this category yet.</span>
                    @endforelse
                </div>
            </div>

            <!-- Community Guidelines Card -->
            <div class="fh-card p-4">
                <h4 class="h6 fw-bold text-light mb-2">
                    <i class="bi bi-shield-check text-success me-2"></i> Creator Guidelines
                </h4>
                <ul class="small text-muted ps-3 mb-0">
                    <li>Original creations or credited adaptations only.</li>
                    <li>Respect community members and copyright boundaries.</li>
                    <li>All fan submissions are moderated by the platform team.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
