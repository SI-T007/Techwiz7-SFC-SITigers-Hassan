<nav class="navbar navbar-expand-lg fh-navbar">
    <div class="container-fluid px-3 px-md-4 position-relative">
        <!-- Brand Logo -->
        <a class="fh-logo me-3 me-xl-4" href="{{ route('home') }}" aria-label="Fan Hub Plus Home">
            <!-- Circular Icon (all breakpoints) -->
            <img src="{{ asset('images/logo-favicon.png') }}?v=2"
                 alt="Fan Hub Plus"
                 class="fh-logo-circle-img"
                 loading="eager">
            <!-- Wordmark text — desktop only -->
            <span class="fh-logo-text d-none d-md-inline">FAN HUB<span>+</span></span>
        </a>

        <!-- Mobile Search Dropdown Overlay -->
        <div class="fh-mobile-search-overlay d-lg-none" id="mobileSearchOverlay" style="display: none;">
            <form action="{{ route('explore') }}" method="GET" class="d-flex align-items-center gap-2 w-100">
                <div class="input-group flex-grow-1">
                    <span class="input-group-text bg-dark border-secondary text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text"
                           name="search"
                           id="mobileSearchOverlayInput"
                           class="form-control bg-dark text-light border-secondary shadow-none"
                           placeholder="Search titles, lore, characters, drops..."
                           value="{{ request('search') }}"
                           autocomplete="off">
                    <button class="btn btn-danger px-3" type="submit">Search</button>
                </div>
                <button type="button" class="btn btn-link text-light p-1 text-decoration-none" id="closeMobileSearch" aria-label="Close search">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </form>
        </div>

        <!-- Mobile Toggler -->
        <div class="d-flex align-items-center gap-2 d-lg-none ms-auto me-2">
            <button class="fh-mobile-search-btn d-lg-none" id="mobileSearchBtn" type="button" aria-label="Open search">
                <i class="bi bi-search"></i>
            </button>
            <button class="navbar-toggler border-0 p-1 text-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFanhub" aria-controls="navbarFanhub" aria-expanded="false" aria-label="Toggle navigation menu">
                <i class="bi bi-list fs-2 text-light"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarFanhub">
            <!-- Mobile Quick Search Bar inside menu -->
            <div class="d-lg-none my-3 px-1">
                <form action="{{ route('explore') }}" method="GET" class="d-flex gap-2">
                    <input type="text"
                           name="search"
                           class="form-control bg-dark text-light border-secondary"
                           placeholder="Search titles, lore, genres..."
                           value="{{ request('search') }}">
                    <button class="btn btn-danger px-3 flex-shrink-0" type="submit" aria-label="Search">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>

            <!-- Center-Left Netflix Navigation Links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link fh-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        Home
                    </a>
                </li>
                <li class="nav-item fh-nav-collapsible">
                    <a class="nav-link fh-nav-link {{ request()->is('fandoms/tv-shows') ? 'active' : '' }}" href="{{ route('fandoms.show', 'tv-shows') }}">
                        TV Shows
                    </a>
                </li>
                <li class="nav-item fh-nav-collapsible">
                    <a class="nav-link fh-nav-link {{ request()->is('fandoms/movies') ? 'active' : '' }}" href="{{ route('fandoms.show', 'movies') }}">
                        Movies
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fh-nav-link {{ request()->routeIs('anime.*') || request()->is('anime*') ? 'active' : '' }}" href="{{ route('anime.index') }}">
                        <span class="d-inline-flex align-items-center gap-1">
                            Anime
                            <span class="badge bg-danger text-light px-1.5 py-0.5 rounded-pill" style="font-size: 0.65rem; font-weight: 800;">HOT</span>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fh-nav-link {{ request()->routeIs('explore*') ? 'active' : '' }}" href="{{ route('explore') }}">
                        New & Popular
                    </a>
                </li>
                <li class="nav-item">
                    @auth
                        <a class="nav-link fh-nav-link {{ request()->routeIs('bookmarks.*') ? 'active' : '' }}" href="{{ route('bookmarks.index') }}">
                            My List
                        </a>
                    @else
                        <a class="nav-link fh-nav-link" href="{{ route('login') }}">
                            My List
                        </a>
                    @endauth
                </li>

                <!-- More Fandom Realms & Features Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link fh-nav-link dropdown-toggle" href="#" id="browseRealmsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Browse Lore
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow-lg" aria-labelledby="browseRealmsDropdown">
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('anime.index') }}">
                                <i class="bi bi-fire text-danger me-2"></i> Anime Universe & Shonen Hub
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('fandoms.index') }}">
                                <i class="bi bi-grid-fill text-info me-2"></i> All Fandom Realms
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('characters.index') }}">
                                <i class="bi bi-people-fill text-info me-2"></i> Character Archives
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('releases.index') }}">
                                <i class="bi bi-calendar-event-fill text-warning me-2"></i> Release Schedule
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('events.index') }}">
                                <i class="bi bi-geo-alt-fill text-success me-2"></i> Fandom Conventions
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('multimedia.index') }}">
                                <i class="bi bi-play-circle-fill text-primary me-2"></i> Multimedia Hub
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('community.index') }}">
                                <i class="bi bi-chat-heart-fill text-danger me-2"></i> Fan Community
                            </a>
                        </li>
                        <li><hr class="dropdown-divider border-subtle"></li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('chatbot.index') }}">
                                <i class="bi bi-robot text-purple me-2"></i> AI Fan Assistant
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- Right Controls: Expandable Search, Notifications, Theme & Profile -->
            <div class="d-flex align-items-center gap-2 gap-xl-3 fh-navbar-right-controls">
                <!-- Netflix Expandable Search Bar -->
                <div class="fh-expandable-search d-none d-lg-flex" id="navSearchBox">
                    <form action="{{ route('explore') }}" method="GET" class="fh-search-input-wrap" id="navSearchForm">
                        <button class="fh-search-toggle-btn" type="button" id="navSearchToggle" aria-label="Search" aria-expanded="false">
                            <i class="bi bi-search"></i>
                        </button>
                        <input type="text"
                               name="search"
                               id="navSearchInput"
                               class="fh-expandable-input"
                               placeholder="Titles, lore, drops, genres..."
                               aria-label="Search catalog"
                               value="{{ request('search') }}"
                               autocomplete="off">
                        <button type="button" class="fh-search-clear-btn" id="navSearchClear" aria-label="Clear search" style="display: none;">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </form>
                </div>

                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="fh-nav-notification-btn" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                        <i class="bi bi-bell-fill"></i>
                        <span class="fh-nav-notification-badge"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg py-2" aria-labelledby="notifDropdown" style="width: 290px;">
                        <li class="px-3 py-2 border-bottom border-subtle">
                            <div class="fw-bold text-light small text-uppercase" style="letter-spacing: 0.05em;">Fandom Alerts</div>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 small d-flex align-items-center gap-2" href="{{ route('releases.index') }}">
                                <i class="bi bi-broadcast text-danger fs-6"></i>
                                <div>
                                    <div class="text-light fw-medium">New Release Drops</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Track upcoming canon premieres</div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 small d-flex align-items-center gap-2" href="{{ route('events.index') }}">
                                <i class="bi bi-ticket-perforated text-warning fs-6"></i>
                                <div>
                                    <div class="text-light fw-medium">Fandom Expo 2026</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Check calendar & venues</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Theme Toggle (Fully Responsive across Mobile, Tablet & Desktop) -->
                <button class="fh-theme-toggle d-inline-flex" type="button" id="navThemeToggle" aria-label="Toggle visual theme" title="Toggle Dark/Light Mode">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>

                @guest
                    <a href="{{ route('login') }}" class="fh-btn-ghost py-1 px-3 fs-6">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="netflix-btn-play py-1 px-3 fs-6">
                        Join Free
                    </a>
                @else
                    <!-- Authenticated User — Slide-in Sidebar Trigger -->
                    <button class="btn p-1 d-flex align-items-center gap-2 border-0" type="button"
                            id="userSidebarToggle" aria-label="Open user menu" aria-expanded="false">
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}"
                             class="avatar-img" width="34" height="34"
                             style="border: 2px solid var(--fh-accent); border-radius: 4px;">
                        <span class="d-none d-xl-inline fw-semibold text-light small">{{ Auth::user()->display_name }}</span>
                        <i class="bi bi-caret-down-fill text-muted" style="font-size: 0.7rem;"></i>
                    </button>
                @endguest
            </div>
        </div>
    </div>
