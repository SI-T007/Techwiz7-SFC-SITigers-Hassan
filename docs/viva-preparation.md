# Fan Hub Plus — Technical Viva Voce Preparation Guide

This guide is designed for the student/presenter to confidently explain the architectural, technical, security, and design decisions of **Fan Hub Plus** during academic examination and viva evaluation.

---

## 1. Core Architecture & Stack Questions

### Q1: Why was Laravel selected as the backend framework?
**Answer:**
Laravel was chosen for its robust Model-View-Controller (MVC) architecture, expressive Eloquent ORM, built-in security features (CSRF protection, SQL injection prevention via PDO parameter binding, bcrypt password hashing), native routing pipelines, and powerful testing harness. It allows clean separation of concerns between business logic (Services/Controllers), database modeling (Eloquent Models), and presentation (Blade templates).

### Q2: Why MySQL / MariaDB for the database?
**Answer:**
MySQL provides ACID compliance, strong relational integrity through foreign key constraints, high performance for read-heavy content platforms, and native support for indexing and JSON columns. In Fan Hub Plus, MySQL supports 22 normalized migrations, polymorphic relationships for ratings and bookmarks, and indexed foreign keys for high query performance.

### Q3: Explain the MVC workflow in Fan Hub Plus.
**Answer:**
1. **Route (`routes/web.php`):** An incoming HTTP request is matched to a named route and filtered through middleware (e.g. `SetSecurityHeaders`, `auth`, `admin`).
2. **Controller (`app/Http/Controllers/`):** The controller receives the validated request (via Form Request or inline validation), invokes service logic if needed, queries models, and prepares data.
3. **Model (`app/Models/`):** Eloquent models manage database interaction, relationships (`hasMany`, `belongsTo`, `morphTo`), scopes, and attribute casting.
4. **View (`resources/views/`):** The controller returns a compiled Blade view rendering HTML with accessible semantic components.

---

## 2. Database & Data Modeling Questions

### Q4: What are Polymorphic Relationships, and where are they used?
**Answer:**
A polymorphic relationship allows a single model (e.g. `Bookmark` or `Rating`) to belong to multiple target models on a single association using two columns: `bookmarkable_type` (the model class name) and `bookmarkable_id` (the target model primary key).
- In Fan Hub Plus, `bookmarks` and `ratings` are polymorphic, enabling users to bookmark or rate `Content`, `Events`, `Releases`, or `Characters` without creating duplicate tables for each entity.

### Q5: How is database normalization maintained?
**Answer:**
The schema is normalized to 3NF (Third Normal Form):
- User profile preferences and avatars are separated into `user_profiles` with a 1:1 foreign key.
- Many-to-many relationships (e.g. content-genres, user-favorite categories) are handled via dedicated pivot tables (`content_genre`, `category_user`).
- Duplicate data is eliminated, and relational integrity is enforced via `cascadeOnDelete` and `nullOnDelete` constraints.

---

## 3. Security & Access Control Questions

### Q6: How does Role-Based Access Control (RBAC) work?
**Answer:**
The application defines three distinct clearance tiers:
1. **Visitor:** Public browsing, unauthenticated session.
2. **Registered User (`role = 'user'`):** Authenticated session via Laravel session cookies; access to member dashboard, submissions, ratings, and bookmarks.
3. **Administrator (`role = 'admin'`):** Privileged access strictly guarded by `EnsureUserIsAdmin` middleware.

### Q7: How does the platform defend against IDOR (Insecure Direct Object References)?
**Answer:**
Whenever a user requests a state change on a user-owned resource (such as deleting a bookmark note, removing a fan submission, or accessing a chat transcript), the backend verifies ownership server-side using `$model->user_id === auth()->id()`. If ownership does not match, an HTTP `403 Forbidden` response is returned immediately.

### Q8: What HTTP Security Headers are configured?
**Answer:**
The `SetSecurityHeaders` middleware automatically applies:
- `X-Content-Type-Options: nosniff` — Prevents MIME-type confusion attacks.
- `X-Frame-Options: SAMEORIGIN` — Mitigates clickjacking.
- `Referrer-Policy: strict-origin-when-cross-origin` — Protects referral leakage.
- `Permissions-Policy` — Restricts unneeded device APIs (camera, microphone, payment).

---

## 4. AI Chatbot Architecture Questions

### Q9: How does the AI Chatbot work without crashing if the AI API is offline or unconfigured?
**Answer:**
The `ChatbotService` utilizes a 4-tier orchestrator:
1. **Tier 1 (Knowledge Base):** Searches admin-curated Q&A pairs using normalized keyword array scoring.
2. **Tier 2 (Database Queries):** Executes parameterized lookups against MySQL for structured platform queries (e.g. upcoming releases, fandom events, character biographies).
3. **Tier 3 (Gemini AI Completion):** If enabled and `AI_API_KEY` is present, queries Google Gemini API with system guardrails.
4. **Tier 4 (Zero-Cost Fallback):** If the external API key is empty or the network times out, the system provides clean, pre-compiled platform guidance directly from the local database without throwing 500 errors.

### Q10: How is prompt injection prevented in the chatbot?
**Answer:**
- System prompts isolate user input from administrative instructions.
- The chatbot operates with read-only application logic; it **cannot** generate or execute dynamic SQL strings against MySQL.
- API keys and sensitive environment variables are strictly server-side and never returned in API payloads.

---

## 5. UI/UX & Accessibility Questions

### Q11: How does Dark/Light mode work and persist?
**Answer:**
Theme modes are managed via CSS Custom Properties (`data-bs-theme="dark"` / `"light"`). For visitors, theme choices persist in browser `localStorage`. For registered users, preferences are saved to `user_profiles.display_preferences` in the database, ensuring seamless multi-device persistence.

### Q12: How is WCAG accessibility compliance achieved?
**Answer:**
- **Color Contrast:** Curated color palettes with high-contrast text ratios against dark and light backgrounds.
- **Semantic HTML:** Strict heading hierarchy (`h1` through `h6`), ARIA labels on icon buttons, and accessible form labels.
- **Keyboard Navigation:** Focus rings and keyboard-tabbable interactive elements.
- **Reduced Motion:** CSS `@media (prefers-reduced-motion)` and user-configurable animation toggles.

---

## 6. Testing & Quality Assurance Questions

### Q13: How is software correctness validated?
**Answer:**
Fan Hub Plus has **161 automated feature tests with 528 assertions (0 failures, 0 errors)** executed using PHPUnit and Laravel's Testing framework. The tests cover authentication, authorization, CRUD workflows, moderation, chatbot responses, event exports, security headers, and analytics date filtering.
