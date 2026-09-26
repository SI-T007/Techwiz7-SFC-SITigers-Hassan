@extends('layouts.app')

@section('title', $character->name . ' — Character Dossier & Power Matrix')

@push('styles')
<style>
    .fh-character-stat-card {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1rem;
        backdrop-filter: blur(12px);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="row g-4">
        <!-- Main Character Dossier -->
        <div class="col-lg-7">
            <div class="fh-card fh-card-glow p-4 p-md-5 h-100">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="fh-badge fh-badge-purple">
                        {{ $character->franchise ?: 'Canon Lore' }}
                    </span>
                    <a href="{{ route('fandoms.show', $character->category?->slug ?? 'all') }}" class="fh-badge fh-badge-cyan text-decoration-none">
                        {{ $character->category?->name }}
                    </a>
                </div>

                <h1 class="h2 fw-bold text-light mb-2">{{ $character->name }}</h1>

                @if($character->abilities)
                    <div class="p-3 rounded-3 border border-secondary border-opacity-25 mb-4" style="background: rgba(99, 102, 241, 0.08);">
                        <div class="small fw-bold text-info mb-1"><i class="bi bi-lightning-charge-fill me-1"></i> Special Abilities & Lore Attributes</div>
                        <div class="text-light small">{{ $character->abilities }}</div>
                    </div>
                @endif

                <h2 class="h5 fw-bold text-light mb-2">Canonical Biography</h2>
                <div class="text-light mb-4" style="line-height: 1.8;">
                    {!! nl2br(e($character->biography)) !!}
                </div>

                <div class="pt-3 border-top border-secondary border-opacity-25">
                    <a href="{{ route('characters.index') }}" class="fh-btn-ghost">
                        &larr; Back to Character Archives
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar / Character Radar Stats & Related Characters -->
        <div class="col-lg-5">
            <!-- 1. 360° Lore & Power Radar Matrix -->
            <div class="fh-character-stat-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 fw-bold text-light mb-0 text-uppercase" style="letter-spacing: 0.05em;">
                        <i class="bi bi-compass text-info me-2"></i>Power & Lore Matrix
                    </h3>
                    <span class="badge bg-info bg-opacity-25 text-info small">360° Radar</span>
                </div>
                <div style="position: relative; height: 230px; width: 100%;">
                    <canvas id="characterRadarChart"></canvas>
                </div>
                <div class="row g-2 mt-3 pt-3 border-top border-secondary border-opacity-25 text-center small">
                    @foreach($characterRadar as $attr => $score)
                        <div class="col-4">
                            <div class="fw-bold text-light">{{ $score }}/100</div>
                            <div class="text-muted" style="font-size: 0.68rem;">{{ $attr }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 2. Other Characters in this Universe -->
            <div class="fh-character-stat-card p-4">
                <h3 class="h6 fw-bold text-light mb-3 text-uppercase" style="letter-spacing: 0.05em;">
                    <i class="bi bi-people text-purple me-2"></i> Other {{ $character->category?->name }} Characters
                </h3>

                <div class="d-flex flex-column gap-3">
                    @forelse($relatedCharacters as $rel)
                        <div class="p-2.5 rounded-3 border border-secondary border-opacity-25" style="background: rgba(255,255,255,0.02);">
                            <div class="small fw-semibold">
                                <a href="{{ route('characters.show', $rel->slug) }}" class="text-light text-decoration-none hover-primary">
                                    {{ $rel->name }}
                                </a>
                            </div>
                            <div class="small text-muted text-truncate">{{ $rel->franchise ?: Str::limit($rel->biography, 50) }}</div>
                        </div>
                    @empty
                        <span class="small text-muted">No other characters recorded in this realm yet.</span>
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
    const radarCtx = document.getElementById('characterRadarChart');
    if (radarCtx) {
        new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: @json(array_keys($characterRadar)),
                datasets: [{
                    label: 'Power Matrix',
                    data: @json(array_values($characterRadar)),
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.28)',
                    borderWidth: 2,
                    pointBackgroundColor: '#8b5cf6',
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        min: 0,
                        max: 100,
                        grid: { color: 'rgba(255, 255, 255, 0.08)' },
                        angleLines: { color: 'rgba(255, 255, 255, 0.08)' },
                        pointLabels: { color: '#cbd5e1', font: { size: 9, weight: 'bold' } },
                        ticks: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>
@endpush
