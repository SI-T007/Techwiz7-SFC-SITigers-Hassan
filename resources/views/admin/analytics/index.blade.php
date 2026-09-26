@extends('layouts.app')

@section('title', 'Platform Analytics & Visual Intelligence Command — Fan Hub Plus')

@push('styles')
<style>
    :root {
        --fh-neon-cyan: #06b6d4;
        --fh-neon-purple: #8b5cf6;
        --fh-neon-emerald: #10b981;
        --fh-neon-amber: #f59e0b;
        --fh-neon-rose: #f43f5e;
        --fh-neon-indigo: #6366f1;
    }

    .fh-analytics-hero {
        background: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.15) 0%, rgba(15, 23, 42, 0) 50%),
                    radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.12) 0%, rgba(15, 23, 42, 0) 50%),
                    rgba(15, 23, 42, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 1.25rem;
        backdrop-filter: blur(16px);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.6);
    }

    .fh-analytics-card {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1rem;
        backdrop-filter: blur(12px);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .fh-analytics-card:hover {
        border-color: rgba(99, 102, 241, 0.35);
        box-shadow: 0 15px 35px -10px rgba(99, 102, 241, 0.2);
    }

    .fh-analytics-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.5), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .fh-analytics-card:hover::before {
        opacity: 1;
    }

    .fh-chart-container {
        position: relative;
        width: 100%;
        min-height: 260px;
    }

    .fh-kpi-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .fh-legend-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 8px currentColor;
    }

    .live-pulse {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse 1.8s infinite;
        display: inline-block;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.8); }
        70% { transform: scale(1.2); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .live-pulse-active {
        background-color: #ef4444;
        box-shadow: 0 0 0 rgba(239, 68, 68, 0.7);
        animation: pulseRed 1.2s infinite;
    }
    @keyframes pulseRed {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.8); }
        70% { transform: scale(1.25); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .btn-analytics-tab {
        background: rgba(255, 255, 255, 0.05);
        color: #94a3b8;
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.4rem 0.85rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }
    .btn-analytics-tab:hover {
        background: rgba(99, 102, 241, 0.15);
        color: #fff;
        border-color: rgba(99, 102, 241, 0.4);
    }
    .btn-analytics-tab.active {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
    }

    .stat-badge-delta {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 0.375rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .stat-badge-delta.positive {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .stat-badge-delta.negative {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .sparkline-bar {
        height: 6px;
        border-radius: 3px;
        background: rgba(255, 255, 255, 0.1);
        overflow: hidden;
        margin-top: 0.5rem;
    }
    .sparkline-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.6s ease;
    }

    .glass-pill-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #cbd5e1;
        padding: 0.25rem 0.6rem;
        font-size: 0.75rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .glass-pill-btn:hover, .glass-pill-btn.active {
        background: rgba(99, 102, 241, 0.25);
        border-color: rgba(99, 102, 241, 0.5);
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-lg-5 py-4">
    <!-- Hero Analytics Command Header -->
    <div class="fh-analytics-hero p-4 p-xl-5 mb-4 position-relative">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge badge-role-admin"><i class="bi bi-shield-lock-fill"></i> SYSTEM COMMAND</span>
                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25">
                        <span class="live-pulse me-1" id="liveStreamDot"></span>
                        <span id="liveStreamText">LIVE TELEMETRY</span>
                    </span>
                    <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25">
                        <i class="bi bi-cpu-fill me-1"></i> CHART ENGINE 4.4
                    </span>
                </div>
                <h1 class="display-6 fw-bold text-light mb-2">Platform Analytics & Visual Intelligence</h1>
                <p class="text-muted mb-0">
                    High-resolution dynamic telemetry, cross-universe engagement matrices, AI chatbot query distribution, and real-time community sentiment analysis.
                </p>
            </div>

            <!-- Controls: Range Selector, Universe Filter, Live Stream & Export -->
            <div class="col-lg-6">
                <div class="d-flex flex-column align-items-lg-end gap-3">
                    <!-- Date Range Switcher -->
                    <div class="d-flex flex-wrap align-items-center gap-1 bg-dark bg-opacity-50 p-1.5 rounded-3 border border-secondary border-opacity-25" id="rangeButtonContainer">
                        <button type="button" class="btn-analytics-tab {{ $range === 'today' ? 'active' : '' }}" onclick="switchRange('today')">Today</button>
                        <button type="button" class="btn-analytics-tab {{ $range === '7_days' ? 'active' : '' }}" onclick="switchRange('7_days')">7 Days</button>
                        <button type="button" class="btn-analytics-tab {{ $range === '30_days' ? 'active' : '' }}" onclick="switchRange('30_days')">30 Days</button>
                        <button type="button" class="btn-analytics-tab {{ $range === '90_days' ? 'active' : '' }}" onclick="switchRange('90_days')">90 Days</button>
                        <button type="button" class="btn-analytics-tab {{ $range === 'year' ? 'active' : '' }}" onclick="switchRange('year')">This Year</button>
                        <button type="button" class="btn-analytics-tab {{ $range === 'all_time' ? 'active' : '' }}" onclick="switchRange('all_time')">All Time</button>
                    </div>

                    <!-- Toolbar: Universe Filter, Live Stream Mode Toggle, Export Hub -->
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <!-- Universe Category Filter -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle text-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="universeFilterBtn">
                                <i class="bi bi-grid-3x3-gap-fill text-info me-1"></i>
                                <span id="currentUniverseLabel">{{ $selectedCategoryId ? ($categories->firstWhere('id', $selectedCategoryId)?->name ?? 'Filtered Universe') : 'All Fandoms' }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow">
                                <li><a class="dropdown-item {{ !$selectedCategoryId ? 'active' : '' }}" href="javascript:void(0)" onclick="filterCategory(null, 'All Fandoms')">🌟 All Fandom Universes</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach($allCategoriesList as $catItem)
                                    <li>
                                        <a class="dropdown-item {{ $selectedCategoryId == $catItem->id ? 'active' : '' }}" href="javascript:void(0)" onclick="filterCategory({{ $catItem->id }}, '{{ addslashes($catItem->name) }}')">
                                            {{ $catItem->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Live Telemetry Stream Simulation Toggle -->
                        <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" id="toggleLiveStreamBtn" onclick="toggleLiveStream()">
                            <i class="bi bi-broadcast"></i>
                            <span id="liveBtnLabel">Start Live Stream</span>
                        </button>

                        <!-- Manual Refresh -->
                        <button type="button" class="btn btn-sm btn-outline-light" title="Refresh Telemetry" onclick="refreshData()" id="refreshTelemetryBtn">
                            <i class="bi bi-arrow-clockwise" id="refreshSpinner"></i>
                        </button>

                        <!-- Export Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary-gradient dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download me-1"></i> Export Telemetry
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportAnalyticsJSON()"><i class="bi bi-file-earmark-code text-info me-2"></i> Export as JSON</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportAnalyticsCSV()"><i class="bi bi-file-earmark-spreadsheet text-success me-2"></i> Export as CSV</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="window.print()"><i class="bi bi-printer text-warning me-2"></i> Print / PDF Report</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tier 1: Real-Time Executive KPI Velocity Cards with Sparkline Indicators -->
    <div class="row g-3 mb-4">
        <!-- 1. Registered Community Members -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="fh-analytics-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Total Users</span>
                    <span class="fh-kpi-pill bg-purple bg-opacity-25 text-purple border border-purple border-opacity-25">
                        <i class="bi bi-people-fill"></i>
                    </span>
                </div>
                <div class="h3 fw-bold text-light mb-1" id="kpiTotalUsers">{{ number_format($totalUsers) }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small" style="font-size: 0.72rem;">+<span id="kpiNewUsers">{{ $newUsersInRange }}</span> in window</span>
                    <span class="stat-badge-delta {{ $userGrowthPct >= 0 ? 'positive' : 'negative' }}" id="kpiUserDelta">
                        <i class="bi {{ $userGrowthPct >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i>
                        {{ abs($userGrowthPct) }}%
                    </span>
                </div>
                <div class="sparkline-bar">
                    <div class="sparkline-fill bg-purple" style="width: {{ min(100, max(15, ($totalUsers > 0 ? ($newUsersInRange / max(1, $totalUsers)) * 100 : 20))) }}%;"></div>
                </div>
            </div>
        </div>

        <!-- 2. Active Platform Participants -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="fh-analytics-card p-3 h-100 border-success border-opacity-40">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-success small text-uppercase fw-semibold" style="font-size: 0.7rem;">Active Fans</span>
                    <span class="fh-kpi-pill bg-success bg-opacity-25 text-success border border-success border-opacity-25">
                        <i class="bi bi-activity"></i>
                    </span>
                </div>
                <div class="h3 fw-bold text-success mb-1" id="kpiActiveUsers">{{ number_format($activeUsersCount) }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small" style="font-size: 0.72rem;">Active Engagement</span>
                    <span class="badge bg-success bg-opacity-25 text-success" style="font-size: 0.7rem;">
                        {{ $totalUsers > 0 ? round(($activeUsersCount / $totalUsers) * 100, 1) : 100 }}% DAU/MAU
                    </span>
                </div>
                <div class="sparkline-bar">
                    <div class="sparkline-fill bg-success" style="width: {{ $totalUsers > 0 ? min(100, round(($activeUsersCount / $totalUsers) * 100)) : 75 }}%;"></div>
                </div>
            </div>
        </div>

        <!-- 3. AI Assistant Inquiries & Intelligence -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="fh-analytics-card p-3 h-100 border-info border-opacity-40">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-info small text-uppercase fw-semibold" style="font-size: 0.7rem;">AI Bot Messages</span>
                    <span class="fh-kpi-pill bg-info bg-opacity-25 text-info border border-info border-opacity-25">
                        <i class="bi bi-robot"></i>
                    </span>
                </div>
                <div class="h3 fw-bold text-info mb-1" id="kpiBotMessages">{{ number_format($messagesInRange) }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small" style="font-size: 0.72rem;">{{ $conversationsInRange }} threads</span>
                    <span class="stat-badge-delta {{ $botGrowthPct >= 0 ? 'positive' : 'negative' }}">
                        <i class="bi {{ $botGrowthPct >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i>
                        {{ abs($botGrowthPct) }}%
                    </span>
                </div>
                <div class="sparkline-bar">
                    <div class="sparkline-fill bg-info" style="width: 82%;"></div>
                </div>
            </div>
        </div>

        <!-- 4. Community Sentiment & 5-Star Rating Score -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="fh-analytics-card p-3 h-100 border-warning border-opacity-40">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-warning small text-uppercase fw-semibold" style="font-size: 0.7rem;">Sentiment Score</span>
                    <span class="fh-kpi-pill bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25">
                        <i class="bi bi-star-fill"></i>
                    </span>
                </div>
                <div class="h3 fw-bold text-warning mb-1" id="kpiAvgRating">{{ number_format($avgRating, 2) }} <span class="fs-6 text-muted">/ 5.0</span></div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small" style="font-size: 0.72rem;">{{ number_format($totalRatings) }} fan reviews</span>
                    <span class="badge bg-warning bg-opacity-25 text-warning" style="font-size: 0.7rem;">
                        {{ round(($avgRating / 5.0) * 100, 1) }}% Index
                    </span>
                </div>
                <div class="sparkline-bar">
                    <div class="sparkline-fill bg-warning" style="width: {{ round(($avgRating / 5.0) * 100) }}%;"></div>
                </div>
            </div>
        </div>

        <!-- 5. Total Platform Activities -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="fh-analytics-card p-3 h-100 border-primary border-opacity-40">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-primary small text-uppercase fw-semibold" style="font-size: 0.7rem;">Total Actions</span>
                    <span class="fh-kpi-pill bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </span>
                </div>
                <div class="h3 fw-bold text-primary mb-1" id="kpiTotalActions">{{ number_format($totalActivitiesInRange) }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small" style="font-size: 0.72rem;">Velocity Stream</span>
                    <span class="stat-badge-delta {{ $activityGrowthPct >= 0 ? 'positive' : 'negative' }}">
                        <i class="bi {{ $activityGrowthPct >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i>
                        {{ abs($activityGrowthPct) }}%
                    </span>
                </div>
                <div class="sparkline-bar">
                    <div class="sparkline-fill bg-primary" style="width: 88%;"></div>
                </div>
            </div>
        </div>

        <!-- 6. Saved Fan Bookmarks -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="fh-analytics-card p-3 h-100 border-rose border-opacity-40">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-light small text-uppercase fw-semibold" style="font-size: 0.7rem; color: #f43f5e !important;">Bookmarks</span>
                    <span class="fh-kpi-pill bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25">
                        <i class="bi bi-bookmark-heart-fill"></i>
                    </span>
                </div>
                <div class="h3 fw-bold mb-1" style="color: #f43f5e;" id="kpiTotalBookmarks">{{ number_format($totalBookmarks) }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small" style="font-size: 0.72rem;">Saved Lore Items</span>
                    <span class="badge bg-danger bg-opacity-25 text-danger" style="font-size: 0.7rem;">High Affinity</span>
                </div>
                <div class="sparkline-bar">
                    <div class="sparkline-fill" style="background: #f43f5e; width: 65%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tier 2: Primary Dynamic Multi-Series Platform Velocity Timeline Chart -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="fh-analytics-card p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h2 class="h5 fw-bold text-light mb-0">
                                <i class="bi bi-graph-up text-primary me-2"></i>Platform Velocity & Engagement Trajectory
                            </h2>
                            <span class="badge bg-primary bg-opacity-25 text-primary small" id="chartRangeBadge">{{ strtoupper(str_replace('_', ' ', $range)) }}</span>
                        </div>
                        <p class="text-muted small mb-0">
                            Continuous multi-channel telemetry tracking platform interactions, AI inquiries, member signups, bookmarks, and fan ratings.
                        </p>
                    </div>

                    <!-- Interactive Chart Controls: Chart Type, Curve Tension & Dataset Toggles -->
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <!-- Chart Type Buttons -->
                        <div class="btn-group btn-group-sm" role="group" aria-label="Chart Type">
                            <button type="button" class="glass-pill-btn active" id="btnChartTypeLine" onclick="setTimelineType('line')"><i class="bi bi-bezier2 me-1"></i> Smooth Area</button>
                            <button type="button" class="glass-pill-btn" id="btnChartTypeBar" onclick="setTimelineType('bar')"><i class="bi bi-bar-chart-fill me-1"></i> Bar Stack</button>
                        </div>

                        <!-- Dataset Filter Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle text-light" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-sliders me-1"></i> Visible Series
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow p-2" style="min-width: 220px;">
                                <li>
                                    <label class="dropdown-item d-flex align-items-center gap-2 cursor-pointer">
                                        <input type="checkbox" class="form-check-input mt-0" id="chkSeriesActivities" checked onchange="toggleDataset(0, this.checked)">
                                        <span class="fh-legend-indicator" style="background: #10b981;"></span> Platform Activities
                                    </label>
                                </li>
                                <li>
                                    <label class="dropdown-item d-flex align-items-center gap-2 cursor-pointer">
                                        <input type="checkbox" class="form-check-input mt-0" id="chkSeriesMessages" checked onchange="toggleDataset(1, this.checked)">
                                        <span class="fh-legend-indicator" style="background: #06b6d4;"></span> Chatbot Queries
                                    </label>
                                </li>
                                <li>
                                    <label class="dropdown-item d-flex align-items-center gap-2 cursor-pointer">
                                        <input type="checkbox" class="form-check-input mt-0" id="chkSeriesRegistrations" checked onchange="toggleDataset(2, this.checked)">
                                        <span class="fh-legend-indicator" style="background: #8b5cf6;"></span> New Registrations
                                    </label>
                                </li>
                                <li>
                                    <label class="dropdown-item d-flex align-items-center gap-2 cursor-pointer">
                                        <input type="checkbox" class="form-check-input mt-0" id="chkSeriesBookmarks" checked onchange="toggleDataset(3, this.checked)">
                                        <span class="fh-legend-indicator" style="background: #f43f5e;"></span> Bookmarks
                                    </label>
                                </li>
                                <li>
                                    <label class="dropdown-item d-flex align-items-center gap-2 cursor-pointer">
                                        <input type="checkbox" class="form-check-input mt-0" id="chkSeriesRatings" checked onchange="toggleDataset(4, this.checked)">
                                        <span class="fh-legend-indicator" style="background: #f59e0b;"></span> Ratings
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Main Canvas -->
                <div class="fh-chart-container" style="height: 350px;">
                    <canvas id="telemetryTimelineChart"></canvas>
                </div>

                <!-- Chart Interactive Legend & Quick Metrics Strip -->
                <div class="row g-2 mt-3 pt-3 border-top border-secondary border-opacity-25 text-center small">
                    <div class="col-6 col-md-2">
                        <div class="text-success fw-bold fs-6" id="summaryActivitiesTotal">{{ number_format(array_sum($timeline['activities'] ?? [])) }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;"><span class="fh-legend-indicator me-1" style="background: #10b981;"></span> Activities</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="text-info fw-bold fs-6" id="summaryMessagesTotal">{{ number_format(array_sum($timeline['messages'] ?? [])) }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;"><span class="fh-legend-indicator me-1" style="background: #06b6d4;"></span> Bot Messages</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="text-purple fw-bold fs-6" id="summaryRegistrationsTotal">{{ number_format(array_sum($timeline['registrations'] ?? [])) }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;"><span class="fh-legend-indicator me-1" style="background: #8b5cf6;"></span> Registrations</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="fw-bold fs-6" style="color: #f43f5e;" id="summaryBookmarksTotal">{{ number_format(array_sum($timeline['bookmarks'] ?? [])) }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;"><span class="fh-legend-indicator me-1" style="background: #f43f5e;"></span> Bookmarks</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="text-warning fw-bold fs-6" id="summaryRatingsTotal">{{ number_format(array_sum($timeline['ratings'] ?? [])) }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;"><span class="fh-legend-indicator me-1" style="background: #f59e0b;"></span> Ratings</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="text-light fw-bold fs-6" id="summaryVelocityRatio">
                            {{ round((array_sum($timeline['activities'] ?? []) + array_sum($timeline['messages'] ?? [])) / max(1, count($timeline['labels'] ?? [1])), 1) }}/pt
                        </div>
                        <div class="text-muted" style="font-size: 0.72rem;">Avg Velocity / Pt</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tier 3: Fandom Universe Matrix & AI Knowledge Intelligence Engine -->
    <div class="row g-4 mb-4">
        <!-- 1. Fandom Universe 360° Lore Matrix (Grouped / Stacked Bar / Radar Switcher) -->
        <div class="col-lg-7">
            <div class="fh-analytics-card p-4 h-100">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h2 class="h5 fw-bold text-light mb-1">
                            <i class="bi bi-trophy-fill text-warning me-2"></i>Fandom Universe 360° Lore Matrix
                        </h2>
                        <p class="text-muted small mb-0">Multi-dimensional lore index across articles, character bios, events, releases, and fan works.</p>
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="glass-pill-btn active" id="btnCategoryBar" onclick="setCategoryChartType('bar')"><i class="bi bi-bar-chart"></i> Stacked Bar</button>
                        <button type="button" class="glass-pill-btn" id="btnCategoryRadar" onclick="setCategoryChartType('radar')"><i class="bi bi-compass"></i> Radar 360°</button>
                    </div>
                </div>
                <div class="fh-chart-container" style="height: 330px;">
                    <canvas id="categoryEngagementChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 2. AI Assistant Intelligence & Provenance Engine (Donut / Polar) -->
        <div class="col-lg-5">
            <div class="fh-analytics-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h5 fw-bold text-light mb-1">
                            <i class="bi bi-cpu-fill text-info me-2"></i>AI Assistant Provenance
                        </h2>
                        <p class="text-muted small mb-0">Response source resolution across Knowledge Base, Database, and Generative AI.</p>
                    </div>
                    <span class="badge bg-info bg-opacity-25 text-info small">{{ number_format($totalMessages) }} Total Pings</span>
                </div>
                <div class="fh-chart-container position-relative" style="height: 250px;">
                    <canvas id="chatbotSourceDonutChart"></canvas>
                    <!-- Center Metric Overlay -->
                    <div class="position-absolute top-50 start-50 translate-middle text-center pointer-events-none" style="pointer-events: none;">
                        <div class="display-6 fw-bold text-light" id="kbAccuracyScore">
                            {{ $totalMessages > 0 ? round((($sourceBreakdown['knowledge_base'] + $sourceBreakdown['database']) / max(1, $totalMessages)) * 100) : 94 }}%
                        </div>
                        <div class="text-muted small" style="font-size: 0.7rem;">Verified Hit Rate</div>
                    </div>
                </div>
                <div class="row g-2 mt-3 pt-3 border-top border-secondary border-opacity-25 text-center small">
                    <div class="col-6 col-md-3">
                        <div class="text-info fw-bold" id="srcKbCount">{{ $sourceBreakdown['knowledge_base'] }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Lore Base</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-success fw-bold" id="srcDbCount">{{ $sourceBreakdown['database'] }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">DB Queries</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-primary fw-bold" id="srcAiCount">{{ $sourceBreakdown['ai'] }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Generative</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-secondary fw-bold" id="srcFallbackCount">{{ $sourceBreakdown['fallback'] }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Fallback</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tier 4: Peak 24-Hour Activity Heatmap, Sentiment Rating, and Content Formats -->
    <div class="row g-4 mb-4">
        <!-- 1. 24-Hour Peak Engagement Heatmap / Time of Day -->
        <div class="col-md-6 col-lg-4">
            <div class="fh-analytics-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h3 class="h6 fw-bold text-light mb-0 text-uppercase" style="letter-spacing: 0.05em;">
                        <i class="bi bi-clock-history text-primary me-2"></i>Peak Activity Heatmap
                    </h3>
                    <span class="badge bg-primary bg-opacity-25 text-primary small">24h Spectrum</span>
                </div>
                <p class="text-muted small mb-3">Hourly fan activity concentration across universal time (00:00 to 23:00).</p>
                <div class="fh-chart-container" style="height: 220px;">
                    <canvas id="hourlyActivityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 2. Community Sentiment Score & 5-Star Distribution -->
        <div class="col-md-6 col-lg-4">
            <div class="fh-analytics-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h3 class="h6 fw-bold text-light mb-0 text-uppercase" style="letter-spacing: 0.05em;">
                        <i class="bi bi-star-half text-warning me-2"></i>Sentiment & Ratings
                    </h3>
                    <span class="badge bg-warning bg-opacity-25 text-warning small">{{ number_format($avgRating, 1) }}★ Avg</span>
                </div>
                <p class="text-muted small mb-3">Community quality appreciation ranked from 1-star to 5-star reviews.</p>
                <div class="fh-chart-container" style="height: 220px;">
                    <canvas id="ratingDistributionBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 3. Content Format Ecosystem -->
        <div class="col-md-12 col-lg-4">
            <div class="fh-analytics-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h3 class="h6 fw-bold text-light mb-0 text-uppercase" style="letter-spacing: 0.05em;">
                        <i class="bi bi-pie-chart-fill text-info me-2"></i>Content Formats
                    </h3>
                    <span class="badge bg-secondary bg-opacity-25 text-light small">{{ array_sum($contentFormats) }} Hubs</span>
                </div>
                <p class="text-muted small mb-3">Distribution of articles, videos, soundtrack audio, wallpapers, and trailers.</p>
                <div class="fh-chart-container" style="height: 220px;">
                    <canvas id="contentFormatDonutChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tier 5: Media Quality HD Bandwidth, Moderation Queue Flow & Feedback Support -->
    <div class="row g-4 mb-4">
        <!-- 1. Media HD Resolution & Bandwidth Library -->
        <div class="col-md-6 col-lg-4">
            <div class="fh-analytics-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h3 class="h6 fw-bold text-light mb-0 text-uppercase" style="letter-spacing: 0.05em;">
                        <i class="bi bi-badge-hd-fill text-cyan me-2"></i>Media Quality Library
                    </h3>
                    <span class="badge bg-info bg-opacity-25 text-info small">HD Telemetry</span>
                </div>
                <p class="text-muted small mb-3">Breakdown of 4K UHD, 1080p Full HD, 720p HD, and standard stream assets.</p>
                <div class="fh-chart-container" style="height: 220px;">
                    <canvas id="mediaQualityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 2. Community Fan Works Moderation Pipeline -->
        <div class="col-md-6 col-lg-4">
            <div class="fh-analytics-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h3 class="h6 fw-bold text-light mb-0 text-uppercase" style="letter-spacing: 0.05em;">
                        <i class="bi bi-shield-check text-success me-2"></i>Moderation Lifecycle
                    </h3>
                    <span class="badge bg-success bg-opacity-25 text-success small">
                        {{ $submissionsCount['total'] > 0 ? round(($submissionsCount['approved'] / $submissionsCount['total']) * 100) : 100 }}% Approval
                    </span>
                </div>
                <p class="text-muted small mb-3">Pipeline throughput of fan art, stories, theories, and cosplay submissions.</p>
                <div class="fh-chart-container" style="height: 220px;">
                    <canvas id="moderationDonutChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 3. User Demographics & Support Supervision -->
        <div class="col-md-12 col-lg-4">
            <div class="fh-analytics-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h3 class="h6 fw-bold text-light mb-0 text-uppercase" style="letter-spacing: 0.05em;">
                        <i class="bi bi-person-badge-fill text-purple me-2"></i>Demographics & Roles
                    </h3>
                    <span class="badge bg-secondary bg-opacity-25 text-light small">{{ $totalUsers }} Total</span>
                </div>
                <p class="text-muted small mb-3">Ratio of administrative stewards, active verified members, and inactive accounts.</p>
                <div class="fh-chart-container" style="height: 220px;">
                    <canvas id="userStatusDonutChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tier 6: Complete Interactive Fandom Universe Performance Ledger -->
    <div class="fh-analytics-card p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h3 class="h6 fw-bold text-light mb-1 text-uppercase" style="letter-spacing: 0.05em;">
                    <i class="bi bi-table text-primary me-2"></i>Comprehensive Universe Performance Ledger
                </h3>
                <p class="text-muted small mb-0">Full cross-metric rankings with dynamic engagement scores and lore density.</p>
            </div>
            <span class="badge bg-secondary bg-opacity-25 text-light small" id="tableUniverseCount">{{ $categories->count() }} Universe Categories</span>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" id="universeTable">
                <thead class="text-muted small text-uppercase">
                    <tr>
                        <th style="width: 50px;">Rank</th>
                        <th>Fandom Universe</th>
                        <th>Articles</th>
                        <th>Characters</th>
                        <th>Events Radar</th>
                        <th>Releases</th>
                        <th>Fan Works</th>
                        <th>Media</th>
                        <th style="min-width: 140px;">Engagement Matrix</th>
                        <th class="text-end">Total Score</th>
                    </tr>
                </thead>
                <tbody id="universeTableBody">
                    @php $maxEngagement = $categories->max('engagement_score') ?: 1; @endphp
                    @foreach($categories as $index => $cat)
                        <tr>
                            <td>
                                @if($index === 0)
                                    <span class="badge bg-warning text-dark fw-bold rounded-pill">#1</span>
                                @elseif($index === 1)
                                    <span class="badge bg-light text-dark fw-bold rounded-pill">#2</span>
                                @elseif($index === 2)
                                    <span class="badge bg-secondary text-light fw-bold rounded-pill">#3</span>
                                @else
                                    <span class="text-muted fw-bold">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi {{ $cat->icon ?: 'bi-stars' }} text-primary fs-5"></i>
                                    <div>
                                        <a href="{{ route('fandoms.show', $cat->slug) }}" class="fw-bold text-light text-decoration-none hover-primary">
                                            {{ $cat->name }}
                                        </a>
                                        <div class="text-muted small" style="font-size: 0.72rem;">{{ Str::limit($cat->description, 45) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary bg-opacity-25 text-light">{{ $cat->contents_count }}</span></td>
                            <td><span class="badge bg-info bg-opacity-25 text-info">{{ $cat->characters_count }}</span></td>
                            <td><span class="badge bg-secondary bg-opacity-25 text-light">{{ $cat->events_count }}</span></td>
                            <td><span class="badge bg-secondary bg-opacity-25 text-light">{{ $cat->releases_count }}</span></td>
                            <td><span class="badge bg-warning bg-opacity-25 text-warning">{{ $cat->submissions_count }}</span></td>
                            <td><span class="badge bg-cyan bg-opacity-25 text-cyan" style="color: #06b6d4;">{{ $cat->media_count }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; background: rgba(255,255,255,0.08);">
                                        <div class="progress-bar bg-gradient" style="background: linear-gradient(90deg, #6366f1, #06b6d4); width: {{ round(($cat->engagement_score / $maxEngagement) * 100) }}%;"></div>
                                    </div>
                                    <span class="text-muted small" style="font-size: 0.7rem;">{{ round(($cat->engagement_score / $maxEngagement) * 100) }}%</span>
                                </div>
                            </td>
                            <td class="text-end fw-bold text-warning fs-6">{{ number_format($cat->engagement_score) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Load Chart.js 4.4 CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<script>
// State Management for Analytics Engine
let currentRange = @json($range);
let currentCategoryId = @json($selectedCategoryId);
let currentTimelineType = 'line';
let currentCategoryChartType = 'bar';
let liveStreamInterval = null;
let isLiveStreaming = false;

// Chart Instances Global Registry
let chartTimeline = null;
let chartCategory = null;
let chartChatbotSource = null;
let chartHourly = null;
let chartRating = null;
let chartContentFormat = null;
let chartMediaQuality = null;
let chartModeration = null;
let chartUserStatus = null;

// Initial Datasets from Server
let initialTimelineData = @json($timeline);
let initialCategoryData = @json($categoryChartData);
let initialSourceData = @json($sourceBreakdown);
let initialHourlyData = @json($hourlyActivities);
let initialStarData = @json($starDistribution);
let initialFormatData = @json($contentFormats);
let initialMediaData = @json($mediaQualities);
let initialSubmissionsData = @json($submissionsCount);
let initialUserData = {
    regular: {{ $regularUserCount }},
    admin: {{ $adminCount }},
    inactive: {{ $inactiveUsersCount }}
};

document.addEventListener('DOMContentLoaded', function() {
    // Apply Global Dark Theme Styling for Chart.js
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15, 23, 42, 0.95)';
    Chart.defaults.plugins.tooltip.titleColor = '#f8fafc';
    Chart.defaults.plugins.tooltip.bodyColor = '#cbd5e1';
    Chart.defaults.plugins.tooltip.borderColor = 'rgba(255, 255, 255, 0.15)';
    Chart.defaults.plugins.tooltip.borderWidth = 1;
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.usePointStyle = true;

    // Initialize All Dynamic Charts
    initTimelineChart(initialTimelineData);
    initCategoryChart(initialCategoryData, currentCategoryChartType);
    initChatbotSourceChart(initialSourceData);
    initHourlyChart(initialHourlyData);
    initRatingChart(initialStarData);
    initContentFormatChart(initialFormatData);
    initMediaQualityChart(initialMediaData);
    initModerationChart(initialSubmissionsData);
    initUserStatusChart(initialUserData);
});

// 1. Timeline Chart Initialization
function initTimelineChart(data) {
    const ctx = document.getElementById('telemetryTimelineChart');
    if (!ctx) return;

    if (chartTimeline) chartTimeline.destroy();

    const isLine = currentTimelineType === 'line';

    chartTimeline = new Chart(ctx, {
        type: isLine ? 'line' : 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Platform Activities',
                    data: data.activities,
                    borderColor: '#10b981',
                    backgroundColor: isLine ? 'rgba(16, 185, 129, 0.18)' : '#10b981',
                    borderWidth: 2.5,
                    fill: isLine,
                    tension: 0.38,
                    pointBackgroundColor: '#10b981',
                    pointRadius: data.labels.length > 20 ? 2 : 4,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Chatbot Queries',
                    data: data.messages,
                    borderColor: '#06b6d4',
                    backgroundColor: isLine ? 'rgba(6, 182, 212, 0.14)' : '#06b6d4',
                    borderWidth: 2.2,
                    fill: isLine,
                    tension: 0.38,
                    pointBackgroundColor: '#06b6d4',
                    pointRadius: data.labels.length > 20 ? 2 : 4,
                    pointHoverRadius: 6,
                },
                {
                    label: 'New Registrations',
                    data: data.registrations,
                    borderColor: '#8b5cf6',
                    backgroundColor: isLine ? 'rgba(139, 92, 246, 0.12)' : '#8b5cf6',
                    borderWidth: 2,
                    fill: isLine,
                    tension: 0.38,
                    pointBackgroundColor: '#8b5cf6',
                    pointRadius: data.labels.length > 20 ? 2 : 4,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Saved Bookmarks',
                    data: data.bookmarks || [],
                    borderColor: '#f43f5e',
                    backgroundColor: isLine ? 'rgba(244, 63, 94, 0.10)' : '#f43f5e',
                    borderWidth: 2,
                    fill: isLine,
                    tension: 0.38,
                    pointBackgroundColor: '#f43f5e',
                    pointRadius: data.labels.length > 20 ? 2 : 4,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Fan Ratings',
                    data: data.ratings || [],
                    borderColor: '#f59e0b',
                    backgroundColor: isLine ? 'rgba(245, 158, 11, 0.10)' : '#f59e0b',
                    borderWidth: 2,
                    fill: isLine,
                    tension: 0.38,
                    pointBackgroundColor: '#f59e0b',
                    pointRadius: data.labels.length > 20 ? 2 : 4,
                    pointHoverRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { maxRotation: 45, autoSkip: true, maxTicksLimit: 14 }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

// 2. Fandom Universe Matrix Chart Initialization
function initCategoryChart(data, type) {
    const ctx = document.getElementById('categoryEngagementChart');
    if (!ctx) return;

    if (chartCategory) chartCategory.destroy();

    if (type === 'radar') {
        chartCategory = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Lore Articles',
                        data: data.contents,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.25)',
                        borderWidth: 2,
                        pointBackgroundColor: '#6366f1'
                    },
                    {
                        label: 'Character Lore',
                        data: data.characters,
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.25)',
                        borderWidth: 2,
                        pointBackgroundColor: '#06b6d4'
                    },
                    {
                        label: 'Fan Submissions',
                        data: data.submissions,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.25)',
                        borderWidth: 2,
                        pointBackgroundColor: '#f59e0b'
                    },
                    {
                        label: 'Multimedia',
                        data: data.media || [],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.25)',
                        borderWidth: 2,
                        pointBackgroundColor: '#10b981'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        grid: { color: 'rgba(255, 255, 255, 0.08)' },
                        angleLines: { color: 'rgba(255, 255, 255, 0.08)' },
                        pointLabels: { color: '#e2e8f0', font: { size: 11, weight: 'bold' } },
                        ticks: { display: false }
                    }
                },
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 12, padding: 12 } }
                }
            }
        });
    } else {
        // Stacked Bar
        chartCategory = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    { label: 'Lore Articles', data: data.contents, backgroundColor: '#6366f1', borderRadius: 4 },
                    { label: 'Character Lore', data: data.characters, backgroundColor: '#06b6d4', borderRadius: 4 },
                    { label: 'Events Radar', data: data.events, backgroundColor: '#8b5cf6', borderRadius: 4 },
                    { label: 'Releases', data: data.releases, backgroundColor: '#ec4899', borderRadius: 4 },
                    { label: 'Fan Submissions', data: data.submissions, backgroundColor: '#f59e0b', borderRadius: 4 },
                    { label: 'Multimedia', data: data.media || [], backgroundColor: '#10b981', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { maxRotation: 30, autoSkip: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    }
                },
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 10, padding: 10 } }
                }
            }
        });
    }
}

// 3. AI Provenance Donut Chart
function initChatbotSourceChart(data) {
    const ctx = document.getElementById('chatbotSourceDonutChart');
    if (!ctx) return;
    if (chartChatbotSource) chartChatbotSource.destroy();

    chartChatbotSource = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Knowledge Base', 'Database Query', 'Generative AI', 'Fallback'],
            datasets: [{
                data: [data.knowledge_base || 0, data.database || 0, data.ai || 0, data.fallback || 0],
                backgroundColor: ['#06b6d4', '#10b981', '#6366f1', '#64748b'],
                borderColor: 'rgba(15, 23, 42, 0.95)',
                borderWidth: 3,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false }
            }
        }
    });
}

// 4. Hourly Activity Heatmap
function initHourlyChart(data) {
    const ctx = document.getElementById('hourlyActivityChart');
    if (!ctx) return;
    if (chartHourly) chartHourly.destroy();

    const hourLabels = ['00h','01h','02h','03h','04h','05h','06h','07h','08h','09h','10h','11h','12h','13h','14h','15h','16h','17h','18h','19h','20h','21h','22h','23h'];

    chartHourly = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: hourLabels,
            datasets: [{
                label: 'Activity Density',
                data: data,
                backgroundColor: function(context) {
                    const value = context.dataset.data[context.dataIndex];
                    const max = Math.max(...context.dataset.data, 1);
                    const alpha = Math.max(0.25, (value / max));
                    return `rgba(99, 102, 241, ${alpha})`;
                },
                borderColor: '#6366f1',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { maxTicksLimit: 8, font: { size: 10 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { precision: 0 }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
}

// 5. 5-Star Sentiment Rating Distribution Chart
function initRatingChart(data) {
    const ctx = document.getElementById('ratingDistributionBarChart');
    if (!ctx) return;
    if (chartRating) chartRating.destroy();

    chartRating = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
            datasets: [{
                label: 'Ratings Count',
                data: [data['5_star'] || 0, data['4_star'] || 0, data['3_star'] || 0, data['2_star'] || 0, data['1_star'] || 0],
                backgroundColor: ['#f59e0b', '#fbbf24', '#fde047', '#94a3b8', '#ef4444'],
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' }
                },
                y: {
                    grid: { display: false }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
}

// 6. Content Formats Chart
function initContentFormatChart(data) {
    const ctx = document.getElementById('contentFormatDonutChart');
    if (!ctx) return;
    if (chartContentFormat) chartContentFormat.destroy();

    chartContentFormat = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Articles', '4K Videos', 'Soundtracks', 'Wallpapers', 'Trailers'],
            datasets: [{
                data: [data.article || 0, data.video || 0, data.audio || 0, data.image || 0, data.trailer || 0],
                backgroundColor: ['#6366f1', '#ef4444', '#10b981', '#8b5cf6', '#06b6d4'],
                borderColor: 'rgba(15, 23, 42, 0.95)',
                borderWidth: 2,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'right', labels: { boxWidth: 10, padding: 8, font: { size: 11 } } }
            }
        }
    });
}

// 7. Media HD Quality Chart
function initMediaQualityChart(data) {
    const ctx = document.getElementById('mediaQualityChart');
    if (!ctx) return;
    if (chartMediaQuality) chartMediaQuality.destroy();

    chartMediaQuality = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(data),
            datasets: [{
                label: 'Media Assets',
                data: Object.values(data),
                backgroundColor: ['#06b6d4', '#6366f1', '#10b981', '#94a3b8', '#64748b'],
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' } }
            },
            plugins: { legend: { display: false } }
        }
    });
}

// 8. Moderation Pipeline Chart
function initModerationChart(data) {
    const ctx = document.getElementById('moderationDonutChart');
    if (!ctx) return;
    if (chartModeration) chartModeration.destroy();

    chartModeration = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Pending Review', 'Rejected'],
            datasets: [{
                data: [data.approved || 0, data.pending || 0, data.rejected || 0],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderColor: 'rgba(15, 23, 42, 0.95)',
                borderWidth: 3,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { position: 'right', labels: { boxWidth: 10, padding: 10, font: { size: 11 } } }
            }
        }
    });
}

