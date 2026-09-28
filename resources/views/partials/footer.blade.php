<footer class="fh-footer py-5" style="background-color: #101010; border-top: 1px solid rgba(255, 255, 255, 0.08);">
    <div class="container py-3">
       
        <div class="mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <a class="fh-logo mb-2" href="{{ route('home') }}" aria-label="Fan Hub Plus">
                    <img src="{{ asset('images/logo-favicon.png') }}?v=2"
                         alt="Fan Hub Plus"
                         class="fh-logo-circle-img"
                         style="width: 32px; height: 32px;"
                         loading="lazy">
                    <span class="fh-logo-text">FAN HUB<span>+</span></span>
                </a>
                <div class="text-muted small">
                    Questions? Explore our <a href="{{ route('chatbot.index') }}" class="text-decoration-underline text-secondary">AI Knowledge Base</a> or <a href="{{ route('feedback.create') }}" class="text-decoration-underline text-secondary">Send Direct Feedback</a>
                </div>
            </div>
        </div>

   
        <div class="row g-4 mb-4 small text-muted">
            <div class="col-6 col-md-3">
                <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('explore') }}" class="text-muted text-decoration-none">Audio & Canon Lore</a></li>
                    <li><a href="{{ route('fandoms.show', 'anime') }}" class="text-muted text-decoration-none">Anime Realm</a></li>
                    <li><a href="{{ route('fandoms.show', 'gaming') }}" class="text-muted text-decoration-none">Gaming Realm</a></li>
                    <li><a href="{{ route('fandoms.show', 'movies') }}" class="text-muted text-decoration-none">Cinema Realm</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('characters.index') }}" class="text-muted text-decoration-none">Character Archives</a></li>
                    <li><a href="{{ route('releases.index') }}" class="text-muted text-decoration-none">Release Drops Radar</a></li>
                    <li><a href="{{ route('events.index') }}" class="text-muted text-decoration-none">Fandom Conventions</a></li>
                    <li><a href="{{ route('community.index') }}" class="text-muted text-decoration-none">Fan Creations Hub</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('multimedia.index') }}" class="text-muted text-decoration-none">Media Gallery</a></li>
                    <li><a href="{{ route('chatbot.index') }}" class="text-muted text-decoration-none">Platform Assistant</a></li>
                    <li><a href="{{ route('feedback.create') }}" class="text-muted text-decoration-none">Feedback & Support</a></li>
                    <li><a href="#platform-sitemap" class="text-muted text-decoration-none">Visual Platform Sitemap</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('fandoms.index') }}" class="text-muted text-decoration-none">All 8 Fandoms</a></li>
                    <li><a href="{{ route('about') }}" class="text-muted text-decoration-none">About Fan Hub+</a></li>
                    <li><a href="#platform-sitemap" class="text-muted text-decoration-none">Terms of Use</a></li>
                    <li><a href="#platform-sitemap" class="text-muted text-decoration-none">Privacy Statement</a></li>
                </ul>
            </div>
        </div>

        <!-- Social Icons & Controls -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top border-subtle">
            <div class="d-flex align-items-center gap-3">
                <!-- Language / Region Selector Pill -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-1 px-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: rgba(255, 255, 255, 0.2); color: #e5e5e5; font-size: 0.8rem;">
                        <i class="bi bi-globe"></i> English (Global)
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark shadow-sm">
                        <li><a class="dropdown-item active small" href="#">English (Global)</a></li>
                        <li><a class="dropdown-item small" href="#">日本語 (Japanese)</a></li>
                        <li><a class="dropdown-item small" href="#">Español (Spanish)</a></li>
                    </ul>
                </div>

                <!-- Theme Toggle Pill Button -->
                <button class="btn btn-sm btn-outline-secondary py-1 px-3 fh-theme-toggle fh-theme-toggle-pill d-inline-flex align-items-center gap-2"
                        type="button"
                        id="footerThemeToggle"
                        aria-label="Toggle visual theme"
                        title="Toggle Dark / Light Mode">
                    <i class="bi bi-moon-stars-fill fh-theme-icon"></i>
                    <span class="fh-theme-text-wrap">Theme: <strong class="fh-theme-mode text-capitalize text-warning">Dark</strong></span>
                </button>
            </div>

            <div class="d-flex align-items-center gap-3 fs-5 text-muted">
                <a href="#" class="text-muted hover-light" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="text-muted hover-light" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                <a href="#" class="text-muted hover-light" aria-label="Discord"><i class="bi bi-discord"></i></a>
                <a href="#" class="text-muted hover-light" aria-label="GitHub"><i class="bi bi-github"></i></a>
            </div>
        </div>

        <div class="mt-4 pt-2 small text-muted d-flex flex-wrap justify-content-between align-items-center">
            <div>
                &copy; {{ date('Y') }} <strong>FAN HUB+</strong> &bull; The premier cinematic Fandom Universe experience.
            </div>
            <div class="text-success small d-flex align-items-center gap-1">
                <i class="bi bi-circle-fill" style="font-size: 0.45rem;"></i> All Streaming Systems Operational
            </div>
        </div>
    </div>
</footer>
