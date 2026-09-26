<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Character;
use App\Models\Content;
use App\Models\Event;
use App\Models\Genre;
use App\Models\Media;
use App\Models\Rating;
use App\Models\Release;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    /**
     * Homepage Portal (Netflix OTT Streaming Experience)
     */
    public function home(): View
    {
        $categories = Category::active()->withCount('contents')->get();
        
        $featuredContents = Content::with(['category', 'genres'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->published()
            ->featured()
            ->latest()
            ->take(5)
            ->get();

        if ($featuredContents->isEmpty()) {
            $featuredContents = Content::with(['category', 'genres'])
                ->withAvg('ratings as ratings_avg', 'score')
                ->withCount('ratings')
                ->published()
                ->latest()
                ->take(5)
                ->get();
        }

        $trendingContents = Content::with(['category', 'genres'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->published()
            ->orderByDesc('views')
            ->take(10)
            ->get();

        $animeContents = Content::with(['category', 'genres'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->published()
            ->whereHas('category', fn($q) => $q->where('slug', 'anime'))
            ->take(10)
            ->get();

        $gamingContents = Content::with(['category', 'genres'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->published()
            ->whereHas('category', fn($q) => $q->where('slug', 'gaming'))
            ->take(10)
            ->get();

        $movieContents = Content::with(['category', 'genres'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->published()
            ->whereHas('category', fn($q) => $q->whereIn('slug', ['movies', 'tv-shows', 'comics']))
            ->take(10)
            ->get();

        $characters = Character::with('category')->active()->featured()->take(10)->get();
        if ($characters->isEmpty()) {
            $characters = Character::with('category')->active()->take(10)->get();
        }

        $upcomingReleases = Release::with('category')->upcoming()->take(10)->get();
        $upcomingEvents = Event::with('category')->upcoming()->take(6)->get();

        $stats = [
            'categories' => Category::count(),
            'contents' => Content::published()->count(),
            'characters' => Character::count(),
            'users' => User::count(),
        ];

        return view('welcome', compact(
            'categories',
            'featuredContents',
            'trendingContents',
            'animeContents',
            'gamingContents',
            'movieContents',
            'characters',
            'upcomingReleases',
            'upcomingEvents',
            'stats'
        ));
    }

    /**
     * Dedicated Anime Universe Hub
     */
    public function anime(Request $request): View
    {
        $category = Category::where('slug', 'anime')->firstOrFail();

        // 1. Spotlight Hero Banner
        $spotlight = Content::with(['category', 'genres'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->published()
            ->where('category_id', $category->id)
            ->where('is_featured', true)
            ->latest()
            ->first();

        if (!$spotlight) {
            $spotlight = Content::with(['category', 'genres'])
                ->withAvg('ratings as ratings_avg', 'score')
                ->withCount('ratings')
                ->published()
                ->where('category_id', $category->id)
                ->latest()
                ->first();
        }

        // 2. Trending & High-Velocity Anime
        $trendingAnime = Content::with(['category', 'genres'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->published()
            ->where('category_id', $category->id)
            ->orderByDesc('views')
            ->take(8)
            ->get();

        // 3. Filterable / Searchable Anime Catalog
        $query = Content::with(['category', 'genres'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->published()
            ->where('category_id', $category->id);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('genre')) {
            $genreSlug = $request->input('genre');
            $query->whereHas('genres', fn($q) => $q->where('slug', $genreSlug));
        }

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'popular' => $query->orderByDesc('views'),
            'rating' => $query->orderByDesc('ratings_avg')->orderByDesc('ratings_count'),
            'alpha' => $query->orderBy('title', 'asc'),
            default => $query->latest('published_at')->latest('id'),
        };

        $catalog = $query->paginate(8)->withQueryString();

        // 4. Iconic Anime Characters
        $characters = Character::with('category')
            ->where('category_id', $category->id)
            ->active()
            ->take(8)
            ->get();

        // 5. Seasonal Releases & Simulcast Schedule
        $upcomingReleases = Release::where('category_id', $category->id)
            ->upcoming()
            ->take(4)
            ->get();

        // 6. Community Anime Works & Submissions
        $fanSubmissions = \App\Models\FanSubmission::where('category_id', $category->id)
            ->where('status', 'approved')
            ->with('user')
            ->latest()
            ->take(4)
            ->get();

        // 7. Anime Genres & Telemetry Distribution
        $genres = Genre::all();
        $genreCounts = [];
        foreach ($genres as $g) {
            $cnt = Content::where('category_id', $category->id)
                ->whereHas('genres', fn($q) => $q->where('genres.id', $g->id))
                ->count();
            if ($cnt > 0) {
                $genreCounts[$g->name] = $cnt;
            }
        }
        if (empty($genreCounts)) {
            $genreCounts = ['Shonen' => 4, 'Action' => 3, 'Supernatural' => 2, 'Fantasy' => 2, 'Sci-Fi' => 1];
        }

        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Anime Universe', 'url' => ''],
        ];

        return view('pages.anime', compact(
            'category',
            'spotlight',
            'trendingAnime',
            'catalog',
            'characters',
            'upcomingReleases',
            'fanSubmissions',
            'genres',
            'genreCounts',
            'breadcrumbs'
        ));
    }

    /**
     * Explore Hub
     */
    public function explore(Request $request): View
    {
        $query = Content::with(['category', 'genres'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->published();

        $matchedReleases = collect();
        $matchedCharacters = collect();
        $matchedMedia = collect();

        // Search by title, description, body text, categories, genres, and token keywords
        if ($request->filled('search')) {
            $rawSearch = trim($request->input('search'));
            $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $rawSearch);
            $stopWords = ['the', 'and', 'for', 'with', 'of', 'in', 'a', 'an', 'to', 'on', 'at', 'by'];
            $tokens = array_values(array_filter(
                explode(' ', $clean),
                fn($w) => mb_strlen(trim($w)) >= 2 && !in_array(strtolower(trim($w)), $stopWords)
            ));

            $query->where(function ($q) use ($rawSearch, $tokens) {
                // Exact full-string match
                $q->where('title', 'like', "%{$rawSearch}%")
                  ->orWhere('description', 'like', "%{$rawSearch}%")
                  ->orWhere('body', 'like', "%{$rawSearch}%")
                  ->orWhereHas('category', function ($catQ) use ($rawSearch) {
                      $catQ->where('name', 'like', "%{$rawSearch}%")
                           ->orWhere('slug', 'like', "%{$rawSearch}%");
                  })
                  ->orWhereHas('genres', function ($genQ) use ($rawSearch) {
                      $genQ->where('name', 'like', "%{$rawSearch}%");
                  });

                // Intelligent keyword tokens (e.g. "House", "Dragon")
                if (!empty($tokens)) {
                    $keyTokens = array_slice($tokens, 0, min(3, count($tokens)));
                    $q->orWhere(function ($sub) use ($keyTokens) {
                        foreach ($keyTokens as $token) {
                            $sub->where(function ($sub2) use ($token) {
                                $sub2->where('title', 'like', "%{$token}%")
                                     ->orWhere('description', 'like', "%{$token}%");
                            });
                        }
                    });
                }
            });

            // 1. Cross-Entity Search: Releases (e.g. Upcoming premiere drops)
            $relQuery = Release::with('category');
            $relQuery->where(function ($q) use ($rawSearch, $tokens) {
                $q->where('title', 'like', "%{$rawSearch}%")
                  ->orWhere('description', 'like', "%{$rawSearch}%")
                  ->orWhere('platform', 'like', "%{$rawSearch}%")
                  ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$rawSearch}%"));
                if (!empty($tokens)) {
                    $keyTokens = array_slice($tokens, 0, min(3, count($tokens)));
                    $q->orWhere(function ($sub) use ($keyTokens) {
                        foreach ($keyTokens as $t) {
                            $sub->where('title', 'like', "%{$t}%");
                        }
                    });
                }
            });
            $matchedReleases = $relQuery->take(6)->get();

            // 2. Cross-Entity Search: Characters
            $charQuery = Character::with('category')->active();
            $charQuery->where(function ($q) use ($rawSearch, $tokens) {
                $q->where('name', 'like', "%{$rawSearch}%")
                  ->orWhere('franchise', 'like', "%{$rawSearch}%")
                  ->orWhere('biography', 'like', "%{$rawSearch}%")
                  ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$rawSearch}%"));
                if (!empty($tokens)) {
                    $keyTokens = array_slice($tokens, 0, min(2, count($tokens)));
                    $q->orWhere(function ($sub) use ($keyTokens) {
                        foreach ($keyTokens as $t) {
                            $sub->where('name', 'like', "%{$t}%");
                        }
                    });
                }
            });
            $matchedCharacters = $charQuery->take(6)->get();

            // 3. Cross-Entity Search: Media & Video Trailers
            $mediaQuery = Media::with('category');
            $mediaQuery->where(function ($q) use ($rawSearch, $tokens) {
                $q->where('title', 'like', "%{$rawSearch}%")
                  ->orWhere('description', 'like', "%{$rawSearch}%")
                  ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$rawSearch}%"));
                if (!empty($tokens)) {
                    $keyTokens = array_slice($tokens, 0, min(2, count($tokens)));
                    $q->orWhere(function ($sub) use ($keyTokens) {
                        foreach ($keyTokens as $t) {
                            $sub->where('title', 'like', "%{$t}%");
                        }
                    });
                }
            });
            $matchedMedia = $mediaQuery->take(6)->get();
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // Genre filter
        if ($request->filled('genre')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.slug', $request->input('genre'))
                  ->orWhere('genres.id', $request->input('genre'));
            });
        }

        // Content format/type filter
        if ($request->filled('type')) {
            $type = $request->input('type');
            if (in_array($type, ['article', 'video', 'audio', 'image', 'trailer'])) {
                $query->where('type', $type);
            }
        }

        // Release year filter
        if ($request->filled('year')) {
            $year = (int) $request->input('year');
            if ($year >= 1970 && $year <= 2030) {
                $query->whereYear('release_date', $year);
            }
        }

        // Sorting options
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'popular' => $query->orderByDesc('popularity')->orderByDesc('views'),
            'alpha' => $query->orderBy('title', 'asc'),
            'rating' => $query->orderByDesc('ratings_avg')->orderByDesc('ratings_count'),
            default => $query->latest('published_at')->latest('id'),
        };

        $contents = $query->paginate(9)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();
        $genres = Genre::orderBy('name')->get();

        $totalResultsCount = $contents->total() + $matchedReleases->count() + $matchedCharacters->count() + $matchedMedia->count();

        // Compute active filters for chip displays
        $activeFilters = [];
        if ($request->filled('search')) {
            $activeFilters['search'] = ['label' => 'Search: "' . $request->input('search') . '"', 'param' => 'search'];
        }
        if ($request->filled('category')) {
            $matchedCat = $categories->firstWhere('slug', $request->input('category'));
            $activeFilters['category'] = ['label' => 'Fandom: ' . ($matchedCat?->name ?? $request->input('category')), 'param' => 'category'];
        }
        if ($request->filled('genre')) {
            $matchedGenre = $genres->firstWhere('slug', $request->input('genre')) ?? $genres->firstWhere('id', $request->input('genre'));
            $activeFilters['genre'] = ['label' => 'Genre: ' . ($matchedGenre?->name ?? $request->input('genre')), 'param' => 'genre'];
        }
        if ($request->filled('type')) {
            $activeFilters['type'] = ['label' => 'Format: ' . ucfirst($request->input('type')), 'param' => 'type'];
        }
        if ($request->filled('sort') && $request->input('sort') !== 'latest') {
            $sortLabels = ['popular' => 'Most Popular', 'alpha' => 'Alphabetical', 'rating' => 'Highest Rated'];
            $activeFilters['sort'] = ['label' => 'Sort: ' . ($sortLabels[$sort] ?? ucfirst($sort)), 'param' => 'sort'];
        }

        $breadcrumbs = [
            ['label' => 'Explore', 'url' => route('explore')],
        ];

        return view('pages.explore', compact(
            'contents',
            'categories',
            'genres',
            'activeFilters',
            'breadcrumbs',
            'matchedReleases',
            'matchedCharacters',
            'matchedMedia',
            'totalResultsCount'
        ));
    }

    /**
     * Explore Single Content Item
     */
    public function showContent(string $slug): View
    {
        $content = Content::with(['category', 'genres', 'author', 'media'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views counter safely
        $content->increment('views');

        // Record watch history for authenticated users
        if (auth()->check()) {
            WatchHistory::record(
                userId:        auth()->id(),
                watchableType: Content::class,
                watchableId:   $content->id,
                title:         $content->title,
                url:           route('explore.show', $content->slug),
                thumbnail:     $content->thumbnail_url,
                type:          $content->type ?? 'content'
            );
        }

        // Related contents: same category or overlapping genres, excluding current item
        $genreIds = $content->genres->pluck('id')->toArray();
        $relatedContents = Content::where('id', '!=', $content->id)
            ->where(function ($q) use ($content, $genreIds) {
                $q->where('category_id', $content->category_id);
                if (! empty($genreIds)) {
                    $q->orWhereHas('genres', function ($gq) use ($genreIds) {
                        $gq->whereIn('genres.id', $genreIds);
                    });
                }
            })
            ->published()
            ->with('category')
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->take(3)
            ->get();

        $user = auth()->user();
        $isBookmarked = $content->isBookmarkedBy($user);
        $userBookmark = $content->userBookmark($user);
        $userRating = $content->userRating($user);

        $breadcrumbs = [
            ['label' => 'Explore', 'url' => route('explore')],
            ['label' => $content->category?->name ?? 'Fandom', 'url' => route('fandoms.show', $content->category?->slug ?? 'all')],
            ['label' => $content->title, 'url' => ''],
        ];

        return view('pages.content-show', compact(
            'content',
            'relatedContents',
            'breadcrumbs',
            'isBookmarked',
            'userBookmark',
            'userRating'
        ));
    }

    /**
     * Fandoms Directory
     */
    public function fandoms(): View
    {
        $categories = Category::active()->withCount('contents')->get();
        $breadcrumbs = [
            ['label' => 'Fandoms', 'url' => route('fandoms.index')],
        ];

        return view('pages.fandoms', compact('categories', 'breadcrumbs'));
    }

    /**
     * Single Fandom Category Hub
     */
    public function showFandom(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $contents = Content::where('category_id', $category->id)->published()->latest()->take(6)->get();
        $characters = Character::where('category_id', $category->id)->active()->take(4)->get();
        $releases = Release::where('category_id', $category->id)->upcoming()->take(3)->get();
        $events = Event::where('category_id', $category->id)->upcoming()->take(3)->get();

        // Fandom telemetry metrics
        $fandomMetrics = [
            'contents_count' => Content::where('category_id', $category->id)->count(),
            'characters_count' => Character::where('category_id', $category->id)->count(),
            'releases_count' => Release::where('category_id', $category->id)->count(),
            'events_count' => Event::where('category_id', $category->id)->count(),
            'media_count' => \App\Models\Media::where('category_id', $category->id)->count(),
            'submissions_count' => \App\Models\FanSubmission::where('category_id', $category->id)->count(),
            'avg_rating' => (function() use ($category) {
                $catContentIds = Content::where('category_id', $category->id)->pluck('id');
                return Rating::where('ratable_type', Content::class)->whereIn('ratable_id', $catContentIds)->avg('score') ?? 4.85;
            })(),
            'total_views' => Content::where('category_id', $category->id)->sum('views') ?? 0,
        ];

        $breadcrumbs = [
            ['label' => 'Fandoms', 'url' => route('fandoms.index')],
            ['label' => $category->name, 'url' => ''],
        ];

        return view('pages.fandom-show', compact('category', 'contents', 'characters', 'releases', 'events', 'fandomMetrics', 'breadcrumbs'));
    }

    /**
     * Characters Directory
     */
    public function characters(Request $request): View
    {
        $query = Character::with('category')->active();

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('franchise', 'like', "%{$search}%");
        }

        $characters = $query->paginate(8)->withQueryString();
        $categories = Category::active()->get();

        $breadcrumbs = [
            ['label' => 'Characters', 'url' => route('characters.index')],
        ];

        return view('pages.characters', compact('characters', 'categories', 'breadcrumbs'));
    }

    /**
     * Single Character Profile
     */
    public function showCharacter(string $slug): View
    {
        $character = Character::with('category')->where('slug', $slug)->firstOrFail();
        $relatedCharacters = Character::where('category_id', $character->category_id)
            ->where('id', '!=', $character->id)
            ->active()
            ->take(3)
            ->get();

        // Deterministic character power / radar matrix
        $seed = crc32($character->slug);
        $combat = 70 + ($seed % 28);
        $intellect = 65 + (($seed >> 2) % 32);
        $loreImpact = 75 + (($seed >> 4) % 24);
        $fanAffinity = 80 + (($seed >> 6) % 20);
        $mysticism = 60 + (($seed >> 8) % 38);
        $agility = 68 + (($seed >> 10) % 30);

        $characterRadar = [
            'Combat Prowess' => min(99, $combat),
            'Intellect & Lore' => min(99, $intellect),
            'Universe Impact' => min(99, $loreImpact),
            'Fan Affinity' => min(99, $fanAffinity),
            'Mystic Power' => min(99, $mysticism),
            'Agility & Speed' => min(99, $agility),
        ];

        $breadcrumbs = [
            ['label' => 'Characters', 'url' => route('characters.index')],
            ['label' => $character->name, 'url' => ''],
        ];

        return view('pages.character-show', compact('character', 'relatedCharacters', 'characterRadar', 'breadcrumbs'));
    }

    /**
     * Releases Tracker & Catalog
     */
    public function releases(Request $request): View
    {
        $timeframe = $request->input('timeframe', 'upcoming');
        $query = Release::with('category');

        if ($timeframe === 'upcoming') {
            $query->upcoming();
        } elseif ($timeframe === 'recent') {
            $query->past();
        } elseif ($timeframe === 'featured') {
            $query->featured();
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('tag')) {
            $tag = $request->input('tag');
            $query->where(function ($q) use ($tag) {
                $q->whereJsonContains('tags', $tag)
                  ->orWhere('tags', 'like', '%"' . $tag . '"%')
                  ->orWhere('tags', 'like', '%' . $tag . '%');
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('platform', 'like', "%{$search}%")
                  ->orWhere('developer_publisher', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->input('sort', 'soonest');
        if ($sort === 'latest') {
            $query->latest('created_at');
        } elseif ($sort === 'title') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'date_desc') {
            $query->orderBy('release_date', 'desc');
        } else {
            $query->orderBy('release_date', 'asc');
        }

        $releases = $query->paginate(12)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();

        // Release formats telemetry
        $releaseStats = [
            'Game' => Release::where('type', 'game')->count(),
            'Anime' => Release::where('type', 'anime')->count(),
            'Theatrical Film' => Release::where('type', 'movie')->count(),
            'TV / Series' => Release::where('type', 'tv_series')->count(),
            'Comics / Manga' => Release::whereIn('type', ['comic', 'manga'])->count(),
            'Merchandise' => Release::where('type', 'merchandise')->count(),
        ];

        // Unique tags collection for filter badge list
        $allTags = ['Limited Edition', 'Pre-Order', 'Collectible', 'Collector\'s Edition', 'Special Edition', 'Steelbook', 'Box Set'];

        $breadcrumbs = [
            ['label' => 'Upcoming Releases', 'url' => route('releases.index')],
        ];

        return view('pages.releases', compact('releases', 'categories', 'breadcrumbs', 'allTags', 'timeframe', 'releaseStats'));
    }

    /**
     * Show single release detail with countdown and lore links.
     */
    public function showRelease(string $slug): View
    {
        $release = Release::where('slug', $slug)
            ->with(['category', 'bookmarks'])
            ->firstOrFail();

        $relatedReleases = Release::where('category_id', $release->category_id)
            ->where('id', '!=', $release->id)
            ->orderBy('release_date', 'asc')
            ->take(3)
            ->get();

        $relatedLore = Content::where('category_id', $release->category_id)
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        $breadcrumbs = [
            ['label' => 'Releases', 'url' => route('releases.index')],
            ['label' => $release->category?->name ?? 'Fandom', 'url' => route('fandoms.show', $release->category?->slug ?? 'all')],
            ['label' => $release->title, 'url' => ''],
        ];

        return view('pages.releases.show', compact('release', 'relatedReleases', 'relatedLore', 'breadcrumbs'));
    }

    /**
     * Events Portal Main View (Interactive Multiverse Map, Calendar & Nearby Events)
     */
    public function events(Request $request): View
    {
        $query = Event::with('category');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('venue_name', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        if ($request->filled('type')) {
            $query->where('event_type', $request->input('type'));
        }

        if ($request->filled('city')) {
            $query->where('city', $request->input('city'));
        }

        if ($request->filled('format')) {
            if ($request->input('format') === 'online') {
                $query->where('is_online', true);
            } elseif ($request->input('format') === 'in_person') {
                $query->where('is_online', false);
            }
        }

        // Location-aware Haversine filter if lat/lng are supplied
        if ($request->filled('lat') && $request->filled('lng')) {
            $lat = (float) $request->input('lat');
            $lng = (float) $request->input('lng');
            $radius = (float) $request->input('radius', 500);
            $query->nearby($lat, $lng, $radius);
        } else {
            $query->orderBy('starts_at', 'asc');
        }

        $events = $query->paginate(9)->withQueryString();

        // Spotlight featured event
        $featuredEvent = Event::with('category')
            ->where('is_featured', true)
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at', 'asc')
            ->first() ?? Event::with('category')->latest('starts_at')->first();

        // All map events formatted for Leaflet.js
        $mapEvents = Event::with('category')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('is_online', false)
            ->orderBy('starts_at', 'asc')
            ->get()
            ->map(function ($ev) {
                return [
                    'id' => $ev->id,
                    'title' => $ev->title,
                    'slug' => $ev->slug,
                    'cover_image' => $ev->cover_image_url,
                    'event_type' => $ev->event_type,
                    'type_label' => ucfirst(str_replace('_', ' ', $ev->event_type)),
                    'category_name' => $ev->category?->name ?? 'Fandom',
                    'category_slug' => $ev->category?->slug ?? 'all',
                    'venue_name' => $ev->venue_name,
                    'city' => $ev->city,
                    'country' => $ev->country,
                    'lat' => (float) $ev->latitude,
                    'lng' => (float) $ev->longitude,
                    'starts_at' => $ev->starts_at->format('M d, Y'),
                    'starts_time' => $ev->starts_at->format('H:i'),
                    'ticket_url' => $ev->ticket_url,
                    'url' => route('events.show', $ev->slug),
                ];
            });

        // Current month calendar events
        $currentMonth = \Carbon\Carbon::now()->startOfMonth();
        $monthEvents = Event::with('category')
            ->whereBetween('starts_at', [$currentMonth->copy()->startOfMonth(), $currentMonth->copy()->endOfMonth()])
            ->orderBy('starts_at', 'asc')
            ->get();

        $eventsByDay = [];
        foreach ($monthEvents as $ev) {
            $day = (int) $ev->starts_at->format('j');
            $eventsByDay[$day][] = $ev;
        }

        $categories = Category::active()->orderBy('name')->get();
        $cities = Event::whereNotNull('city')->where('city', '!=', 'Online')->distinct()->pluck('city')->sort()->values();

        $breadcrumbs = [
            ['label' => 'Events', 'url' => route('events.index')],
        ];

        return view('pages.events.index', compact(
            'events',
            'featuredEvent',
            'mapEvents',
            'monthEvents',
            'eventsByDay',
            'currentMonth',
            'categories',
            'cities',
            'breadcrumbs'
        ));
    }

    /**
     * Dedicated Monthly Events Calendar View
     */
    public function eventCalendar(Request $request): View
    {
        $monthParam = $request->input('month', now()->format('Y-m'));
        try {
            $currentMonth = \Carbon\Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
        } catch (\Throwable) {
            $currentMonth = now()->startOfMonth();
        }

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        $query = Event::with('category')
            ->whereBetween('starts_at', [$currentMonth->copy()->startOfMonth(), $currentMonth->copy()->endOfMonth()]);

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->input('category')));
        }

        if ($request->filled('city')) {
            $query->where('city', $request->input('city'));
        }

        $monthEvents = $query->orderBy('starts_at', 'asc')->get();

        $eventsByDay = [];
        foreach ($monthEvents as $ev) {
            $day = (int) $ev->starts_at->format('j');
            $eventsByDay[$day][] = $ev;
        }

        $categories = Category::active()->orderBy('name')->get();
        $cities = Event::whereNotNull('city')->where('city', '!=', 'Online')->distinct()->pluck('city')->sort()->values();

        $breadcrumbs = [
            ['label' => 'Events', 'url' => route('events.index')],
            ['label' => 'Monthly Calendar (' . $currentMonth->format('M Y') . ')', 'url' => ''],
        ];

        $events = $monthEvents;

        return view('pages.events.calendar', compact(
            'events',
            'currentMonth',
            'prevMonth',
            'nextMonth',
            'eventsByDay',
            'monthEvents',
            'categories',
            'cities',
            'breadcrumbs'
        ));
    }

    /**
     * JSON API Endpoint for Dynamic Leaflet Map & Nearby Radar Queries
     */
    public function eventsData(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Event::with('category');

        $userLat = $request->filled('lat') ? (float) $request->input('lat') : null;
        $userLng = $request->filled('lng') ? (float) $request->input('lng') : null;
        $radius = (float) $request->input('radius', 500);

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->input('category')));
        }

        if ($request->filled('type') && $request->input('type') !== 'all') {
            $query->where('event_type', $request->input('type'));
        }

        if ($userLat !== null && $userLng !== null) {
            $events = $query->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->nearby($userLat, $userLng, $radius)
                ->get();
        } else {
            $events = $query->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('starts_at', 'asc')
                ->get();
        }

        $data = $events->map(function ($ev) use ($userLat, $userLng) {
            $distance = null;
            if ($userLat !== null && $userLng !== null && $ev->latitude && $ev->longitude) {
                // Haversine formula in KM
                $earthRadius = 6371;
                $dLat = deg2rad($ev->latitude - $userLat);
                $dLon = deg2rad($ev->longitude - $userLng);
                $a = sin($dLat / 2) * sin($dLat / 2) +
                     cos(deg2rad($userLat)) * cos(deg2rad($ev->latitude)) *
                     sin($dLon / 2) * sin($dLon / 2);
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = round($earthRadius * $c, 1);
            }

            return [
                'id' => $ev->id,
                'title' => $ev->title,
                'slug' => $ev->slug,
                'cover_image' => $ev->cover_image_url,
                'event_type' => $ev->event_type,
                'type_label' => ucfirst(str_replace('_', ' ', $ev->event_type)),
                'category_name' => $ev->category?->name ?? 'General',
                'category_slug' => $ev->category?->slug ?? 'all',
                'venue_name' => $ev->venue_name,
                'city' => $ev->city,
                'country' => $ev->country,
                'lat' => (float) $ev->latitude,
                'lng' => (float) $ev->longitude,
                'distance_km' => $distance,
                'starts_at' => $ev->starts_at->format('M d, Y'),
                'starts_time' => $ev->starts_at->format('H:i'),
                'ticket_url' => $ev->ticket_url,
                'url' => route('events.show', $ev->slug),
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $data->count(),
            'events' => $data,
        ]);
    }

    /**
     * Show Single Event Dossier
     */
    public function showEvent(string $slug): View
    {
        $event = Event::with('category')->where('slug', $slug)->firstOrFail();

        $relatedEvents = Event::with('category')
            ->where('id', '!=', $event->id)
            ->where(function ($q) use ($event) {
                if ($event->category_id) {
                    $q->where('category_id', $event->category_id);
                }
                $q->orWhere('city', $event->city);
            })
            ->where('starts_at', '>=', now())
            ->take(3)
            ->get();

        $breadcrumbs = [
            ['label' => 'Events', 'url' => route('events.index')],
            ['label' => $event->title, 'url' => ''],
        ];

        return view('pages.events.show', compact('event', 'relatedEvents', 'breadcrumbs'));
    }

    /**
     * Download RFC 5545 iCalendar (.ics) File
     */
    public function eventIcs(Event $event): \Illuminate\Http\Response
    {
        return response($event->generateIcs(), 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="event-' . $event->slug . '.ics"',
        ]);
    }

    /**
     * Community Hub — Verified Fan Creations Showcase
     */
    public function community(Request $request): View
    {
        $query = \App\Models\FanSubmission::with(['user.profile', 'category'])->approved()->latest('published_at');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $submissions = $query->paginate(9)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();

        $communityStats = [
            'Fan Art' => \App\Models\FanSubmission::where('type', 'fan_art')->count(),
            'Cosplay' => \App\Models\FanSubmission::where('type', 'cosplay')->count(),
            'Fiction' => \App\Models\FanSubmission::where('type', 'fan_fiction')->count(),
            'Videos' => \App\Models\FanSubmission::where('type', 'fan_video')->count(),
        ];

        $breadcrumbs = [
            ['label' => 'Community', 'url' => route('community.index')],
        ];

        return view('pages.community', compact('submissions', 'categories', 'breadcrumbs', 'communityStats'));
    }

    /**
     * Show single approved fan creation.
     */
    public function showSubmission(\App\Models\FanSubmission $submission): View
    {
        $user = auth()->user();
        if (! $submission->isApproved()) {
            if (! $user || (! $user->isAdmin() && $user->id !== $submission->user_id)) {
                abort(404, 'Fan submission not found or pending review.');
            }
        }

        $submission->load(['user.profile', 'category']);

        $relatedSubmissions = \App\Models\FanSubmission::approved()
            ->where('category_id', $submission->category_id)
            ->where('id', '!=', $submission->id)
            ->take(3)
            ->get();

        $breadcrumbs = [
            ['label' => 'Community', 'url' => route('community.index')],
            ['label' => $submission->category?->name ?? 'Fandom', 'url' => route('fandoms.show', $submission->category?->slug ?? 'all')],
            ['label' => $submission->title, 'url' => ''],
        ];

        return view('pages.community.show', compact('submission', 'relatedSubmissions', 'breadcrumbs'));
    }

    /**
     * Multimedia Center
     */
    public function multimedia(Request $request): View
    {
        $query = \App\Models\Media::with(['category', 'content'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->published();

        if ($request->filled('type')) {
            $type = $request->input('type');
            if (array_key_exists($type, \App\Models\Media::MEDIA_TYPES)) {
                $query->where('media_type', $type);
            } else {
                $query->where('type', $type);
            }
        }

        if ($request->filled('media_type')) {
            $query->where('media_type', $request->input('media_type'));
        }

        if ($request->filled('quality')) {
            $query->where('quality', $request->input('quality'));
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('youtube_video_id', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($catQ) use ($search) {
                      $catQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('min_rating')) {
            $minRating = (float) $request->input('min_rating');
            $query->having('ratings_avg', '>=', $minRating);
        }

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'popular' => $query->orderByDesc('views')->orderByDesc('ratings_count'),
            'views' => $query->orderByDesc('views'),
            'rating' => $query->orderByDesc('ratings_avg')->orderByDesc('ratings_count'),
            'alpha' => $query->orderBy('title', 'asc'),
            default => $query->latest('id'),
        };

        $mediaItems = $query->paginate(12)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();

        // Base query for curated showcase rails
        $baseShowcase = \App\Models\Media::with(['category', 'content'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->published();

        $featuredMedia = (clone $baseShowcase)->featured()->take(4)->get();
        $trendingVideos = (clone $baseShowcase)->orderByDesc('views')->take(8)->get();
        $movieTrailers = (clone $baseShowcase)->whereIn('media_type', ['movie_trailer', 'movie_teaser'])->take(8)->get();
        $animeTrailers = (clone $baseShowcase)->whereIn('media_type', ['anime_trailer', 'anime_opening', 'anime_ending'])->take(8)->get();
        $gamingTrailers = (clone $baseShowcase)->whereIn('media_type', ['gaming_trailer', 'gaming_cinematic', 'gaming_gameplay'])->take(8)->get();
        $musicAndOst = (clone $baseShowcase)->whereIn('media_type', ['anime_song', 'movie_song', 'music_video'])->orWhere('type', 'audio')->take(8)->get();
        $topRatedMedia = (clone $baseShowcase)->having('ratings_avg', '>=', 4.0)->orderByDesc('ratings_avg')->take(8)->get();

        $breadcrumbs = [
            ['label' => 'Multimedia Center', 'url' => route('multimedia.index')],
        ];

        return view('pages.multimedia.index', compact(
            'mediaItems',
            'categories',
            'featuredMedia',
            'trendingVideos',
            'movieTrailers',
            'animeTrailers',
            'gamingTrailers',
            'musicAndOst',
            'topRatedMedia',
            'breadcrumbs'
        ));
    }

    /**
     * Show single media asset player/viewer.
     */
    public function showMedia(\App\Models\Media $media): View
    {
        $user = auth()->user();
        if ($media->status !== 'published' && (! $user || ! $user->isAdmin())) {
            abort(404, 'Media asset not found.');
        }

        $media->increment('views');
        $media->load(['category', 'content', 'uploader']);
        $media->loadAvg('ratings as ratings_avg', 'score');
        $media->loadCount('ratings');

        // Record watch history for authenticated users
        if (auth()->check()) {
            WatchHistory::record(
                userId:        auth()->id(),
                watchableType: \App\Models\Media::class,
                watchableId:   $media->id,
                title:         $media->title,
                url:           route('multimedia.show', $media->id),
                thumbnail:     $media->thumbnail_url,
                type:          $media->media_type ?? $media->type ?? 'media'
            );
        }

        $userRating = $media->userRating($user);

        $relatedMedia = \App\Models\Media::published()
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->where(function ($q) use ($media) {
                $q->where('category_id', $media->category_id)
                  ->orWhere('media_type', $media->media_type);
            })
            ->where('id', '!=', $media->id)
            ->take(6)
            ->get();

        $breadcrumbs = [
            ['label' => 'Multimedia Center', 'url' => route('multimedia.index')],
            ['label' => $media->title, 'url' => ''],
        ];

        return view('pages.multimedia.show', compact('media', 'userRating', 'relatedMedia', 'breadcrumbs'));
    }

    /**
     * About Platform
     */
    public function about(): View
    {
        $breadcrumbs = [
            ['label' => 'About Platform', 'url' => route('about')],
        ];

        return view('pages.about', compact('breadcrumbs'));
    }
}
