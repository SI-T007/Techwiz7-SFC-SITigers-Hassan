# Fan Hub Plus — Database Architecture & Schema Design

This document details the complete relational database architecture of **Fan Hub Plus**, including table structures, relationships, foreign key constraints, indexes, and design rationale.

---

## 1. High-Level Entity-Relationship (ER) Overview

```text
User (users)
 ├── UserProfile (user_profiles) [1:1]
 ├── FavoriteCategories (category_user) [M:N]
 ├── Content (contents) [1:M as author]
 ├── FanSubmissions (fan_submissions) [1:M]
 ├── Bookmarks (bookmarks) [1:M polymorphic]
 ├── Ratings (ratings) [1:M polymorphic]
 ├── Feedback (feedback) [1:M]
 ├── ChatbotConversations (chatbot_conversations) [1:M]
 └── UserActivities (user_activities) [1:M]

Category (categories)
 ├── Contents (contents) [1:M]
 ├── Characters (characters) [1:M]
 ├── Events (events) [1:M]
 ├── Releases (releases) [1:M]
 ├── Media (media) [1:M]
 └── FanSubmissions (fan_submissions) [1:M]

Content (contents)
 ├── Genres (content_genre) [M:N]
 ├── Media (media) [1:M]
 ├── Bookmarks (bookmarks) [1:M polymorphic]
 └── Ratings (ratings) [1:M polymorphic]

ChatbotConversation (chatbot_conversations)
 └── Messages (chatbot_messages) [1:M]
```

---

## 2. Table Specifications & Constraints

### 2.1. `users`
Core identity table supporting Visitor, Registered User, and Administrator roles.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `name`: VARCHAR(255) NOT NULL
- `email`: VARCHAR(255) NOT NULL UNIQUE
- `email_verified_at`: TIMESTAMP NULL
- `password`: VARCHAR(255) NOT NULL (bcrypt hashed)
- `role`: ENUM('user', 'admin') DEFAULT 'user'
- `is_active`: BOOLEAN DEFAULT true
- `remember_token`: VARCHAR(100) NULL
- `created_at`, `updated_at`: TIMESTAMP

### 2.2. `user_profiles`
Personalization and user preferences.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `user_id`: BIGINT UNSIGNED NOT NULL (FK -> users.id, ON DELETE CASCADE) UNIQUE
- `display_name`: VARCHAR(255) NULL
- `bio`: TEXT NULL
- `avatar`: VARCHAR(255) NULL
- `location`: VARCHAR(255) NULL
- `website`: VARCHAR(255) NULL
- `display_preferences`: JSON NULL (theme, language, animations)
- `accessibility_preferences`: JSON NULL (reduced_motion, high_contrast, font_size)

### 2.3. `categories`
Fandom Universe realms (Anime, Gaming, Movies, TV Shows, K-Pop, Comics, Manga, Cosplay).
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `name`: VARCHAR(255) NOT NULL
- `slug`: VARCHAR(255) NOT NULL UNIQUE
- `description`: TEXT NULL
- `icon`: VARCHAR(100) NULL (Bootstrap icon class)
- `image`: VARCHAR(255) NULL
- `status`: ENUM('active', 'inactive') DEFAULT 'active'
- `sort_order`: INT DEFAULT 0

### 2.4. `contents`
Canon universe lore, articles, and guides.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `category_id`: BIGINT UNSIGNED NOT NULL (FK -> categories.id, ON DELETE CASCADE)
- `user_id`: BIGINT UNSIGNED NULL (FK -> users.id, ON DELETE SET NULL)
- `title`: VARCHAR(500) NOT NULL
- `slug`: VARCHAR(500) NOT NULL UNIQUE
- `type`: ENUM('article', 'lore', 'guide', 'news') DEFAULT 'article'
- `description`: TEXT NULL
- `body`: LONGTEXT NULL (Markdown format)
- `thumbnail`: VARCHAR(500) NULL
- `media_url`: VARCHAR(1000) NULL
- `status`: ENUM('draft', 'published', 'archived') DEFAULT 'draft'
- `is_featured`: BOOLEAN DEFAULT false
- `views`: INT UNSIGNED DEFAULT 0
- `popularity`: INT UNSIGNED DEFAULT 0
- `release_date`: DATE NULL

### 2.5. `characters`
Character profiles and dossiers.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `category_id`: BIGINT UNSIGNED NOT NULL (FK -> categories.id, ON DELETE CASCADE)
- `name`: VARCHAR(255) NOT NULL
- `slug`: VARCHAR(255) NOT NULL UNIQUE
- `role`: VARCHAR(100) NULL (e.g. Protagonist, Antagonist, Supporting)
- `description`: TEXT NULL
- `abilities`: JSON NULL (array of strings)
- `image`: VARCHAR(500) NULL
- `is_featured`: BOOLEAN DEFAULT false

### 2.6. `events`
Conventions, tournaments, and fan gatherings.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `category_id`: BIGINT UNSIGNED NOT NULL (FK -> categories.id, ON DELETE CASCADE)
- `title`: VARCHAR(255) NOT NULL
- `slug`: VARCHAR(255) NOT NULL UNIQUE
- `description`: TEXT NULL
- `start_date`: DATETIME NOT NULL
- `end_date`: DATETIME NULL
- `location`: VARCHAR(255) NOT NULL (City, Country / Venue)
- `is_online`: BOOLEAN DEFAULT false
- `ticket_url`: VARCHAR(500) NULL (External ticketing link)
- `image`: VARCHAR(500) NULL
- `status`: ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming'

