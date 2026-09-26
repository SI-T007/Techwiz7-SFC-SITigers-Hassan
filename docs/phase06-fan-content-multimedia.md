# Phase 06 — Fan Content, Multimedia Center & Admin Moderation Architecture

## 1. Executive Architecture Summary
Phase 06 establishes the participatory creator layer and multimedia experience of the **Fan Hub Plus** platform. The implementation delivers four major interrelated systems:
1. **Fan Content Submission Workflow**: Registered members can submit original creations (Fan Art, Fan Fiction, Cosplay Photography, Video Essays, Lore Breakdowns) through a secure form request validation pipeline.
2. **Moderation Lifecycle & Queue**: User submissions start in `pending` review status. Administrators inspect submissions via `/admin/submissions`, with the power to approve (publishing them immediately to `/community`) or reject with structured review notes.
3. **Multimedia Center**: A unified media discovery engine (`/multimedia`) hosting video streams, cinematic trailers, accessible HTML5 audio players, and responsive visual galleries linked to canonical lore.
4. **Platform Feedback & Bug Reporting**: A feedback system (`/feedback`) allowing users and visitors to file bug reports, feature suggestions, or lore queries, with administrative reply tracking in `/admin/feedback`.

---

## 2. Database & Schema Reused
Phase 06 strictly reused existing Phase 02 database migrations without adding duplicate tables:
- **`fan_submissions` Table**:
  - `user_id`: Foreign key to `users` with cascade delete.
  - `category_id`: Foreign key to `categories` (nullable, null on delete).
  - `title`, `body`: Content fields for user text and writing.
  - `type`: Enum (`fan_art`, `fan_fiction`, `fan_video`, `cosplay`, `other`).
  - `featured_image`: Storage path to uploaded artwork/photography.
  - `external_url`: Validated URL for external portfolios or videos.
  - `status`: Enum (`pending`, `approved`, `rejected`) defaulting to `pending`.
  - `reviewed_by`: Foreign key to `users` (administrator id).
  - `rejection_reason`: Moderator feedback for rejected submissions.
  - `reviewed_at`, `published_at`, `deleted_at` (soft deletes enabled).
- **`media` Table**:
  - `category_id`, `content_id`: Foreign keys linking media assets to fandom realms and canonical articles.
  - `title`, `description`: Metadata fields.
  - `type`: Enum (`image`, `video`, `audio`, `trailer`).
  - `file_path`: Storage path for locally uploaded media.
  - `external_url`: Embedded video/audio links (YouTube, Vimeo, SoundCloud).
  - `status`: Enum (`draft`, `published`, `archived`).
  - `is_featured`: Boolean flag for carousel spotlights.
  - `views`: Integer counter for analytics.
- **`feedback` Table**:
  - `user_id`: Nullable foreign key for registered submitters.
  - `name`, `email`: Contact details.
  - `type`: Enum (`bug`, `suggestion`, `query`, `other`).
  - `subject`, `message`: Ticket text.
  - `status`: Enum (`new`, `read`, `replied`, `archived`).
  - `admin_reply`, `replied_by`, `replied_at`: Audit fields for administrative responses.

---

## 3. Fan Submission & Moderation Lifecycle
```
[User Submits Form]
        │
        ▼
   (Validates via StoreFanSubmissionRequest)
        │
        ▼
   (Saved with status = 'pending')
        │
   ┌────┴───────────────────────────┐
   │                                │
   ▼                                ▼
[Author Dashboard]          [Admin Moderation Queue]
(/community/my-submissions)   (/admin/submissions)
   - Status: Pending Review         │
   - Can Delete Draft Only          ▼
                            Admin Review Decision
                             ┌──────┴──────┐
                             │             │
                             ▼             ▼
                       [APPROVE]       [REJECT]
                             │             │
                             ▼             ▼
                     Status = 'approved'   Status = 'rejected'
                     Published At = now()  Reason = saved
                     Visible at /community Visible to author
```

