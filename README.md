# Fan Hub Plus — Next-Generation Fandom Universe Platform

> **The definitive web platform celebrating Anime, Gaming, Movies, TV Shows, K-Pop, Comics, Manga, and Cosplay lore.**

---

## 1. Project Overview

**Fan Hub Plus** is a full-stack Laravel/PHP web application developed to unify disparate fandom communities into a single cohesive, database-driven ecosystem. The platform delivers rich lore dossiers, interactive release radar countdowns, location-aware event discovery, member bookmarking with private notes, 5-star community reviews, fan creations moderation, a smart AI fan assistant, and a centralized 12-module Administrator Control Center.

---

## 2. Key Features

- **8 Core Fandom Realms**: Dedicated hubs for Anime, Gaming, Movies, TV Shows, K-Pop, Comics, Manga, and Cosplay.
- **Universe Content Explorer**: Real-time multi-facet filtering by category, genre, content format, and publication status.
- **Character Dossiers & Canon Lore**: In-depth character profiles with ability tags, biographies, and spotlight features.
- **Interactive Event Discovery**: Filterable event schedule, interactive calendar view, city/venue filtering, and one-click `.ics` calendar exports.
- **Release Radar**: Live countdowns for upcoming games, anime seasons, movies, and TV series with platform tags.
- **Polymorphic Bookmarks & Private Notes**: Save any lore item, event, or release with private annotations.
- **5-Star Community Ratings**: Dynamic, atomic average score calculations with duplicate review prevention.
- **Fan Creations & Community Hub**: Member submissions for fan art, fiction, cosplay, and theories with an administrative review queue.
- **Smart AI Fan Assistant (Chatbot)**: Multi-tier assistant with Knowledge Base matching, parameterized database queries, Gemini AI completion, and zero-cost fallback mode.
- **Admin Control Center**: 12 administrative modules with live KPI cards, moderation queues, self-protection rules, and real-time database-driven platform analytics.
- **Accessibility & Theme System**: Dark/Light mode toggle, high contrast options, font scaling, and semantic WCAG-compliant design.

---

## 3. Technology Stack

- **Backend:** PHP 8.2+ / Laravel 12.x / Eloquent ORM / Form Requests / Custom Middleware
- **Database:** MySQL / MariaDB (22 Migrations, Indexed Foreign Keys, Relational Constraints)
- **Frontend:** Laravel Blade / Vanilla CSS (Custom Design System) / ES6+ JavaScript (AJAX, Fetch)
- **Icons & Styling:** Bootstrap Icons / Custom Glassmorphism UI
- **Testing:** PHPUnit / Laravel Feature Testing (161 Tests Passing, 528 Assertions)

---

## 4. Quick Start Installation Guide

### Prerequisites
- PHP 8.2+ with `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `xml`, `ctype`, `json` extensions.
- Composer 2.x
- MySQL / MariaDB 10.4+

### Installation Steps
```bash
# 1. Clone or extract project
cd "Fan Hub Plus"

# 2. Install Composer dependencies
composer install

# 3. Configure environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure MySQL database in .env
# DB_DATABASE=fanhubplus
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Run database migrations & seed development data
php artisan migrate --seed

# 7. Create storage symlink for uploaded media
php artisan storage:link

# 8. Start local server
php artisan serve
```
Access the application at: **`http://localhost:8000`**

*(For detailed XAMPP instructions, see [`docs/installation.md`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/docs/installation.md))*

---

## 5. Development & Demo Credentials

| User Type | Email Address | Password | Clearances |
|---|---|---|---|
| **Visitor** | *(No login required)* | N/A | Public content, search, calendar, sitemap |
| **Registered User** | `user@fanhub.test` | `Password@123` | Bookmarks, notes, ratings, fan submissions, chatbot history |
| **Administrator** | `admin@fanhub.test` | `Password@123` | Full 12-Module Admin Control Center, moderation, analytics |

*(Note: These are pre-seeded development/demo credentials only).*

---

## 6. Automated Test Suite

Fan Hub Plus includes a comprehensive automated test suite verifying all 10 implementation phases:

```bash
php artisan test
```

**Test Results:**
```text
Tests:    161 passed
Assertions: 528
Failures: 0
Errors:   0
Duration: ~29s
```

---

## 7. Project Documentation Directory

| Document | Purpose |
|---|---|
| [`docs/installation.md`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/docs/installation.md) | Comprehensive setup guide (Composer, XAMPP, environment config) |
| [`docs/user-guide.md`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/docs/user-guide.md) | Visitor and member feature walkthrough |
| [`docs/admin-guide.md`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/docs/admin-guide.md) | Admin Control Center & moderation workflows |
| [`docs/database-design.md`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/docs/database-design.md) | ERD diagrams, table schemas, relations, and foreign keys |
| [`docs/system-audit.md`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/docs/system-audit.md) | Security, hardening, and performance audit report |
| [`docs/testing.md`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/docs/testing.md) | Automated test suite details and manual QA checklists |
| [`docs/demo-script.md`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/docs/demo-script.md) | Walkthrough script for mandatory MP4 video demonstration |
| [`docs/ai-usage.md`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/docs/ai-usage.md) | Academic transparency and AI tool usage acknowledgement |
| [`docs/final-project-overview.md`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/docs/final-project-overview.md) | Formal academic project report and problem solution overview |
| [`docs/final-srs-checklist.md`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/docs/final-srs-checklist.md) | Complete 100% SRS traceability matrix |

---

## 8. Academic Project Information

- **Project Title:** Fan Hub Plus — Fandom Universe Platform
- **Architecture:** Senior Full-Stack Laravel Architecture
- **Constraints Compliance:** Showcase-only merchandise (strictly no cart/checkout per SRS), external event ticketing links, zero fabricated analytics metrics.
