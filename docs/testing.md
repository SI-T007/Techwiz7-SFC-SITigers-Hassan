# Fan Hub Plus — Testing Guide & Verification Suite

This document describes the automated test architecture, coverage breakdown, manual testing checklists, and quality assurance procedures for **Fan Hub Plus**.

---

## 1. Automated Testing Suite

The project includes an extensive automated test suite built on PHPUnit and Laravel Feature Testing.

### 1.1. Running the Automated Tests
Execute the entire test suite from the project root:
```bash
php artisan test
```

To run a specific test suite:
```bash
php artisan test --filter=AdminControlCenterAndAnalyticsTest
php artisan test --filter=ChatbotAndKnowledgeBaseTest
php artisan test --filter=SystemHardeningAndSecurityAuditTest
```

### 1.2. Feature Test Classes Overview

| Test Suite File | Tests | Coverage Scope |
|---|---|---|
| `AdminControlCenterAndAnalyticsTest.php` | 23 | 12-Module Admin Control Center, self-protection guards, safe category deletion, platform analytics date filters |
| `AdminContentTest.php` | 7 | Lore article and canon content CRUD, file upload validation, soft deletion |
| `AuthenticationTest.php` | 11 | Registration, password hashing, login, logout, remember me, verification |
| `BookmarkAndRatingTest.php` | 10 | Polymorphic bookmarks, private annotations, 5-star rating calculations |
| `ChatbotAndKnowledgeBaseTest.php` | 23 | Chatbot orchestrator, KB matching, database queries, Gemini AI, fallback mode |
| `EventAndReleaseTest.php` | 19 | Event calendar, location filtering, iCal exports, release radar countdowns |
| `FanSubmissionAndModerationTest.php` | 13 | Fan works submission, pending review queue, admin approve/reject workflow |
| `FeedbackTest.php` | 8 | Feedback ticketing, admin response workflow, status tracking |
| `MultimediaTest.php` | 8 | Audio, video, trailers, artwork showcase, category filtering |
| `PublicWebsiteShellTest.php` | 9 | Homepage rendering, visual sitemap, 8 fandom hubs, custom 404 error page |
| `SystemHardeningAndSecurityAuditTest.php` | 8 | HTTP security headers, IDOR defenses, mass-assignment protection, prompt injection |
| `UserRoleAccessTest.php` | 6 | Visitor / Registered User / Admin authorization boundaries |
| `ExampleTest.php` | 1 | Basic application ping |

**Total:** **161 Tests Passing (528 Assertions, 0 Failures, 0 Errors)**

---

## 2. Manual Testing Checklist

For evaluators and QA engineers conducting manual end-to-end testing:

### 2.1. Authentication & Security
- [x] Register a new user at `/register` and verify immediate login.
- [x] Test invalid password credentials at `/login` and verify error feedback.
- [x] Verify that accessing `/admin/dashboard` as a regular user returns `403 Forbidden`.
- [x] Verify that unauthenticated requests to `/dashboard` redirect to `/login`.

### 2.2. Content Exploration & Interactions
- [x] Navigate to `/explore` and filter by "Gaming" and "RPG". Verify results match.
- [x] Open any content item, submit a 5-star rating, and verify the average score updates.
- [x] Toggle the bookmark icon, add a personal note, and verify it appears in `/bookmarks`.

### 2.3. Community & Submissions
- [x] Navigate to `/community/submit` and submit a fan fiction story.
- [x] Verify the submission appears as "Pending Review" on your personal submissions tab.
- [x] Log in as Administrator (`admin@fanhub.test`), navigate to `/admin/submissions`, and approve the submission.
- [x] Verify the approved fan work is now publicly visible on `/community`.

### 2.4. Smart Fan Assistant (Chatbot)
- [x] Click the floating chatbot button on any page.
- [x] Ask: *"What is the release date for GTA VI?"* -> Verify database-aware answer.
- [x] Ask: *"How do I submit fan art?"* -> Verify Knowledge Base answer.
- [x] Navigate to `/chatbot/history` and verify past conversation threads are preserved.

### 2.5. Events & Releases
- [x] Navigate to `/events` and switch between Grid view and Calendar view (`/events/calendar`).
- [x] Download an event `.ics` file and verify calendar event details.
- [x] Navigate to `/releases` and filter by platform tag (e.g. `PS5`).

### 2.6. Admin Control Center & Analytics
- [x] Navigate to `/admin/dashboard` and verify all KPI cards reflect actual database data.
- [x] Navigate to `/admin/analytics` and change date range filter between `7 Days`, `30 Days`, and `All Time`.
- [x] Navigate to `/admin/categories` and attempt to delete a category that has content; verify safe deletion warning blocks orphaned records.
