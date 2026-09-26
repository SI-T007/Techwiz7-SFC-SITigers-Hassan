# FAN HUB+ MOTION DESIGN & ANIMATION SYSTEM

## 1. Animation Philosophy

Motion in **Fan Hub+** is designed to create a **cinematic, responsive, and tactile streaming experience**. Animations are not decorative distractions; they communicate state changes, guide visual attention, and provide feedback on user intent.

All animations use GPU-accelerated CSS properties (`transform`, `opacity`) and vanilla JavaScript observers to ensure **60+ FPS performance on mobile and desktop devices**.

---

## 2. Animation Categories & Implementations

### A. Navigation Dynamics
- **Transparent-to-Solid Scroll Transition**:
  - Initial state: Transparent background over the cinematic hero banner.
  - Scrolled state (`window.scrollY > 40`): Smoothly transitions to solid `#141414` with backdrop blur (`backdrop-filter: blur(16px)`), height reduction from `68px` to `60px`, and subtle bottom border shadow.
  - Duration: `350ms`, Easing: `ease`.
- **Expandable Search Bar**:
  - Collapsed state: Icon-only button (`38px` width).
  - Active state: Expands smoothly to `240px` with focus on input and subtle red glow highlight.

### B. Hero Banner & Ken Burns Motion
- **Ken Burns Slow Zoom Effect**:
  - Image scales continuously from `scale(1)` to `scale(1.05)` over `12s` with alternate direction.
  - Overlaid with vertical and horizontal linear gradients to ensure maximum legibility for hero text and CTA buttons.
- **Soundwave Equalizer Badge**:
  - 4 pulsating animated vertical bars (`@keyframes soundwave`) oscillating at varied animation delays (`0.1s`, `0.3s`, `0.5s`, `0.2s`) to denote active media.

### C. Content Card Interaction & Spring Physics
- **Hover Lift & Scale**:
  - Transform: `translateY(-6px) scale(1.05)`.
  - Transition curve: `cubic-bezier(0.2, 0.8, 0.2, 1)` over `320ms`.
  - Box shadow: `0 16px 36px rgba(0, 0, 0, 0.8)`.
- **Teaser Sweep Shine**:
  - Pseudo-element gradient beam sweeps across the 16:9 poster on hover (`translateX(-100%)` to `translateX(200%)` over `700ms`).
- **Metadata Fade-in**:
  - Rating, match percentage badge, and quick-action buttons reveal upon card elevation.

### D. Horizontal Slider Track (Carousel Rails)
- **Smooth Scroll Physics**:
  - `scroll-behavior: smooth`, `scroll-snap-type: x mandatory`.
  - Chevron controls calculate container client width (`0.75 * clientWidth`) to shift view smoothly.
  - Infinite scroll indicator and active slide tracking.

### E. Telemetry Number Count-Up
- **IntersectionObserver Trigger**:
  - Triggers when stats section enters viewport.
  - Linear/ease-out interpolation counts from `0` to target dataset values (e.g. `12,840+`, `1,420+`) over `1500ms`.

### F. Floating Toast System
- **Micro-Notification Dispatcher**:
  - Appears from bottom right with `@keyframes toastSlideIn` (`translateY(20px)` to `0`, `opacity: 0` to `1`).
  - Auto-dismisses after 3.5 seconds with fade-out.

---

## 3. Reduced Motion Compliance (`prefers-reduced-motion`)

For users who have requested reduced motion in operating system preferences:
```css
@media (prefers-reduced-motion: reduce) {
    *, ::before, ::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
    .fh-hero-backdrop img {
        animation: none !important;
        transform: none !important;
    }
}
```

---

## 4. Performance Benchmarks
- **Zero Heavy Third-Party Animation Libraries**: Accomplished using native CSS3 Transitions/Keyframes and Vanilla JS without external overhead.
- **Paint Optimization**: Utilizes `will-change: transform` only on active hover states to avoid excessive GPU layer allocation.
