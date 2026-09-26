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
