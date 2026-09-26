# Fan Hub Plus — UI/UX Design System Specification

## 1. Design Philosophy & Aesthetic Identity
Fan Hub Plus is designed as a **mature, cinematic, immersive, and highly structured Fandom Universe platform**. It rejects generic, unstyled templates, avoid AI "vibe coding", and instead provides an authentic pop-culture hub connecting Anime, Gaming, Movies, TV, K-Pop, Comics, Manga, and Cosplay.

---

## 2. Centralized CSS Tokens & Design Variables

All visual parameters are defined in [`public/css/fanhub-theme.css`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/public/css/fanhub-theme.css) and automatically switch between Dark Mode and Light Mode using CSS custom properties attached to `[data-bs-theme]`:

| Token | Dark Mode Value | Light Mode Value | Usage Description |
| :--- | :--- | :--- | :--- |
| `--fh-bg-main` | `#090c15` (Deep Cosmic Navy) | `#f8fafc` (Slate Crisp White) | Primary page canvas background |
| `--fh-bg-surface` | `#101423` (Midnight Slate) | `#ffffff` (Pure White) | Card containers and standard surfaces |
| `--fh-bg-surface-elevated`| `#161b2e` | `#f1f5f9` | Elevated surfaces, dropdowns, inputs |
| `--fh-border` | `rgba(255, 255, 255, 0.08)` | `rgba(15, 23, 42, 0.1)` | Subtle card and divider borders |
| `--fh-text-primary` | `#f8fafc` (Ultra Light) | `#0f172a` (Slate 900) | Main titles and display headings |
| `--fh-text-secondary` | `#cbd5e1` (Muted Slate) | `#334155` (Slate 700) | Body copy and descriptions |
| `--fh-text-muted` | `#94a3b8` | `#64748b` | Captions, dates, metadata |
| `--fh-primary` | `#6366f1` (Cyber Indigo) | `#4f46e5` (Deep Indigo) | Primary brand accent and interactive CTAs |
| `--fh-accent-cyan` | `#06b6d4` (Neon Cyan) | `#0891b2` | Technology and member pill badges |
| `--fh-accent-purple` | `#a855f7` (Electric Purple) | `#9333ea` | Character archives and admin badges |
| `--fh-accent-pink` | `#ec4899` (Magenta Rose) | `#db2777` | Highlight accents and admin roles |
| `--fh-accent-amber` | `#f59e0b` (Solar Amber) | `#d97706` | Ratings and warning indicators |
| `--fh-accent-emerald` | `#10b981` (Emerald) | `#059669` | Success states and active counters |
| `--fh-accent-crimson` | `#ef4444` (Crimson) | `#dc2626` | Error states and danger zone actions |

---

## 3. Typography Hierarchy

The platform relies on two complementary web fonts loaded via Google Fonts:
- **Headings & Display**: `'Outfit', sans-serif` (Bold, cinematic, futuristic geometry).
- **Body & Controls**: `'Inter', sans-serif` (Highly legible, modern grotesque).

```text
Display Heading:     3.5rem / 56px  (Outfit, Weight 800)
Page Title (H1):     2.25rem / 36px (Outfit, Weight 700)
Section Header (H2): 1.75rem / 28px (Outfit, Weight 700)
Card Heading (H3):   1.15rem / 18px (Outfit, Weight 600)
Body Paragraph:      0.975rem / 16px (Inter, Line Height 1.6)
Metadata & Captions: 0.8rem / 13px  (Inter, Weight 500)
```

---

## 4. Theme System & Zero-FOUC Architecture
Theme persistence is dual-layered to serve both guests and authenticated users:
1. **Zero-FOUC Initialization**: An inline JavaScript snippet at the very top of `<head>` reads user authentication state:
   - For authenticated users: reads their stored database preference `user_profiles.display_preferences['theme']`.
   - For visitors (guests): reads `localStorage.getItem('fanhub_theme')` with a fallback to `'dark'`.
   - Immediately sets `document.documentElement.setAttribute('data-bs-theme', theme)` before any markup is painted.
2. **Interactive Toggle**:
   - Toggling the theme immediately updates document attributes and toggles the sun/moon icon.
   - For authenticated users, an asynchronous background JSON request sends the preference to `/profile/preferences`, saving the choice to the database without reloading the page.

---

## 5. Reusable Component Catalog

All components reside in `resources/views/components/` and are cleanly instantiated using `<x-component-name />`:

| Component | File Path | Primary Props | Function |
| :--- | :--- | :--- | :--- |
| `<x-category-card>` | `components/category-card.blade.php` | `:category`, `:showCount` | Displays icon, category title, description, content counter, and accessible link. |
| `<x-content-card>` | `components/content-card.blade.php` | `:content` | 16:9 thumbnail, format badge, title, year, popularity score, description. |
| `<x-character-card>` | `components/character-card.blade.php` | `:character` | Character portrait, name, franchise badge, abilities summary, dossier link. |
| `<x-release-card>` | `components/release-card.blade.php` | `:release` | Upcoming release drop, platform, developer, countdown badge. |
| `<x-event-card>` | `components/event-card.blade.php` | `:event` | Gathering title, venue name, city/country, formatted date, ticket badge. |
| `<x-empty-state>` | `components/empty-state.blade.php` | `:icon`, `:title`, `:message`, `:actionText`, `:actionUrl` | Polished empty state with icon and optional CTA button. |
| `<x-loading-skeleton>` | `components/loading-skeleton.blade.php` | `:type` ('card', 'text', 'category'), `:count` | Shimmering loading placeholders respecting reduced-motion. |
| `<x-breadcrumb>` | `components/breadcrumb.blade.php` | `:items` (array) | Semantic breadcrumb with Schema.org ListItem microdata. |

---

## 6. Accessibility & Inclusivity (WCAG Compliance)
1. **Skip Navigation**: Accessible link (`<a href="#main-content" class="skip-link">Skip to main content</a>`) allows keyboard and screen reader users to skip repeated navigation menus.
2. **Keyboard Navigation**: High-contrast, glowing focus rings (`:focus-visible`) ensure complete keyboard accessibility without mouse dependence.
3. **Semantic HTML5**: Native `<nav>`, `<main>`, `<article>`, `<section>`, `<header>`, and `<footer>` elements provide structured document landmarks.
4. **Accessible Forms**: Explicit `<label>` elements with corresponding input `id` attributes and ARIA error descriptions.
5. **Reduced Motion**: Full support for `@media (prefers-reduced-motion: reduce)` which disables transforms, animations, and transitions for users with vestibular sensitivities.
