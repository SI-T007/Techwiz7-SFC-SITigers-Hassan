# Fan Hub Plus — Demonstration Script (Mandatory MP4 Walkthrough)

This script guides the presenter or evaluator through the mandatory video demonstration showing all functional requirements of **Fan Hub Plus**.

**Target Duration:** ~8–12 minutes  
**Preparation Checklist:**
- Start Apache and MySQL in XAMPP (or run `php artisan serve`).
- Seed database: `php artisan migrate:fresh --seed`.
- Have credentials ready:
  - Admin: `admin@fanhub.test` / `Password@123`
  - User: `user@fanhub.test` / `Password@123`
- Open browser at `http://localhost:8000`.

---

## Act 1: Introduction & Public Experience (Visitor)

### 1. Title & Problem Overview (0:00 – 1:00)
- **Voiceover/Text:** "Welcome to Fan Hub Plus — the next-generation unified Fandom Universe platform celebrating Anime, Gaming, Movies, Comics, and Cosplay lore."
- **Action:** Show the Homepage hero section with dynamic glow styling, 8 core fandom universe realms, and live database platform statistics.

### 2. Homepage & Fandom Realms (1:00 – 2:15)
- **Action:** Scroll through the Homepage:
  - Fandom Universe categories (Anime, Gaming, Movies, TV Shows, etc.).
  - Featured Universe lore articles.
  - Release Radar countdowns.
  - Upcoming Fan Conventions with city tags.
  - Interactive Visual Sitemap in the footer.
- **Action:** Click into the **Anime Fandom Hub** (`/fandoms/anime`) to demonstrate category-filtered lore, related multimedia, and active events.

### 3. Content Explorer & Lore Dossiers (2:15 – 3:30)
- **Action:** Open **Universe Content Explorer** (`/explore`).
- **Action:** Search for *"Elden Ring"* and filter by Genre: *RPG*.
- **Action:** Click on the article to view the lore breakdown, author credits, and user ratings.
- **Action:** Toggle Dark / Light theme using the header button to showcase the responsive design system.

### 4. Multimedia Center & Events Calendar (3:30 – 4:45)
- **Action:** Navigate to **Multimedia Hub** (`/multimedia`). Filter by Video/Trailers.
- **Action:** Navigate to **Events** (`/events`), show city filter, then click **Interactive Calendar** (`/events/calendar`). Click an event to download an `.ics` calendar invitation.
- **Action:** Navigate to **Release Radar** (`/releases`) and filter by platform tag (e.g. `PC` or `PS5`).

---

## Act 2: Member Experience (Registered User)

### 5. Authentication & Member Dashboard (4:45 – 6:15)
- **Action:** Click **Sign In** (`/login`) and log in with `user@fanhub.test` / `Password@123`.
- **Action:** Tour the **Member Dashboard** (`/dashboard`):
  - Personal stats (Bookmarks, Submissions, Reviews, Chat Sessions).
  - Quick action shortcuts.
  - Tailored activity stream.
- **Action:** Open **Profile Settings** (`/profile/edit`): show favorite fandom selection, display preferences (font size, high contrast), and avatar.

### 6. Bookmarking, Notes & 5-Star Ratings (6:15 – 7:15)
- **Action:** Open a lore article. Click the **Bookmark** button. Add a personal note: *"Review build guide for DLC"*.
- **Action:** Navigate to `/bookmarks` to show the saved bookmark with the private annotation.
- **Action:** Submit a 5-star rating on the article and observe the real-time average score update.

### 7. Fan Creations & Submissions (7:15 – 8:15)
- **Action:** Go to **Community Hub** (`/community`) and click **Submit Fan Creation** (`/community/submit`).
- **Action:** Fill in a submission: Title: *"Geralt vs Sephiroth Lore Matchup"*, Type: *Theory*, Body: *"Detailed power scaling analysis..."*. Submit the form.
- **Action:** Show the "Pending Review" status in `/community/my-submissions`.

### 8. AI Smart Fan Assistant (8:15 – 9:30)
- **Action:** Click the floating **AI Assistant** icon in the bottom-right corner.
- **Action:** Ask a knowledge query: *"How do I submit fan art to the community?"* -> Observe immediate Knowledge Base matched reply.
- **Action:** Ask a dynamic database query: *"What is the release date for GTA VI?"* -> Observe database-aware answer.
- **Action:** Open `/chatbot/history` to show persisted conversation sessions.

---

## Act 3: Administrator Control Center

### 9. Admin Dashboard & User Management (9:30 – 10:45)
- **Action:** Sign out, then sign in as Administrator (`admin@fanhub.test` / `Password@123`).
- **Action:** Navigate to **Admin Control Center** (`/admin/dashboard`):
  - Tour live KPI cards, moderation queue widget, and live platform audit stream.
- **Action:** Go to **User Directory** (`/admin/users`), search for `user@fanhub.test`, click to inspect the comprehensive profile and audit log.

### 10. Community Moderation & Content CMS (10:45 – 11:30)
- **Action:** Go to **Moderation Queue** (`/admin/submissions`).
- **Action:** Find the submission submitted in Act 2 (*"Geralt vs Sephiroth Lore Matchup"*).
- **Action:** Review the content, click **Approve Work**.
- **Action:** Switch back to public `/community` to show the newly approved submission now live.

### 11. Chatbot CMS & Platform Analytics (11:30 – 12:30)
- **Action:** Open **Chatbot Knowledge Base CMS** (`/admin/chatbot/knowledge`), show Q&A editing and keyword tagging.
- **Action:** Open **Platform Analytics** (`/admin/analytics`):
  - Showcase date range filters (`Today`, `7 Days`, `30 Days`, `All Time`).
  - Review Active Users metrics, Popular Categories ranking, Chatbot response breakdown, and Moderation rates.

### 12. Conclusion & Architecture Wrap-Up (12:30 – 13:00)
- **Action:** Run `php artisan test` in terminal to show 161 passing automated tests.
- **Voiceover:** "Fan Hub Plus delivers a production-grade, secure, accessible, and database-driven Fandom Universe architecture adhering 100% to all SRS requirements. Thank you."