### Authorization Rules:
- **Guests**: Blocked from `/community/submit` and `/community/my-submissions` (redirected to login).
- **Normal Users**:
  - Can submit creations.
  - Can view only their own submissions in `/community/my-submissions`.
  - Can delete unapproved (pending) submissions.
  - Forbidden from deleting approved submissions.
  - Forbidden from accessing `/admin/submissions` (HTTP 403).
  - Forbidden from invoking `/approve` or `/reject` endpoints (HTTP 403).
- **Administrators**:
  - Full access to `/admin/submissions`.
  - Single-click approve button which sets `reviewed_by = auth()->id()` and `published_at = now()`.
  - Rejection modal requiring or supporting detailed moderator feedback.

---

## 4. Multimedia Center Architecture
- **Public Catalog (`/multimedia`)**:
  - Multi-tab navigation: All Media, Videos & Trailers, Audio & Soundtracks, Concept Art & Wallpapers.
  - Search by title and description.
  - Filter by Fandom Category (`fandom={slug}`).
  - Eager loading of `category` and `content` relations to prevent N+1 query overhead.
- **Media Player & Detail Experience (`/multimedia/{media}`)**:
  - **Video & Trailers**: Responsive 16:9 iframe embeds safely transforming standard YouTube watch URLs (`youtube.com/watch?v=ID`) into sandboxed privacy-enhanced embed URLs (`youtube-nocookie.com/embed/ID`).
  - **Audio Streams**: Accessible HTML5 audio player with scrub bar, duration indicators, play/pause toggles, and volume control. Autoplay is strictly disabled to prevent audio disruption.
  - **Image Gallery**: Full-width responsive art viewer with zoom controls, resolution badges, and related lore links.
- **Admin CMS (`/admin/media`)**:
  - Full administrative CRUD interface for media assets.
  - Secure file uploads and thumbnail generation.
  - Content linking to associate media assets directly with canonical lore pages.

---

## 5. Security & Protection Measures
1. **CSRF Protection**: All POST, PUT, and DELETE forms utilize Laravel's `@csrf` tokens.
2. **Strict Server-Side Authorization**:
   - `admin` middleware enforces role checks on all moderation, media CMS, and feedback routes.
   - Resource-level checks verify `user_id === auth()->id()` before allowing deletion of submissions.
3. **Secure File Upload Handling**:
   - MIME type validation: Allowed images restricted to `jpeg, png, webp, jpg, gif`.
   - File size limits: Max 5MB per upload.
   - Storage isolation: Stored via `Storage::disk('public')` using random hash filenames; untrusted client filenames are never used directly.
   - Executable extension lockout: `.php`, `.phtml`, `.exe`, `.sh` strictly rejected.
4. **XSS Protection**:
   - Untrusted fan text is escaped using Blade's default `{{ $sub->body }}` engine.
   - Newlines are preserved using CSS `white-space: pre-wrap;` rather than unescaped raw `{!! nl2br() !!}` to avoid injecting script payloads.
5. **IDOR Prevention**:
   - Users cannot view unapproved submissions belonging to other users. Attempting to view an unapproved submission via `/community/submissions/{id}` returns HTTP 404 unless the user is the original author or an administrator.

---

## 6. Automated Testing Verification
All 3 dedicated feature test suites and the 6 prior test suites pass with 100% compliance:
- `tests/Feature/FanSubmissionAndModerationTest.php` (13 tests, 36 assertions)
- `tests/Feature/MultimediaTest.php` (8 tests, 32 assertions)
- `tests/Feature/FeedbackTest.php` (8 tests, 28 assertions)
- Total Suite: **77 tests, 268 assertions, 0 failures, 0 errors**.

---

## 7. Known Limitations & Recommendations
1. **Direct Video Encoding**: Video and trailer media utilize external URLs (YouTube/Vimeo) rather than hosting raw multi-gigabyte video files on the local server. This avoids heavy server CPU usage and disk exhaustion.
2. **Rich Text Formatting**: Fan writing currently supports plaintext with preserved formatting (`white-space: pre-wrap`). Phase 07 or 08 could introduce an accessible Markdown preview editor if rich formatting is desired.