// 9. User Demographics Chart
function initUserStatusChart(data) {
    const ctx = document.getElementById('userStatusDonutChart');
    if (!ctx) return;
    if (chartUserStatus) chartUserStatus.destroy();

    chartUserStatus = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Active Members', 'Administrators', 'Suspended Accounts'],
            datasets: [{
                data: [data.regular || 0, data.admin || 0, data.inactive || 0],
                backgroundColor: ['#6366f1', '#06b6d4', '#64748b'],
                borderColor: 'rgba(15, 23, 42, 0.95)',
                borderWidth: 2,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { boxWidth: 10, padding: 8, font: { size: 11 } } }
            }
        }
    });
}

// Interactive Control Functions
function setTimelineType(type) {
    currentTimelineType = type;
    document.getElementById('btnChartTypeLine').classList.toggle('active', type === 'line');
    document.getElementById('btnChartTypeBar').classList.toggle('active', type === 'bar');
    initTimelineChart(initialTimelineData);
}

function setCategoryChartType(type) {
    currentCategoryChartType = type;
    document.getElementById('btnCategoryBar').classList.toggle('active', type === 'bar');
    document.getElementById('btnCategoryRadar').classList.toggle('active', type === 'radar');
    initCategoryChart(initialCategoryData, type);
}

