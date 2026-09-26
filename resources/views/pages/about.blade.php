@extends('layouts.app')

@section('title', 'About the Platform & Architecture — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="row g-4 justify-content-center">
        <div class="col-lg-10">
            <div class="fh-card fh-card-glow p-4 p-md-5">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(99, 102, 241, 0.1);">
                    <span class="badge bg-primary rounded-pill px-2">ACADEMIC PROJECT</span>
                    <span class="small text-light">Full-Stack Laravel Engineering Demonstration</span>
                </div>

                <h1 class="display-6 fw-bold text-light mb-3">About Fan Hub Plus</h1>
                <p class="lead text-secondary mb-4 pb-3 border-bottom border-subtle">
                    Fan Hub Plus is an enterprise-grade, full-stack fandom platform architected to unify disparate pop culture universes into a single, cohesive, highly structured digital space.
                </p>

                <h2 class="h5 fw-bold text-light mb-3">Architectural Highlights</h2>
                <div class="row g-3 mb-5">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border border-subtle h-100" style="background: rgba(255,255,255,0.02);">
                            <h3 class="h6 fw-bold text-info mb-1"><i class="bi bi-diagram-3 me-2"></i> Clean MVC Architecture</h3>
                            <p class="small text-muted mb-0">Engineered strictly on Laravel 12 MVC patterns, robust Eloquent ORM relationships, and reusable Blade component hierarchies.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border border-subtle h-100" style="background: rgba(255,255,255,0.02);">
                            <h3 class="h6 fw-bold text-primary mb-1"><i class="bi bi-shield-lock me-2"></i> Multi-Role RBAC</h3>
                            <p class="small text-muted mb-0">Strict role clearance (Visitor, Registered Member, Administrator) guarded by dedicated middleware, session protection, and rate limiting.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border border-subtle h-100" style="background: rgba(255,255,255,0.02);">
                            <h3 class="h6 fw-bold text-purple mb-1"><i class="bi bi-database me-2"></i> Relational MySQL Core</h3>
                            <p class="small text-muted mb-0">Normalized relational database with polymorphic associations for bookmarks and ratings, indexing, and cascade rules.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border border-subtle h-100" style="background: rgba(255,255,255,0.02);">
                            <h3 class="h6 fw-bold text-success mb-1"><i class="bi bi-universal-access me-2"></i> Inclusive Accessibility</h3>
                            <p class="small text-muted mb-0">Native dark/light theme switching, high contrast modes, reduced-motion overrides, and semantic HTML conforming to WCAG standards.</p>
                        </div>
                    </div>
                </div>

                <h2 class="h5 fw-bold text-light mb-3">Technology Stack</h2>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="fh-badge fh-badge-primary">PHP 8.2+</span>
                    <span class="fh-badge fh-badge-primary">Laravel 12.x</span>
                    <span class="fh-badge fh-badge-cyan">MySQL 8.0</span>
                    <span class="fh-badge fh-badge-cyan">Bootstrap 5.3</span>
                    <span class="fh-badge fh-badge-purple">Blade Components</span>
                    <span class="fh-badge fh-badge-purple">Vanilla ES6+ JS</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
