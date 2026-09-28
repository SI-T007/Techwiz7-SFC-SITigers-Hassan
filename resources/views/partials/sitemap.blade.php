<section class="fh-sitemap-section" id="platform-sitemap" aria-label="Platform Sitemap">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-subtle">
            <div>
                <span class="small text-info fw-bold text-uppercase" style="letter-spacing: 0.08em;">Navigation Directory</span>
                <h2 class="h4 fw-bold text-light mb-0">Universe Visual Sitemap</h2>
            </div>
            <span class="badge bg-secondary bg-opacity-25 text-light small d-none d-sm-inline">
                Verified Routes &bull; Accessible
            </span>
        </div>

        <div class="row g-4">
            
            <div class="col-6 col-md-4 col-lg-2">
                <h3 class="fh-footer-heading" style="font-size: 0.85rem;">Platform</h3>
                <ul class="fh-footer-links small">
                    <li><a href="{{ route('home') }}">Home Portal</a></li>
                    <li><a href="{{ route('explore') }}">Universe Explore</a></li>
                    <li><a href="{{ route('about') }}">About & Vision</a></li>
                    <li><a href="#platform-sitemap">Sitemap Directory</a></li>
                </ul>
            </div>

        
            <div class="col-6 col-md-4 col-lg-3">
                <h3 class="fh-footer-heading" style="font-size: 0.85rem;">Fandom Realms</h3>
                <ul class="fh-footer-links small">
                    <li><a href="{{ route('fandoms.show', 'anime') }}">Anime Hub</a></li>
                    <li><a href="{{ route('fandoms.show', 'gaming') }}">Gaming World</a></li>
                    <li><a href="{{ route('fandoms.show', 'movies') }}">Cinematic Movies</a></li>
                    <li><a href="{{ route('fandoms.show', 'tv-shows') }}">TV & Streaming</a></li>
                    <li><a href="{{ route('fandoms.show', 'k-pop') }}">K-Pop Community</a></li>
                    <li><a href="{{ route('fandoms.show', 'comics') }}">Comics & Graphic Novels</a></li>
                    <li><a href="{{ route('fandoms.show', 'manga') }}">Manga & Webtoons</a></li>
                    <li><a href="{{ route('fandoms.show', 'cosplay') }}">Cosplay & Props</a></li>
                </ul>
            </div>

            <!-- Content & Archives -->
            <div class="col-6 col-md-4 col-lg-2">
                <h3 class="fh-footer-heading" style="font-size: 0.85rem;">Lore & Drops</h3>
                <ul class="fh-footer-links small">
                    <li><a href="{{ route('characters.index') }}">Character Profiles</a></li>
                    <li><a href="{{ route('releases.index') }}">Upcoming Releases</a></li>
                    <li><a href="{{ route('events.index') }}">Conventions & Events</a></li>
                    <li><a href="{{ route('explore') }}">Featured Lore</a></li>
                </ul>
            </div>

            <!-- Community & Fan Works -->
            <div class="col-6 col-md-4 col-lg-2">
                <h3 class="fh-footer-heading" style="font-size: 0.85rem;">Community</h3>
                <ul class="fh-footer-links small">
                    <li><a href="{{ route('community.index') }}">Community Hub</a></li>
                    <li><a href="{{ route('community.index') }}#submissions">Fan Art & Creations</a></li>
                    <li><a href="{{ route('community.index') }}#reviews">Member Reviews</a></li>
                    <li><a href="{{ route('community.index') }}#feedback">Platform Feedback</a></li>
                </ul>
            </div>

            <!-- Member Portal -->
            <div class="col-12 col-md-4 col-lg-3">
                <h3 class="fh-footer-heading" style="font-size: 0.85rem;">Account & Governance</h3>
                <ul class="fh-footer-links small">
                    @guest
                        <li><a href="{{ route('login') }}">Member Sign In</a></li>
                        <li><a href="{{ route('register') }}">Create Account</a></li>
                        <li><a href="{{ route('password.request') }}">Recover Password</a></li>
                    @else
                        <li><a href="{{ route('user.dashboard') }}">Personal Dashboard</a></li>
                        <li><a href="{{ route('bookmarks.index') }}">My Saved Bookmarks</a></li>
                        <li><a href="{{ route('profile.show') }}">Public Profile</a></li>
                        <li><a href="{{ route('profile.edit') }}">Settings & Preferences</a></li>
                        @if(Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="text-danger fw-semibold">
                                    <i class="bi bi-shield-lock me-1"></i> Admin Console
                                </a>
                            </li>
                        @endif
                    @endguest
                </ul>
            </div>
        </div>
    </div>
</section>
