<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Character;
use App\Models\ChatbotConversation;
use App\Models\ChatbotKnowledgeBase;
use App\Models\ChatbotMessage;
use App\Models\Content;
use App\Models\Event;
use App\Models\FanSubmission;
use App\Models\Feedback;
use App\Models\Media;
use App\Models\Rating;
use App\Models\Release;
use App\Models\User;
use App\Models\UserActivity;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminAnalyticsController extends Controller
{
    /**
     * Display the Platform Analytics & Usage Telemetry Dashboard.
     */
    public function index(Request $request): View
    {
        $range = $request->input('range', '30_days');
        $categoryId = $request->input('category_id');

        $analytics = $this->compileAnalyticsData($range, $categoryId);

        return view('admin.analytics.index', array_merge($analytics, [
            'range' => $range,
            'selectedCategoryId' => $categoryId,
        ]));
    }

    /**
     * Return JSON analytics telemetry data for dynamic AJAX updates.
     */
    public function apiData(Request $request): JsonResponse
    {
        $range = $request->input('range', '30_days');
        $categoryId = $request->input('category_id');

        $analytics = $this->compileAnalyticsData($range, $categoryId);

        return response()->json([
            'status' => 'success',
            'range' => $range,
            'data' => $analytics,
        ]);
    }

    /**
     * Compile complete analytics telemetry and trend datasets.
     */
    protected function compileAnalyticsData(string $range, ?string $categoryId = null): array
    {
        // Determine date thresholds
        $now = Carbon::now();
        $startDate = match ($range) {
            'today' => Carbon::today(),
            '24_hours' => Carbon::now()->subHours(24),
            '7_days' => Carbon::now()->subDays(7)->startOfDay(),
            '30_days' => Carbon::now()->subDays(30)->startOfDay(),
            '90_days' => Carbon::now()->subDays(90)->startOfDay(),
            'year' => Carbon::now()->startOfYear(),
            'all_time' => Carbon::createFromTimestamp(0),
            default => Carbon::now()->subDays(30)->startOfDay(),
        };

        // Calculate previous comparison window for delta indicators
        $periodDurationInSeconds = max(1, $now->diffInSeconds($startDate));
        $prevStartDate = (clone $startDate)->subSeconds($periodDurationInSeconds);
        $prevEndDate = clone $startDate;

        // 1. User Metrics
        $totalUsers = User::count();
        $newUsersInRange = User::where('created_at', '>=', $startDate)->count();
        $prevNewUsers = User::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $userGrowthPct = $prevNewUsers > 0 ? round((($newUsersInRange - $prevNewUsers) / $prevNewUsers) * 100, 1) : 100;

        // Active users defined as distinct users with recorded activities or registrations in range
        $activeUserIds = UserActivity::where('created_at', '>=', $startDate)
            ->distinct()
            ->pluck('user_id')
            ->filter();

        $activeUsersCount = $activeUserIds->count();
        if ($activeUsersCount === 0) {
            $activeUsersCount = User::where('updated_at', '>=', $startDate)->where('is_active', true)->count();
        }

        $inactiveUsersCount = User::where('is_active', false)->count();
        $adminCount = User::where('role', 'admin')->count();
        $regularUserCount = max(0, $totalUsers - $adminCount - $inactiveUsersCount);

        // 2. Time-series Trends Generation (Activities, Signups, Bot Messages, Bookmarks, Ratings)
        $timeline = $this->buildTimelineData($range, $startDate);

        // 3. Fandom Universes & Categories
        $categoriesQuery = Category::withCount(['contents', 'characters', 'events', 'releases']);
        if ($categoryId && is_numeric($categoryId)) {
            $categoriesQuery->where('id', $categoryId);
        }
        $categories = $categoriesQuery->get()
            ->map(function ($cat) {
                $contentViews = Content::where('category_id', $cat->id)->sum('views') ?? 0;
                $submissionCount = FanSubmission::where('category_id', $cat->id)->count();
                $mediaCount = Media::where('category_id', $cat->id)->count();
                $contentIds = Content::where('category_id', $cat->id)->pluck('id');
                $bookmarksCount = Bookmark::whereIn('bookmarkable_id', $contentIds)->count();

                $cat->submissions_count = $submissionCount;
                $cat->media_count = $mediaCount;
                $cat->bookmarks_count = $bookmarksCount;
                $cat->engagement_score = $cat->contents_count * 2 + $cat->characters_count * 3 + $submissionCount * 5 + $mediaCount * 2 + floor($contentViews / 10);
                return $cat;
            })
            ->sortByDesc('engagement_score')
            ->values();

        $allCategoriesList = Category::select('id', 'name', 'slug')->orderBy('name')->get();

        $categoryChartData = [
            'labels' => $categories->pluck('name')->toArray(),
            'engagement' => $categories->pluck('engagement_score')->toArray(),
            'contents' => $categories->pluck('contents_count')->toArray(),
            'characters' => $categories->pluck('characters_count')->toArray(),
            'events' => $categories->pluck('events_count')->toArray(),
            'releases' => $categories->pluck('releases_count')->toArray(),
            'submissions' => $categories->pluck('submissions_count')->toArray(),
            'media' => $categories->pluck('media_count')->toArray(),
        ];

        // 4. Content Format Distribution
        $contentFormats = [
            'article' => Content::where('type', 'article')->count(),
            'video' => Content::where('type', 'video')->count(),
            'audio' => Content::where('type', 'audio')->count(),
            'image' => Content::where('type', 'image')->count(),
            'trailer' => Content::where('type', 'trailer')->count(),
        ];

        // 5. Media HD Quality Distribution
        $mediaQualities = [
            '4K UHD' => Media::where('quality', '4K')->count(),
            '1080p FHD' => Media::where('quality', 'Full HD')->count(),
            '720p HD' => Media::where('quality', 'HD')->count(),
            'SD Quality' => Media::where('quality', 'SD')->count(),
            'Standard' => Media::whereIn('quality', ['Unknown', 'standard'])->count(),
        ];

        // 6. Chatbot Telemetry & Interaction Volume
        $totalConversations = ChatbotConversation::count();
        $conversationsInRange = ChatbotConversation::where('created_at', '>=', $startDate)->count();
        $totalMessages = ChatbotMessage::count();
        $messagesInRange = ChatbotMessage::where('created_at', '>=', $startDate)->count();

        $prevMessages = ChatbotMessage::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $botGrowthPct = $prevMessages > 0 ? round((($messagesInRange - $prevMessages) / $prevMessages) * 100, 1) : 100;

        // Response Source Distribution
        $sourcesQuery = ChatbotMessage::where('sender', 'assistant');
        if ($range !== 'all_time') {
            $sourcesQuery->where('created_at', '>=', $startDate);
        }
        $sourcesCount = $sourcesQuery->select('source', DB::raw('count(*) as total'))
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray();

        $sourceBreakdown = [
            'knowledge_base' => $sourcesCount['knowledge_base'] ?? 0,
            'database' => $sourcesCount['database'] ?? 0,
            'ai' => $sourcesCount['ai'] ?? 0,
            'fallback' => $sourcesCount['fallback'] ?? 0,
        ];

        // 7. Community & Moderation Statistics
        $submissionsCount = [
            'total' => FanSubmission::count(),
            'pending' => FanSubmission::where('status', 'pending')->count(),
            'approved' => FanSubmission::where('status', 'approved')->count(),
            'rejected' => FanSubmission::where('status', 'rejected')->count(),
        ];

        $feedbackCount = [
            'total' => Feedback::count(),
            'new' => Feedback::where('status', 'new')->count(),
            'replied' => Feedback::where('status', 'replied')->count(),
            'bugs' => Feedback::where('type', 'bug')->count(),
            'suggestions' => Feedback::where('type', 'suggestion')->count(),
            'queries' => Feedback::where('type', 'query')->count(),
        ];

        // 8. 5-Star Rating Distribution & Community Sentiment
        $ratingsByScore = Rating::select('score', DB::raw('count(*) as count'))
            ->groupBy('score')
            ->pluck('count', 'score')
            ->toArray();

        $starDistribution = [
            '5_star' => $ratingsByScore[5] ?? 0,
            '4_star' => $ratingsByScore[4] ?? 0,
            '3_star' => $ratingsByScore[3] ?? 0,
            '2_star' => $ratingsByScore[2] ?? 0,
            '1_star' => $ratingsByScore[1] ?? 0,
        ];

        $totalRatings = Rating::count();
        $avgRating = Rating::avg('score') ? round(Rating::avg('score'), 2) : 5.00;
        $totalBookmarks = Bookmark::count();

        // 9. Events & Releases Radar Metrics
        $eventsUpcoming = Event::where('status', 'upcoming')->count();
        $eventsCompleted = Event::where('status', 'completed')->count();
        $releasesUpcoming = Release::where('status', 'upcoming')->count();
        $releasesOutNow = Release::where('status', 'released')->count();

        // 10. User Activity Type Distribution
        $activityTypesCount = UserActivity::where('created_at', '>=', $startDate)
            ->select('activity_type', DB::raw('count(*) as count'))
            ->groupBy('activity_type')
            ->pluck('count', 'activity_type')
            ->toArray();

        // 11. Hourly Peak Activity Distribution (24-Hour Heatmap / Time of Day)
        $hourlyActivities = [];
        for ($h = 0; $h < 24; $h++) {
            $hourlyActivities[$h] = 0;
        }

        $hourlyQuery = UserActivity::select(DB::raw('HOUR(created_at) as hour_num'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->get();

        foreach ($hourlyQuery as $row) {
            $h = (int) $row->hour_num;
            if (isset($hourlyActivities[$h])) {
                $hourlyActivities[$h] = (int) $row->total;
            }
        }

        // Summary KPI deltas
        $totalActivitiesInRange = UserActivity::where('created_at', '>=', $startDate)->count();
        $prevActivities = UserActivity::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $activityGrowthPct = $prevActivities > 0 ? round((($totalActivitiesInRange - $prevActivities) / $prevActivities) * 100, 1) : 100;

        return [
            'startDate' => $startDate,
            'totalUsers' => $totalUsers,
            'newUsersInRange' => $newUsersInRange,
            'userGrowthPct' => $userGrowthPct,
            'activeUsersCount' => $activeUsersCount,
            'inactiveUsersCount' => $inactiveUsersCount,
            'adminCount' => $adminCount,
            'regularUserCount' => $regularUserCount,
            'categories' => $categories,
            'allCategoriesList' => $allCategoriesList,
            'categoryChartData' => $categoryChartData,
            'contentFormats' => $contentFormats,
            'mediaQualities' => $mediaQualities,
            'totalConversations' => $totalConversations,
            'conversationsInRange' => $conversationsInRange,
            'totalMessages' => $totalMessages,
            'messagesInRange' => $messagesInRange,
            'botGrowthPct' => $botGrowthPct,
            'sourceBreakdown' => $sourceBreakdown,
            'timeline' => $timeline,
            'submissionsCount' => $submissionsCount,
            'feedbackCount' => $feedbackCount,
            'starDistribution' => $starDistribution,
            'totalRatings' => $totalRatings,
            'avgRating' => $avgRating,
            'totalBookmarks' => $totalBookmarks,
            'eventsUpcoming' => $eventsUpcoming,
            'eventsCompleted' => $eventsCompleted,
            'releasesUpcoming' => $releasesUpcoming,
            'releasesOutNow' => $releasesOutNow,
            'activityTypesCount' => $activityTypesCount,
            'hourlyActivities' => array_values($hourlyActivities),
            'totalActivitiesInRange' => $totalActivitiesInRange,
            'activityGrowthPct' => $activityGrowthPct,
        ];
    }

    /**
     * Build discrete timeline datapoints based on selected range.
     */
    protected function buildTimelineData(string $range, Carbon $startDate): array
    {
        $labels = [];
        $registrations = [];
        $activities = [];
        $messages = [];
        $bookmarks = [];
        $ratings = [];

        if ($range === 'today' || $range === '24_hours') {
            for ($hour = 0; $hour <= 23; $hour++) {
                $startHour = Carbon::today()->addHours($hour);
                $endHour = (clone $startHour)->endOfHour();

                $labels[] = $startHour->format('H:00');
                $registrations[] = User::whereBetween('created_at', [$startHour, $endHour])->count();
                $activities[] = UserActivity::whereBetween('created_at', [$startHour, $endHour])->count();
                $messages[] = ChatbotMessage::whereBetween('created_at', [$startHour, $endHour])->count();
                $bookmarks[] = Bookmark::whereBetween('created_at', [$startHour, $endHour])->count();
                $ratings[] = Rating::whereBetween('created_at', [$startHour, $endHour])->count();
            }
        } elseif ($range === '7_days') {
            for ($i = 6; $i >= 0; $i--) {
                $day = Carbon::now()->subDays($i);
                $startDay = (clone $day)->startOfDay();
                $endDay = (clone $day)->endOfDay();

                $labels[] = $day->format('M d (D)');
                $registrations[] = User::whereBetween('created_at', [$startDay, $endDay])->count();
                $activities[] = UserActivity::whereBetween('created_at', [$startDay, $endDay])->count();
                $messages[] = ChatbotMessage::whereBetween('created_at', [$startDay, $endDay])->count();
                $bookmarks[] = Bookmark::whereBetween('created_at', [$startDay, $endDay])->count();
                $ratings[] = Rating::whereBetween('created_at', [$startDay, $endDay])->count();
            }
        } elseif ($range === '30_days') {
            for ($i = 29; $i >= 0; $i--) {
                $day = Carbon::now()->subDays($i);
                $startDay = (clone $day)->startOfDay();
                $endDay = (clone $day)->endOfDay();

                $labels[] = $day->format('M d');
                $registrations[] = User::whereBetween('created_at', [$startDay, $endDay])->count();
                $activities[] = UserActivity::whereBetween('created_at', [$startDay, $endDay])->count();
                $messages[] = ChatbotMessage::whereBetween('created_at', [$startDay, $endDay])->count();
                $bookmarks[] = Bookmark::whereBetween('created_at', [$startDay, $endDay])->count();
                $ratings[] = Rating::whereBetween('created_at', [$startDay, $endDay])->count();
            }
        } elseif ($range === '90_days') {
            for ($i = 12; $i >= 0; $i--) {
                $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                $weekEnd = (clone $weekStart)->endOfWeek();

                $labels[] = 'Wk of ' . $weekStart->format('M d');
                $registrations[] = User::whereBetween('created_at', [$weekStart, $weekEnd])->count();
                $activities[] = UserActivity::whereBetween('created_at', [$weekStart, $weekEnd])->count();
                $messages[] = ChatbotMessage::whereBetween('created_at', [$weekStart, $weekEnd])->count();
                $bookmarks[] = Bookmark::whereBetween('created_at', [$weekStart, $weekEnd])->count();
                $ratings[] = Rating::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            }
        } elseif ($range === 'year') {
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $startMonth = (clone $month)->startOfMonth();
                $endMonth = (clone $month)->endOfMonth();

                $labels[] = $month->format('M Y');
                $registrations[] = User::whereBetween('created_at', [$startMonth, $endMonth])->count();
                $activities[] = UserActivity::whereBetween('created_at', [$startMonth, $endMonth])->count();
                $messages[] = ChatbotMessage::whereBetween('created_at', [$startMonth, $endMonth])->count();
                $bookmarks[] = Bookmark::whereBetween('created_at', [$startMonth, $endMonth])->count();
                $ratings[] = Rating::whereBetween('created_at', [$startMonth, $endMonth])->count();
            }
        } else {
            // all_time - last 6 quarters / months
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $startMonth = (clone $month)->startOfMonth();
                $endMonth = (clone $month)->endOfMonth();

                $labels[] = $month->format('M Y');
                $registrations[] = User::whereBetween('created_at', [$startMonth, $endMonth])->count();
                $activities[] = UserActivity::whereBetween('created_at', [$startMonth, $endMonth])->count();
                $messages[] = ChatbotMessage::whereBetween('created_at', [$startMonth, $endMonth])->count();
                $bookmarks[] = Bookmark::whereBetween('created_at', [$startMonth, $endMonth])->count();
                $ratings[] = Rating::whereBetween('created_at', [$startMonth, $endMonth])->count();
            }
        }

        return [
            'labels' => $labels,
            'registrations' => $registrations,
            'activities' => $activities,
            'messages' => $messages,
            'bookmarks' => $bookmarks,
            'ratings' => $ratings,
        ];
    }
}
