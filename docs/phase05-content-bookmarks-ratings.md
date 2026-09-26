# Fan Hub Plus — Phase 05 Technical Architecture
**Fandom Content Hubs, Interactive Lore, Bookmarks & Ratings**

---

## 1. Architectural Overview
Phase 05 establishes the core content discovery, interaction, and curation subsystem for the Fan Hub Plus universe. It connects the visual design system established in Phase 04 with real MySQL database models (`Content`, `Category`, `Genre`, `Bookmark`, `Rating`, `Media`, and `User`).

---

## 2. Content Explorer & Discovery Engine (`/explore`)

### 2.1 Multi-Field Search
- **Search Target**: Searches simultaneously across `title`, `description`, and full `body` text using indexed `LIKE %query%` grouping.
- **Safety**: Inputs are sanitized and bounded.

### 2.2 Faceted Filters
- **Fandom Realm**: Matches `category_id` via Category slug.
- **Genre**: Filters via `content_genre` pivot table relationship.
- **Content Format**: Validated enum whitelist (`article`, `video`, `trailer`, `audio`, `image`).
- **Release Year**: Bounded integer validation (`1970` to `2030`).

### 2.3 Safe Sorting Whitelist
Sort parameter strictly adheres to allowed keys:
- `latest`: Ordered by `published_at` desc, `id` desc.
- `popular`: Ordered by `popularity` desc, `views` desc.
- `alpha`: Ordered by `title` asc.
- `rating`: Ordered by eager loaded `ratings_avg` desc, `ratings_count` desc.

### 2.4 Active Filter Chips
- Generates chip tags in the UI summarizing active search/filter criteria.
- Each tag provides a clickable `x` to selectively remove individual query parameters while preserving the remaining criteria.

---

## 3. Content Detail Page & Canon Lore Experience (`/explore/{slug}`)

### 3.1 Canon Presentation
- Rich formatting of body text using newline preservation and XSS sanitization (`{!! nl2br(e($content->body)) !!}`).
- Category pill and genre tags (`#Action RPG`, `#Sci-Fi`).
- Publication date and platform author attribution.

### 3.2 Media & Video Embeds
- Embeds YouTube video or trailer when `media_url` is provided, transforming watch URLs into responsive iframe embeds (`ratio ratio-16x9`).
- Hero thumbnail fallback loaded from local storage disk (`storage/content/thumbnails`).

### 3.3 Related Content Engine
- Algorithm queries items in the same Category or having overlapping Genres (`whereIn('genres.id', $genreIds)`).
- Explicitly excludes the current item (`where('id', '!=', $content->id)`).
- Eager loads `category` and rating aggregates to avoid N+1 queries.
- Bounded to 3 items.

---

## 4. Polymorphic Bookmarking & Personal Notes (`bookmarks`)

### 4.1 Schema & Morph Map
- Utilizes the pre-existing `bookmarks` table:
  - `user_id` (foreign key)
  - `bookmarkable_type` (polymorphic class string)
  - `bookmarkable_id` (unsigned big integer)
  - `note` (text, nullable)
  - Unique composite index: `['user_id', 'bookmarkable_id', 'bookmarkable_type']`
- Allowed types whitelist: `Content`, `Character`, `Release`, `Event`.

### 4.2 Endpoints & UX
- `POST /bookmarks/toggle`:
  - Instant toggle action via JSON (AJAX) or fallback HTTP redirect.
  - Updates bookmark button icon (`bi-bookmark` vs `bi-bookmark-fill`) and reveals the personal note drawer.
- `POST /bookmarks/{bookmark}/note`:
  - Enforces strict user ownership (`$bookmark->user_id === auth()->id()`).
  - Validates note length (max 1,000 characters).
- `GET /bookmarks`:
  - Authenticated portal showing saved bookmarks with category badges and date saved.
  - Type tabs (All, Lore, Characters, Releases, Events).
  - Inline note editor.

---

## 5. 5-Star Community Ratings Engine (`ratings`)

### 5.1 Schema & Scoring
- Utilizes the pre-existing `ratings` table:
  - `user_id` (foreign key)
  - `ratable_type` (polymorphic class string)
  - `ratable_id` (unsigned big integer)
  - `score` (unsigned tiny integer: 1 to 5)
  - Unique composite index: `['user_id', 'ratable_id', 'ratable_type']`

### 5.2 Business Rules & Validation
- **Server Validation**: Integer between 1 and 5 (`between:1,5`).
- **No Duplicate Votes**: `Rating::updateOrCreate()` ensures a member only holds one vote per item.
- **Aggregate Recalculation**: Instant recalculation of average score and vote count returned in JSON response.
- **Vote Removal**: `DELETE /ratings` allows clearing a user's previous rating.

---

## 6. Admin Content CMS (`/admin/content`)

### 6.1 Administrator CRUD
- `GET /admin/content`: Tabular index of all content with thumbnail preview, categories, status badges, views, and action buttons.
- `GET /admin/content/create`: Content creation form with category select, genre checkboxes, status dropdown, thumbnail file upload, and YouTube media URL.
- `POST /admin/content`: Validates via `StoreContentRequest`, generates unique URL slug, securely stores uploaded thumbnail, and syncs genres.
- `GET /admin/content/{content}/edit`: Pre-populated edit form.
- `PUT /admin/content/{content}`: Validates via `UpdateContentRequest`, handles slug updates if title changed, deletes old thumbnail upon replacement, and updates status.
- `DELETE /admin/content/{content}`: Soft-deletes content.

### 6.2 Security & Protection
- Protected by `['auth', 'admin']` middleware (`EnsureUserIsAdmin`).
- CSRF protection across all forms and AJAX endpoints.
- Secure file upload validation (`mimes:jpeg,png,webp,gif`, max 2MB) stored in private storage mapped to public disk via standard symlink.
