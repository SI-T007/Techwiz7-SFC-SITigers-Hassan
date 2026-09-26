<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-authenticated" content="{{ Auth::check() ? 'true' : 'false' }}">
    <meta name="rating-url" content="{{ route('ratings.store') }}">
    <meta name="bookmark-toggle-url" content="{{ route('bookmarks.toggle') }}">
    <meta name="description" content="Fan Hub Plus — The premier unified fandom universe platform connecting Anime, Gaming, Movies, Comics, and Cosplay lore.">
    <meta name="theme-color" content="#e50914">

    <title>@yield('title', 'Universe Portal') — {{ config('app.name', 'Fan Hub Plus') }}</title>

    <!-- Favicons & PWA Icons (Crisp Circular with Transparent Background) -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon-192.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/favicon-512.png') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-192.png') }}?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">

    <!-- Immediate Theme Evaluator to eliminate FOUC (Flash of Unstyled Content) -->
    <script>
        (function() {
            @auth
                @if(Auth::user()->relationLoaded('profile') || Auth::user()->profile)
                    const userTheme = '{{ Auth::user()->profile?->display_preferences['theme'] ?? 'dark' }}';
                @else
                    const userTheme = 'dark';
                @endif
                document.documentElement.setAttribute('data-bs-theme', userTheme);
            @else
                const storedTheme = localStorage.getItem('fanhub_theme') || 'dark';
                document.documentElement.setAttribute('data-bs-theme', storedTheme);
            @endauth
        })();
    </script>

    <!-- Robust Global Image Fallback Handler -->
    <script>
        window.FALLBACK_POSTER = "https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=800&q=80";
        window.FALLBACK_SVG = "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22640%22%20height%3D%22360%22%20viewBox%3D%220%200%20640%20360%22%3E%3Cdefs%3E%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%231a0b12%22%2F%3E%3Cstop%20offset%3D%2250%25%22%20stop-color%3D%22%23111217%22%2F%3E%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%2308090c%22%2F%3E%3C%2FlinearGradient%3E%3C%2Fdefs%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22url(%23g)%22%2F%3E%3Ccircle%20cx%3D%22320%22%20cy%3D%22180%22%20r%3D%2236%22%20fill%3D%22%23e50914%22%20opacity%3D%220.85%22%2F%3E%3Cpolygon%20points%3D%22314%2C167%20334%2C180%20314%2C193%22%20fill%3D%22%23ffffff%22%2F%3E%3Ctext%20x%3D%22320%22%20y%3D%22240%22%20font-family%3D%22sans-serif%22%20font-size%3D%2213%22%20font-weight%3D%22700%22%20fill%3D%22%2394a3b8%22%20text-anchor%3D%22middle%22%20letter-spacing%3D%221%22%3EFAN%20HUB%20PLUS%3C%2Ftext%3E%3C%2Fsvg%3E";

        window.handleImageFallback = function(img) {
            if (!img || img.dataset.fhFailed) return;
            const currentSrc = img.src || '';
            if (currentSrc.includes('maxresdefault.jpg')) {
                img.src = currentSrc.replace('maxresdefault.jpg', 'hqdefault.jpg');
                return;
            }
            if (currentSrc.includes('hqdefault.jpg') || !img.dataset.fhStep) {
                img.dataset.fhStep = '1';
                img.src = window.FALLBACK_POSTER;
            } else {
                img.dataset.fhFailed = 'true';
                img.src = window.FALLBACK_SVG;
            }
        };

        window.addEventListener('error', function(e) {
            if (e.target && e.target.tagName === 'IMG') {
                window.handleImageFallback(e.target);
            }
        }, true);
    </script>

    <!-- Typography: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Fan Hub Plus Centralized Design Tokens & UI Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/fanhub-theme.css') }}?v=3">

    @stack('styles')
</head>
<body>
    <!-- Accessibility Skip Link -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Global Application Navbar -->
    @include('partials.navbar')

    <!-- Global Notifications / Flash Alerts -->
    <div class="fh-flash-container container {{ request()->routeIs('home') ? 'fh-flash-home' : '' }}">
        @include('partials.flash-messages')
    </div>

    <!-- Main Content Container -->
    <main id="main-content" class="flex-grow-1 {{ !request()->routeIs('home') ? 'fh-page-main' : '' }}">
        @yield('content')
    </main>

    <!-- Floating AI Chatbot Widget -->
    @unless(request()->routeIs('chatbot.*'))
        <x-chatbot-widget />
    @endunless

    <!-- Global Application Footer -->
    @include('partials.footer')

    <!-- Bootstrap 5 JS Bundle & Core Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/fanhub-core.js') }}?v=3"></script>
    @stack('scripts')
</body>
</html>
