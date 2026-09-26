@if ($paginator->hasPages())
    {{-- Fan Hub Plus — Unique Cyber Animated Pagination Component --}}
    <div class="fh-pagination-container my-4 text-center">
        {{-- Results Counter & Status --}}
        <div class="fh-pagination-meta mb-3 d-flex align-items-center justify-content-center gap-2">
            <span class="fh-meta-dot"></span>
            <span class="fh-meta-text">
                Showing <strong class="text-light">{{ $paginator->firstItem() ?? 1 }}</strong>–<strong class="text-light">{{ $paginator->lastItem() ?? $paginator->count() }}</strong>
                of <strong class="text-light">{{ $paginator->total() }}</strong> results
                <span class="fh-meta-divider mx-1">•</span>
                Page <strong class="text-danger">{{ $paginator->currentPage() }}</strong> of <strong class="text-light">{{ $paginator->lastPage() }}</strong>
            </span>
        </div>

        {{-- Interactive Glassmorphic Control Bar --}}
        <nav class="fh-pagination-nav" role="navigation" aria-label="Pagination Navigation">
            <ul class="fh-pagination-list">

                {{-- Quick Jump: First Page (if far from page 1) --}}
                @if ($paginator->currentPage() > 3)
                    <li class="fh-page-item">
                        <a href="{{ $paginator->url(1) }}" class="fh-page-btn fh-jump-btn" title="First Page" aria-label="First Page">
                            <i class="bi bi-chevron-double-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Previous Page Button --}}
                @if ($paginator->onFirstPage())
                    <li class="fh-page-item disabled" aria-disabled="true">
                        <span class="fh-page-btn fh-nav-btn fh-nav-prev disabled">
                            <i class="bi bi-chevron-left fh-arrow-icon"></i>
                            <span class="fh-nav-label d-none d-sm-inline">Prev</span>
                        </span>
                    </li>
                @else
                    <li class="fh-page-item">
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="fh-page-btn fh-nav-btn fh-nav-prev" aria-label="Previous Page">
                            <i class="bi bi-chevron-left fh-arrow-icon"></i>
                            <span class="fh-nav-label d-none d-sm-inline">Prev</span>
                        </a>
                    </li>
                @endif

                {{-- Page Number Elements --}}
                @foreach ($elements as $element)
                    {{-- Separator Ellipsis "..." --}}
                    @if (is_string($element))
                        <li class="fh-page-item disabled" aria-disabled="true">
                            <span class="fh-page-dots">
                                <span class="dot"></span>
                                <span class="dot"></span>
                                <span class="dot"></span>
                            </span>
                        </li>
                    @endif

                    {{-- Array Of Page Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="fh-page-item active" aria-current="page">
                                    <span class="fh-page-btn fh-page-number active">
                                        <span class="fh-active-halo"></span>
                                        <span class="fh-number-val">{{ $page }}</span>
                                    </span>
                                </li>
                            @else
                                <li class="fh-page-item">
                                    <a href="{{ $url }}" class="fh-page-btn fh-page-number" aria-label="Go to page {{ $page }}">
                                        <span class="fh-number-val">{{ $page }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Button --}}
                @if ($paginator->hasMorePages())
                    <li class="fh-page-item">
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="fh-page-btn fh-nav-btn fh-nav-next" aria-label="Next Page">
                            <span class="fh-nav-label d-none d-sm-inline">Next</span>
                            <i class="bi bi-chevron-right fh-arrow-icon"></i>
                        </a>
                    </li>
                @else
                    <li class="fh-page-item disabled" aria-disabled="true">
                        <span class="fh-page-btn fh-nav-btn fh-nav-next disabled">
                            <span class="fh-nav-label d-none d-sm-inline">Next</span>
                            <i class="bi bi-chevron-right fh-arrow-icon"></i>
                        </span>
                    </li>
                @endif

                {{-- Quick Jump: Last Page (if far from end) --}}
                @if ($paginator->currentPage() < $paginator->lastPage() - 2)
                    <li class="fh-page-item">
                        <a href="{{ $paginator->url($paginator->lastPage()) }}" class="fh-page-btn fh-jump-btn" title="Last Page ({{ $paginator->lastPage() }})" aria-label="Last Page">
                            <i class="bi bi-chevron-double-right"></i>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
    </div>

    {{-- Scoped Animations & Styles for Fan Hub Pagination --}}
    <style>
        .fh-pagination-container {
            position: relative;
            padding: 18px 0;
            user-select: none;
        }

        /* Results Counter Badge */
        .fh-pagination-meta {
            font-size: 0.8125rem;
            color: #94a3b8;
            letter-spacing: 0.02em;
        }

        .fh-meta-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #e50914;
            display: inline-block;
            box-shadow: 0 0 10px #e50914;
            animation: fhDotBreathe 2s ease-in-out infinite;
        }

        @keyframes fhDotBreathe {
            0%, 100% { transform: scale(1); opacity: 0.7; box-shadow: 0 0 6px #e50914; }
            50% { transform: scale(1.3); opacity: 1; box-shadow: 0 0 14px #e50914; }
        }

        .fh-meta-divider {
            opacity: 0.4;
        }

        /* Outer Navigation Bar Pill */
        .fh-pagination-nav {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, rgba(22, 27, 44, 0.88), rgba(12, 15, 26, 0.95));
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 9999px;
            padding: 6px 10px;
            box-shadow: 0 14px 34px -8px rgba(0, 0, 0, 0.65), 0 0 25px -5px rgba(229, 9, 20, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            max-width: 100%;
        }

        .fh-pagination-nav:hover {
            border-color: rgba(229, 9, 20, 0.3);
            box-shadow: 0 16px 40px -6px rgba(0, 0, 0, 0.75), 0 0 35px -5px rgba(229, 9, 20, 0.25);
        }

        /* List Items */
        .fh-pagination-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .fh-page-item {
            display: inline-flex;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        /* Base Button Design */
        .fh-page-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none !important;
            font-family: inherit;
            border: none;
            outline: none;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            line-height: 1;
        }

        /* SVG Strict Reset (Prevents giant arrow bug forever) */
        .fh-pagination-container svg {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            display: inline-block !important;
        }

        .fh-arrow-icon {
            font-size: 0.85rem;
            line-height: 1;
            transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Prev / Next Nav Buttons */
        .fh-nav-btn {
            padding: 8px 14px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            color: #e2e8f0;
            font-size: 0.825rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            gap: 6px;
        }

        .fh-nav-btn:hover:not(.disabled) {
            background: linear-gradient(135deg, rgba(229, 9, 20, 0.22), rgba(229, 9, 20, 0.08));
            border-color: rgba(229, 9, 20, 0.55);
            color: #ffffff;
            box-shadow: 0 4px 16px rgba(229, 9, 20, 0.3);
            transform: translateY(-1px);
        }

        .fh-nav-prev:hover:not(.disabled) .fh-arrow-icon {
            transform: translateX(-3px);
        }

        .fh-nav-next:hover:not(.disabled) .fh-arrow-icon {
            transform: translateX(3px);
        }

        .fh-nav-btn.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
            background: rgba(255, 255, 255, 0.02);
            border-color: transparent;
            color: #64748b;
        }

        /* Page Number Buttons */
        .fh-page-number {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            color: #94a3b8;
            font-size: 0.875rem;
            font-weight: 700;
        }

        .fh-page-number:hover:not(.active) {
            background: rgba(229, 9, 20, 0.15);
            border-color: rgba(229, 9, 20, 0.45);
            color: #ffffff;
            transform: translateY(-2px) scale(1.06);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        }

        /* Active Page Number — Vibrant Pulsing Glow */
        .fh-page-number.active {
            background: linear-gradient(135deg, #e50914 0%, #b20710 100%);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            font-weight: 800;
            box-shadow: 0 0 16px rgba(229, 9, 20, 0.7), inset 0 1px 1px rgba(255, 255, 255, 0.4);
            cursor: default;
            animation: fhActivePulse 2.8s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes fhActivePulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 14px rgba(229, 9, 20, 0.65), inset 0 1px 1px rgba(255, 255, 255, 0.4);
            }
            50% {
                transform: scale(1.08);
                box-shadow: 0 0 24px rgba(229, 9, 20, 0.95), 0 0 6px rgba(255, 255, 255, 0.6);
            }
        }

        /* Quick First / Last Jumps */
        .fh-jump-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: #64748b;
            font-size: 0.75rem;
        }

        .fh-jump-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        /* Dots Separator */
        .fh-page-dots {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 3px;
            padding: 0 6px;
            height: 38px;
        }

        .fh-page-dots .dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #64748b;
            opacity: 0.6;
        }

        /* Mobile Optimization */
        @media (max-width: 576px) {
            .fh-pagination-nav {
                padding: 4px 8px;
                gap: 4px;
            }

            .fh-page-number {
                width: 34px;
                height: 34px;
                font-size: 0.8rem;
            }

            .fh-nav-btn {
                padding: 6px 10px;
                font-size: 0.78rem;
            }

            .fh-jump-btn {
                display: none;
            }
        }
    </style>
@endif