### 2.7. `releases`
Release Radar tracker for upcoming games, anime, movies, and TV seasons.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `category_id`: BIGINT UNSIGNED NOT NULL (FK -> categories.id, ON DELETE CASCADE)
- `title`: VARCHAR(255) NOT NULL
- `slug`: VARCHAR(255) NOT NULL UNIQUE
- `type`: ENUM('game', 'anime_season', 'movie', 'tv_season', 'comic_issue', 'merchandise', 'other') NOT NULL
- `release_date`: DATE NOT NULL
- `status`: ENUM('upcoming', 'released', 'delayed', 'cancelled') DEFAULT 'upcoming'
- `description`: TEXT NULL
- `thumbnail`: VARCHAR(500) NULL
- `external_url`: VARCHAR(500) NULL
- `tags`: JSON NULL (e.g. ["PS5", "PC", "Crunchyroll"])

### 2.8. `bookmarks`
Polymorphic bookmarking system with personal annotations.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `user_id`: BIGINT UNSIGNED NOT NULL (FK -> users.id, ON DELETE CASCADE)
- `bookmarkable_type`: VARCHAR(255) NOT NULL (Morph type)
- `bookmarkable_id`: BIGINT UNSIGNED NOT NULL
- `note`: TEXT NULL (Private user annotation)
- UNIQUE (`user_id`, `bookmarkable_id`, `bookmarkable_type`)

### 2.9. `ratings`
Polymorphic 5-star community rating system.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `user_id`: BIGINT UNSIGNED NOT NULL (FK -> users.id, ON DELETE CASCADE)
- `ratable_type`: VARCHAR(255) NOT NULL (Morph type)
- `ratable_id`: BIGINT UNSIGNED NOT NULL
- `score`: TINYINT UNSIGNED NOT NULL (1 to 5)
- UNIQUE (`user_id`, `ratable_id`, `ratable_type`)

### 2.10. `fan_submissions`
Fan creative works moderation system.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `user_id`: BIGINT UNSIGNED NOT NULL (FK -> users.id, ON DELETE CASCADE)
- `category_id`: BIGINT UNSIGNED NULL (FK -> categories.id, ON DELETE SET NULL)
- `title`: VARCHAR(500) NOT NULL
- `body`: TEXT NULL
- `type`: ENUM('fan_art', 'fan_fiction', 'fan_video', 'cosplay', 'other') DEFAULT 'other'
- `featured_image`: VARCHAR(500) NULL
- `external_url`: VARCHAR(1000) NULL
- `status`: ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'
- `reviewed_by`: BIGINT UNSIGNED NULL (FK -> users.id, ON DELETE SET NULL)
- `rejection_reason`: TEXT NULL
- `reviewed_at`: TIMESTAMP NULL
- `published_at`: TIMESTAMP NULL
- `deleted_at`: TIMESTAMP NULL (SoftDeletes)

### 2.11. `feedback`
User feedback ticketing.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `user_id`: BIGINT UNSIGNED NULL (FK -> users.id, ON DELETE SET NULL)
- `name`: VARCHAR(255) NOT NULL
- `email`: VARCHAR(255) NOT NULL
- `type`: ENUM('bug', 'suggestion', 'query') DEFAULT 'suggestion'
- `subject`: VARCHAR(255) NOT NULL
- `message`: TEXT NOT NULL
- `status`: ENUM('new', 'in_progress', 'replied', 'closed') DEFAULT 'new'
- `admin_notes`: TEXT NULL

### 2.12. `chatbot_knowledge_bases`
Admin-curated FAQ and platform Q&A entries.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `category_id`: BIGINT UNSIGNED NULL (FK -> categories.id, ON DELETE SET NULL)
- `question`: VARCHAR(500) NOT NULL
- `answer`: TEXT NOT NULL
- `keywords`: JSON NULL (Array of keyword strings for normalized scoring)
- `is_active`: BOOLEAN DEFAULT true
- `helpful_count`: INT UNSIGNED DEFAULT 0

### 2.13. `chatbot_conversations` & `chatbot_messages`
Chatbot interaction history and telemetry.
- **`chatbot_conversations`**:
  - `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
  - `user_id`: BIGINT UNSIGNED NULL (FK -> users.id, ON DELETE CASCADE)
  - `session_id`: VARCHAR(255) NULL
  - `title`: VARCHAR(255) DEFAULT 'New Conversation'
- **`chatbot_messages`**:
  - `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
  - `conversation_id`: BIGINT UNSIGNED NOT NULL (FK -> chatbot_conversations.id, ON DELETE CASCADE)
  - `sender`: ENUM('user', 'assistant') NOT NULL
  - `message`: TEXT NOT NULL
  - `source`: ENUM('knowledge_base', 'database', 'ai', 'fallback') DEFAULT 'fallback'

### 2.14. `user_activities`
Audit logging and engagement tracking.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `user_id`: BIGINT UNSIGNED NOT NULL (FK -> users.id, ON DELETE CASCADE)
- `activity_type`: VARCHAR(100) NOT NULL
- `description`: VARCHAR(255) NULL
- `metadata`: JSON NULL
- `ip_address`: VARCHAR(45) NULL
