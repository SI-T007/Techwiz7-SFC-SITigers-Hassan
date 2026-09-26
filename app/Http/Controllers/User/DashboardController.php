<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Content;
use App\Models\Feedback;
use App\Models\Rating;
use App\Models\UserActivity;
use App\Models\WatchHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the authenticated user's dashboard with rich graphical fan analytics.
     */
    public function index(Request $request): View
    {
        $user = $request->user()->load(['profile', 'favoriteCategories']);

        // Stats summary
        $stats = [
            'fandoms_count'       => $user->favoriteCategories()->count(),
            'bookmarks_count'     => $user->bookmarks()->count(),
            'ratings_count'       => $user->ratings()->count(),
            'submissions_count'   => $user->fanSubmissions()->count(),
            'watch_history_count' => WatchHistory::where('user_id', $user->id)->count(),
            'feedback_count'      => Feedback::where('user_id', $user->id)->count(),
        ];

        // Favorite fandoms
        $favoriteCategories = $user->favoriteCategories;

        // Suggested categories
        $favoriteIds = $favoriteCategories->pluck('id')->toArray();
        $suggestedCategories = Category::active()
            ->whereNotIn('id', $favoriteIds)
            ->take(4)
            ->get();

        // 1. Personal 14-Day Activity Sparkline Timeline
        $timelineLabels = [];
        $timelineActivities = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $startDay = (clone $day)->startOfDay();
            $endDay = (clone $day)->endOfDay();

            $timelineLabels[] = $day->format('M d');
            $timelineActivities[] = UserActivity::where('user_id', $user->id)
                ->whereBetween('created_at', [$startDay, $endDay])
                ->count();
        }

        $userTimeline = [
            'labels' => $timelineLabels,
            'activities' => $timelineActivities,
        ];

        // 2. Fandom Universe Affinity (Radar Data based on user bookmarks and favorite categories)
        $allActiveCategories = Category::active()->get();
        $radarLabels = [];
        $radarScores = [];

        foreach ($allActiveCategories as $cat) {
            $radarLabels[] = $cat->name;
            $isFav = $favoriteCategories->contains('id', $cat->id) ? 20 : 0;
            $catContentIds = Content::where('category_id', $cat->id)->pluck('id');
            $bookmarksInCat = Bookmark::where('user_id', $user->id)
                ->whereIn('bookmarkable_id', $catContentIds)
                ->count() * 10;
            
            $score = max(5, $isFav + $bookmarksInCat);
            $radarScores[] = $score;
        }

        $userRadar = [
            'labels' => $radarLabels,
            'scores' => $radarScores,
        ];

        // 3. User Personal Star Rating Breakdown
        $userRatings = Rating::where('user_id', $user->id)
            ->select('score', DB::raw('count(*) as count'))
            ->groupBy('score')
            ->pluck('count', 'score')
            ->toArray();

        $userRatingDistribution = [
            '5' => $userRatings[5] ?? 0,
            '4' => $userRatings[4] ?? 0,
            '3' => $userRatings[3] ?? 0,
            '2' => $userRatings[2] ?? 0,
            '1' => $userRatings[1] ?? 0,
        ];

        // 4. Calculate Fan XP & Level Rank
        $totalActions = UserActivity::where('user_id', $user->id)->count();
        $fanXp = ($stats['bookmarks_count'] * 15) + ($stats['ratings_count'] * 20) + ($stats['submissions_count'] * 50) + ($totalActions * 5) + 50;
        $fanLevel = max(1, floor($fanXp / 100));
        $nextLevelXp = ($fanLevel) * 100;
        $currentLevelProgress = min(100, round((($fanXp % 100) / 100) * 100));

        $fanRanks = [
            1 => 'Novice Lore Explorer',
            2 => 'Fandom Enthusiast',
            3 => 'Universe Chronicler',
            4 => 'Cosmic Vanguard',
            5 => 'Grand Lore Archon',
        ];
        $fanRankTitle = $fanRanks[min(5, $fanLevel)] ?? 'Grand Lore Archon';

        // Recent user activities
        $recentActivities = $user->activities()->take(6)->get();

        // User Bookmarks with polymorphics
        $userBookmarks = $user->bookmarks()->with('bookmarkable')->latest()->take(12)->get();

        // User Ratings with polymorphics
        $userRatingsList = $user->ratings()->with('ratable')->latest('updated_at')->take(12)->get();

        // Average score given by user
        $userAvgRating = $user->ratings()->avg('score');
        $userAvgRating = $userAvgRating ? round((float) $userAvgRating, 1) : null;

        // User Feedback & Error Suggestions
        $userFeedback = Feedback::where('user_id', $user->id)
            ->with('repliedBy')
            ->latest()
            ->take(15)
            ->get();

        return view('user.dashboard', compact(
            'user',
            'stats',
            'favoriteCategories',
            'suggestedCategories',
            'recentActivities',
            'userTimeline',
            'userRadar',
            'userRatingDistribution',
            'fanXp',
            'fanLevel',
            'nextLevelXp',
            'currentLevelProgress',
            'fanRankTitle',
            'userBookmarks',
            'userRatingsList',
            'userAvgRating',
            'userFeedback'
        ));
    }
}
