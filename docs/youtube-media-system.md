# Fan Hub Plus — YouTube Multimedia System Documentation

## Overview
The Fan Hub Plus **Multimedia Center** is a high-performance entertainment media hub designed for streaming official anime trailers, gaming cinematics, movie and TV trailers, K-Pop music videos, and official soundtrack themes (OSTs). Content is integrated using direct **YouTube links/embeds** with lazy-loaded players rather than local video hosting.

---

## 1. Supported YouTube URL Formats
The system safely parses, validates, and normalizes standard, shortened, and embedded YouTube URLs:

| Format Pattern | Example Input | Normalized Video ID |
| :--- | :--- | :--- |
| **Standard Watch URL** | `https://www.youtube.com/watch?v=M_OauHnAFc8` | `M_OauHnAFc8` |
| **Shortened youtu.be** | `https://youtu.be/M_OauHnAFc8` | `M_OauHnAFc8` |
| **Direct Embed URL** | `https://www.youtube.com/embed/M_OauHnAFc8` | `M_OauHnAFc8` |
| **YouTube Shorts** | `https://www.youtube.com/shorts/M_OauHnAFc8` | `M_OauHnAFc8` |
| **URLs with query parameters** | `https://www.youtube.com/watch?v=M_OauHnAFc8&t=45s` | `M_OauHnAFc8` |

### Extraction Logic
Extracted server-side in [`Media::extractYouTubeId()`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/app/Models/Media.php#L72-L82):
```php
public static function extractYouTubeId(?string $url): ?string
{
    if (empty($url)) return null;
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
    if (preg_match($pattern, $url, $matches)) {
        return $matches[1];
    }
    return null;
}
```

---

## 2. Safe Embed Generation & Security
To prevent XSS and arbitrary iframe injection:
- **No arbitrary URLs**: Administrators cannot inject raw iframe HTML.
- **Whitelist construction**: Embed URLs are dynamically compiled strictly from the validated 11-character YouTube video ID:
  ```php
  public function getYoutubeEmbedUrlAttribute(): ?string
  {
      if (!$this->youtube_video_id) return null;
      return "https://www.youtube.com/embed/{$this->youtube_video_id}?rel=0&modestbranding=1";
  }
  ```
- **Strict Iframe Sandbox & Attributes**:
  - `allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"`
  - `allowfullscreen`
  - `loading="lazy"`
  - `title="{{ $media->title }}"`

---

## 3. Thumbnail Strategy
YouTube thumbnails are served dynamically via YouTube's public CDN:
- **Primary**: `https://img.youtube.com/vi/{VIDEO_ID}/maxresdefault.jpg`
- **Fallback**: Automatically falls back to `hqdefault.jpg` via client-side `onerror` handler in Blade templates.

---

## 4. Controlled Media Types & Quality Badges

### Media Types (`media_type`)
- `anime_trailer`: Anime Trailer
- `anime_opening`: Anime Opening
- `anime_ending`: Anime Ending
- `anime_song`: Anime Song / OST
- `gaming_trailer`: Gaming Trailer
- `gaming_cinematic`: Gaming Cinematic
- `gaming_gameplay`: Gaming Gameplay
- `movie_trailer`: Movie Trailer
- `movie_teaser`: Movie Teaser
- `movie_song`: Movie Song / OST
- `tv_trailer`: TV Trailer
- `tv_featurette`: TV Featurette
- `music_video`: K-Pop / Music Video
- `character_video`: Character Video
- `behind_scenes`: Behind the Scenes
- `other`: Other

### Quality Metadata (`quality`)
- `SD` (Standard Definition)
- `HD` (High Definition / 720p)
- `Full HD` (1080p)
- `4K` (Ultra High Definition / 2160p)
- `Unknown`

> Quality badges represent verified administrator metadata. Playback quality is dynamically handled by YouTube's adaptive player.

---

## 5. 5-Star Interactive Rating System
- **Polymorphic Model**: Uses the central [`Rating`](file:///c:/xampp/htdocs/Fan%20Hub%20Plus/app/Models/Rating.php) model (`ratable_type: 'media'`).
- **One User = One Rating**: Users can rate 1 to 5 stars. Submitting a new rating updates their existing database record.
- **Real-Time Aggregates**: Average scores and total rating counts are computed directly from the MySQL database using SQL aggregations:
  ```php
  $media = Media::withAvg('ratings as ratings_avg', 'score')
      ->withCount('ratings')
      ->findOrFail($id);
  ```
- **Asynchronous AJAX Submission**: Allows rating and clearing votes without full page reloads, updating the aggregate score pill and star fills dynamically.

---

## 6. Admin Management Workflow
1. Navigate to **Admin Control Center -> Multimedia Assets** (`/admin/media`).
2. Filter by media type, quality, category, publication status, or keyword search.
3. Click **Add Media Asset** (`/admin/media/create`).
4. Enter the YouTube URL, select the media type, assign quality badge, enter release metadata, and save.
5. Edit, toggle featured status, publish/unpublish, or delete with full CSRF protection.

---

## 7. Performance & Lazy-Loading Approach
- **No Iframe Flooding**: The listing page (`/multimedia`) renders lightweight responsive 16:9 poster cards with YouTube thumbnails and SVG play overlays. No iframes are embedded on index pages.
- **On-Demand Embed**: The 16:9 YouTube iframe is only initialized when navigating to the individual media show page (`/multimedia/{id}`).