</nav>

{{-- ═══ Sidebar Backdrop ═══ --}}
<div class="fh-sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

@auth
{{-- ═══ Full-Height User Sidebar Panel ═══ --}}
<aside class="fh-user-sidebar" id="userSidebar" role="dialog" aria-modal="true" aria-label="User menu">

    {{-- Sidebar Header --}}
    <div class="fh-sidebar-header d-flex align-items-center gap-3">
        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}"
             class="fh-sidebar-avatar flex-shrink-0">
        <div class="flex-grow-1 overflow-hidden">
            <div class="fw-bold text-light lh-sm text-truncate">{{ Auth::user()->name }}</div>
            <div class="text-muted small text-truncate" style="font-size: 0.78rem;">{{ Auth::user()->email }}</div>
        </div>
        <button class="fh-sidebar-close" id="sidebarClose" aria-label="Close menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- Section: My Account --}}
    <div class="fh-sidebar-section">
        <div class="fh-sidebar-section-label">My Account</div>
        <a class="fh-sidebar-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}"
           href="{{ route('user.dashboard') }}">
            <i class="bi bi-speedometer2 text-danger"></i> Account Dashboard
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('bookmarks.*') ? 'active' : '' }}"
           href="{{ route('bookmarks.index') }}">
            <i class="bi bi-bookmark-star-fill text-warning"></i> My List
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('watch-history.*') ? 'active' : '' }}"
           href="{{ route('watch-history.index') }}">
            <i class="bi bi-clock-history text-info"></i> Watch History
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('community.my-submissions') ? 'active' : '' }}"
           href="{{ route('community.my-submissions') }}">
            <i class="bi bi-collection-play text-info"></i> My Fan Works
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('community.submit') ? 'active' : '' }}"
           href="{{ route('community.submit') }}">
            <i class="bi bi-plus-circle text-success"></i> Submit Fan Work
        </a>
    </div>

    {{-- Section: Explore Realms --}}
    <div class="fh-sidebar-section">
        <div class="fh-sidebar-section-label">Explore Realms</div>
        <a class="fh-sidebar-link {{ request()->routeIs('events.*') ? 'active' : '' }}"
           href="{{ route('events.index') }}">
            <i class="bi bi-geo-alt-fill text-danger"></i> Events & Map Radar
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('releases.*') ? 'active' : '' }}"
           href="{{ route('releases.index') }}">
            <i class="bi bi-calendar-event-fill text-warning"></i> Release Schedule
        </a>
    </div>

    {{-- Section: Profile --}}
    <div class="fh-sidebar-section">
        <div class="fh-sidebar-section-label">Profile</div>
        <a class="fh-sidebar-link {{ request()->routeIs('profile.show') ? 'active' : '' }}"
           href="{{ route('profile.show') }}">
            <i class="bi bi-person text-info"></i> Profile
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
           href="{{ route('profile.edit') }}">
            <i class="bi bi-gear text-secondary"></i> Settings
        </a>
        <button type="button" class="fh-sidebar-link fh-sidebar-theme-btn fh-theme-toggle w-100 border-0 bg-transparent text-start d-flex align-items-center justify-content-between py-2 px-3 mt-1" aria-label="Toggle theme">
            <span class="d-flex align-items-center gap-2">
                <i class="bi bi-moon-stars-fill text-warning"></i> Visual Theme
            </span>
            <span class="badge bg-secondary bg-opacity-25 text-light small px-2 py-1 fh-theme-mode text-capitalize">Dark</span>
        </button>
    </div>

    @if(Auth::user()->isAdmin())
    {{-- Section: Admin Panel --}}
    <div class="fh-sidebar-section">
        <div class="fh-sidebar-section-label">
            Admin Panel <span class="fh-sidebar-admin-badge">ADMIN</span>
        </div>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           href="{{ route('admin.dashboard') }}">
            <i class="bi bi-shield-lock-fill text-danger"></i>
            Admin Control Center
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}"
           href="{{ route('admin.analytics.index') }}">
            <i class="bi bi-graph-up text-success"></i>
            Platform Analytics
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
           href="{{ route('admin.categories.index') }}">
            <i class="bi bi-grid text-info"></i>
            Fandom Categories
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.content.*') ? 'active' : '' }}"
           href="{{ route('admin.content.index') }}">
            <i class="bi bi-file-earmark-richtext text-warning"></i>
            Content Manager
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}"
           href="{{ route('admin.media.index') }}">
            <i class="bi bi-play-circle-fill text-primary"></i>
            Multimedia Assets
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.media.create') ? 'active' : '' }}"
           href="{{ route('admin.media.create') }}">
            <i class="bi bi-plus-circle-fill text-success"></i>
            + Add Media Asset
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.characters.*') ? 'active' : '' }}"
           href="{{ route('admin.characters.index') }}">
            <i class="bi bi-people-fill text-info"></i>
            Characters
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}"
           href="{{ route('admin.events.index') }}">
            <i class="bi bi-calendar-event-fill text-warning"></i>
            Events
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.releases.*') ? 'active' : '' }}"
           href="{{ route('admin.releases.index') }}">
            <i class="bi bi-clock-history text-danger"></i>
            Releases
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}"
           href="{{ route('admin.submissions.index') }}">
            <i class="bi bi-patch-check text-warning"></i>
            Moderation Queue
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
           href="{{ route('admin.users') }}">
            <i class="bi bi-people text-info"></i>
            User Management
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}"
           href="{{ route('admin.feedback.index') }}">
            <i class="bi bi-chat-left-text text-secondary"></i>
            Feedback
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}"
           href="{{ route('admin.articles.index') }}">
            <i class="bi bi-newspaper text-light"></i>
            Lore Articles
        </a>
        <a class="fh-sidebar-link {{ request()->routeIs('admin.chatbot.*') ? 'active' : '' }}"
           href="{{ route('admin.chatbot.knowledge.index') }}">
            <i class="bi bi-robot text-purple"></i>
            Chatbot Knowledge
        </a>
    </div>
    @endif

    {{-- Sidebar Footer: Logout --}}
    <div class="fh-sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="fh-sidebar-link w-100 text-start border-0 bg-transparent"
                    style="color: #e50914 !important;">
                <i class="bi bi-box-arrow-right" style="color: #e50914;"></i>
                Sign Out of Fan Hub+
            </button>
        </form>
    </div>

</aside>

<script>
(function () {
    const toggle   = document.getElementById('userSidebarToggle');
    const sidebar  = document.getElementById('userSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const closeBtn = document.getElementById('sidebarClose');

    if (!toggle || !sidebar) return;

    function openSidebar() {
        sidebar.classList.add('show');
        backdrop.classList.add('active');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
        sidebar.focus();
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        backdrop.classList.remove('active');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        toggle.focus();
    }

    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
    });

    closeBtn && closeBtn.addEventListener('click', closeSidebar);
    backdrop.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            closeSidebar();
        }
    });
})();
</script>
@endauth
