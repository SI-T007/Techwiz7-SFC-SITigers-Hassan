# Fan Hub Plus — Administrator Control Center Guide

This guide details all 12 modules within the **Fan Hub Plus Administrator Control Center** (`/admin`), outlining operational workflows, moderation procedures, self-protection rules, and telemetry analytics.

---

## 1. Accessing the Admin Console

- **Route:** [`/admin/dashboard`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/resources/views/admin/dashboard.blade.php)
- **Clearance:** Requires `role = 'admin'` and active account status (`is_active = 1`).
- **Security:** All `/admin/*` routes are protected server-side by `auth` and `EnsureUserIsAdmin` middleware.

---

## 2. Admin Modules Breakdown

### 2.1. Unified Dashboard (`/admin/dashboard`)
- **Key Metrics Grid**: Live counters for Registered Users, Universe Content, Fan Works, Feedback Tickets, Scheduled Events, Releases, and Chatbot Interactions.
- **Moderation Queue Status**: Real-time breakdown of pending, approved, and rejected submissions.
- **Recent Registrations**: Latest members with role badges and registration dates.
- **Platform Audit Stream**: Live chronological feed of user actions across the platform.

### 2.2. User Accounts & Access Control (`/admin/users`)
- **Directory**: Paginated user list with email/name search and role filtering (`user` vs `admin`).
- **User Detail Inspection (`/admin/users/{user}`)**: In-depth view of member profile, submission history, bookmarks, ratings, feedback, and chronological audit log.
- **Account Actions**:
  - Activate or Deactivate accounts with a single toggle.
  - Elevate or Demote roles (`user` <-> `admin`).
- **Self-Protection Rules**:
  - Administrators cannot deactivate their own active account.
  - Administrators cannot demote their own administrative role.
  - The system prevents removing or demoting the final administrator on the platform.

### 2.3. Categories & Taxonomy CMS (`/admin/categories`)
- Create and edit fandom realms with title, slug, icon classes, descriptions, and active status.
- **Safe Deletion**: Deletion is blocked if the category has linked contents, characters, events, releases, media, or submissions, preventing database foreign-key errors.

### 2.4. Universe Content CMS (`/admin/content`, `/admin/articles`)
- Create and publish canon lore entries, guides, and featured articles.
- Tag with multiple genres, assign fandom categories, upload cover artwork, set release dates, and toggle featured spotlight status.

### 2.5. Character Profiles CMS (`/admin/characters`)
- Manage character dossiers, ability badges, origin fandom assignments, biographies, and avatar image uploads.

### 2.6. Multimedia Center CMS (`/admin/media`)
- Upload or embed official trailers, game clips, soundtracks, and high-resolution concept art.
- Assign media types (video, audio, image) and categorize by fandom.

### 2.7. Event Schedule Management (`/admin/events`)
- Schedule fan conventions, esports tournaments, and livestreams.
- Set venue locations, date ranges, attendance format, and external ticketing URLs.

### 2.8. Release Radar Management (`/admin/releases`)
- Manage upcoming release entries for video games, anime seasons, movies, and TV series.
- Configure platform tags (PS5, PC, Switch, Crunchyroll, Netflix), release countdowns, and official store URLs.

### 2.9. Community Moderation Queue (`/admin/submissions`)
- **Review Queue**: Filter submissions by status (`Pending`, `Approved`, `Rejected`).
- **Inspection**: Preview submitted story text, images, and external video links.
- **Action**: One-click Approve to publish immediately to the community hub, or Reject with a detailed explanation sent back to the submitter.

### 2.10. Feedback Supervision (`/admin/feedback`)
- Manage incoming user bug reports, feature suggestions, and inquiries.
- Reply to tickets and update status (`New`, `In Progress`, `Resolved`, `Closed`).

### 2.11. Chatbot Management (`/admin/chatbot`)
- **Knowledge Base CMS (`/admin/chatbot/knowledge`)**: Add, edit, and toggle Q&A pairs with keyword arrays, category alignment, and helpful counter tracking.
- **Conversation Telemetry (`/admin/chatbot/conversations`)**: Privacy-preserving overview of conversation volume, user queries, and transcript moderation.

### 2.12. Platform Analytics & Telemetry (`/admin/analytics`)
- **Date Range Filtering**: Analyze statistics across `Today`, `7 Days`, `30 Days`, `90 Days`, and `All Time`.
- **Database Metrics**: Active user counts based on recent activity logs, popular fandom categories, response source distribution (Knowledge Base vs Database vs AI vs Fallback), and moderation metrics.