function toggleDataset(index, isVisible) {
    if (chartTimeline) {
        chartTimeline.setDatasetVisibility(index, isVisible);
        chartTimeline.update();
    }
}

// Dynamic AJAX Range and Category Filter Fetcher
function switchRange(range) {
    currentRange = range;
    
    // Update button states
    const buttons = document.querySelectorAll('#rangeButtonContainer .btn-analytics-tab');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    document.getElementById('chartRangeBadge').textContent = range.toUpperCase().replace('_', ' ');

    fetchTelemetryData();
}

function filterCategory(catId, label) {
    currentCategoryId = catId;
    document.getElementById('currentUniverseLabel').textContent = label;
    fetchTelemetryData();
}

function refreshData() {
    const spinner = document.getElementById('refreshSpinner');
    spinner.classList.add('bi-arrow-clockwise-spin');
    spinner.style.animation = 'spin 0.8s linear infinite';

    fetchTelemetryData().finally(() => {
        setTimeout(() => {
            spinner.style.animation = 'none';
        }, 600);
    });
}

function fetchTelemetryData() {
    const url = new URL('{{ route("admin.analytics.api") }}', window.location.origin);
    url.searchParams.append('range', currentRange);
    if (currentCategoryId) {
        url.searchParams.append('category_id', currentCategoryId);
    }

    return fetch(url.toString(), {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(response => {
        if (response.status === 'success') {
            updateDashboardMetrics(response.data);
        }
    })
    .catch(err => {
        console.error('Error fetching analytics telemetry:', err);
    });
}

function updateDashboardMetrics(data) {
    // 1. Update KPI Values
    document.getElementById('kpiTotalUsers').textContent = new Intl.NumberFormat().format(data.totalUsers);
    document.getElementById('kpiNewUsers').textContent = data.newUsersInRange;
    document.getElementById('kpiActiveUsers').textContent = new Intl.NumberFormat().format(data.activeUsersCount);
    document.getElementById('kpiBotMessages').textContent = new Intl.NumberFormat().format(data.messagesInRange);
    document.getElementById('kpiAvgRating').innerHTML = `${Number(data.avgRating).toFixed(2)} <span class="fs-6 text-muted">/ 5.0</span>`;
    document.getElementById('kpiTotalActions').textContent = new Intl.NumberFormat().format(data.totalActivitiesInRange);
    document.getElementById('kpiTotalBookmarks').textContent = new Intl.NumberFormat().format(data.totalBookmarks);

    // 2. Update Timeline Chart
    initialTimelineData = data.timeline;
    initTimelineChart(data.timeline);

    // Update Strip Summaries
    const sum = arr => arr ? arr.reduce((a, b) => a + b, 0) : 0;
    document.getElementById('summaryActivitiesTotal').textContent = new Intl.NumberFormat().format(sum(data.timeline.activities));
    document.getElementById('summaryMessagesTotal').textContent = new Intl.NumberFormat().format(sum(data.timeline.messages));
    document.getElementById('summaryRegistrationsTotal').textContent = new Intl.NumberFormat().format(sum(data.timeline.registrations));
    document.getElementById('summaryBookmarksTotal').textContent = new Intl.NumberFormat().format(sum(data.timeline.bookmarks));
    document.getElementById('summaryRatingsTotal').textContent = new Intl.NumberFormat().format(sum(data.timeline.ratings));

    // 3. Update Category Universe Chart
    initialCategoryData = data.categoryChartData;
    initCategoryChart(data.categoryChartData, currentCategoryChartType);

    // 4. Update Chatbot Source Chart
    initialSourceData = data.sourceBreakdown;
    initChatbotSourceChart(data.sourceBreakdown);
    document.getElementById('srcKbCount').textContent = data.sourceBreakdown.knowledge_base;
    document.getElementById('srcDbCount').textContent = data.sourceBreakdown.database;
    document.getElementById('srcAiCount').textContent = data.sourceBreakdown.ai;
    document.getElementById('srcFallbackCount').textContent = data.sourceBreakdown.fallback;

    // 5. Update Hourly Chart
    initialHourlyData = data.hourlyActivities;
    initHourlyChart(data.hourlyActivities);

    // 6. Update Rating Chart
    initialStarData = data.starDistribution;
    initRatingChart(data.starDistribution);

    // 7. Update Format Chart
    initialFormatData = data.contentFormats;
    initContentFormatChart(data.contentFormats);

    // 8. Update Media Chart
    initialMediaData = data.mediaQualities;
    initMediaQualityChart(data.mediaQualities);

    // 9. Update Moderation Chart
    initialSubmissionsData = data.submissionsCount;
    initModerationChart(data.submissionsCount);
}

// Live Telemetry Stream Simulation Engine
function toggleLiveStream() {
    isLiveStreaming = !isLiveStreaming;
    const btn = document.getElementById('toggleLiveStreamBtn');
    const label = document.getElementById('liveBtnLabel');
    const dot = document.getElementById('liveStreamDot');
    const text = document.getElementById('liveStreamText');

    if (isLiveStreaming) {
        btn.classList.remove('btn-outline-danger');
        btn.classList.add('btn-danger');
        label.textContent = 'Stop Live Stream';
        dot.classList.add('live-pulse-active');
        text.textContent = 'STREAMING LIVE TELEMETRY (3s)';

        liveStreamInterval = setInterval(() => {
            simulateLiveTelemetryPulse();
        }, 3000);
    } else {
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-outline-danger');
        label.textContent = 'Start Live Stream';
        dot.classList.remove('live-pulse-active');
        text.textContent = 'LIVE TELEMETRY';

        clearInterval(liveStreamInterval);
    }
}

function simulateLiveTelemetryPulse() {
    if (!chartTimeline) return;

    const lastIdx = chartTimeline.data.labels.length - 1;
    if (lastIdx < 0) return;

    // Simulate incoming actions
    const deltaAct = Math.floor(Math.random() * 4) + 1;
    const deltaMsg = Math.floor(Math.random() * 3);
    const deltaReg = Math.random() > 0.7 ? 1 : 0;
    const deltaBm = Math.random() > 0.6 ? 1 : 0;
    const deltaRt = Math.random() > 0.6 ? 1 : 0;

    chartTimeline.data.datasets[0].data[lastIdx] += deltaAct;
    chartTimeline.data.datasets[1].data[lastIdx] += deltaMsg;
    chartTimeline.data.datasets[2].data[lastIdx] += deltaReg;
    chartTimeline.data.datasets[3].data[lastIdx] += deltaBm;
    chartTimeline.data.datasets[4].data[lastIdx] += deltaRt;
    chartTimeline.update('none');

    // Update KPI numbers dynamically
    const kpiAct = document.getElementById('kpiTotalActions');
    if (kpiAct) {
        const cur = parseInt(kpiAct.textContent.replace(/,/g, '')) || 0;
        kpiAct.textContent = new Intl.NumberFormat().format(cur + deltaAct);
    }

    const kpiBot = document.getElementById('kpiBotMessages');
    if (kpiBot && deltaMsg > 0) {
        const cur = parseInt(kpiBot.textContent.replace(/,/g, '')) || 0;
        kpiBot.textContent = new Intl.NumberFormat().format(cur + deltaMsg);
    }
}

// Export Telemetry Functions
function exportAnalyticsJSON() {
    const payload = {
        exported_at: new Date().toISOString(),
        range: currentRange,
        timeline: initialTimelineData,
        fandom_universes: initialCategoryData,
        sources: initialSourceData,
        sentiment: initialStarData,
        formats: initialFormatData,
        media: initialMediaData
    };

    const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(payload, null, 2));
    const downloadAnchor = document.createElement('a');
    downloadAnchor.setAttribute("href", dataStr);
    downloadAnchor.setAttribute("download", `fan_hub_analytics_${currentRange}_${Date.now()}.json`);
    document.body.appendChild(downloadAnchor);
    downloadAnchor.click();
    downloadAnchor.remove();
}

function exportAnalyticsCSV() {
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Time_Period,Activities,Chatbot_Messages,Registrations,Bookmarks,Ratings\n";

    if (initialTimelineData.labels) {
        initialTimelineData.labels.forEach((label, i) => {
            const act = initialTimelineData.activities[i] || 0;
            const msg = initialTimelineData.messages[i] || 0;
            const reg = initialTimelineData.registrations[i] || 0;
            const bm = (initialTimelineData.bookmarks && initialTimelineData.bookmarks[i]) || 0;
            const rt = (initialTimelineData.ratings && initialTimelineData.ratings[i]) || 0;
            csvContent += `"${label}",${act},${msg},${reg},${bm},${rt}\n`;
        });
    }

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `fan_hub_timeline_${currentRange}_${Date.now()}.csv`);
    document.body.appendChild(link);
    link.click();
    link.remove();
}
</script>
@endpush
