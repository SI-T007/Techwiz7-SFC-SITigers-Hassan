<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WatchHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WatchHistoryController extends Controller
{
    /**
     * Display the user's full paginated watch history.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Filters
        $type   = $request->query('type');
        $search = $request->query('q');
        $period = $request->query('period', 'all'); // today | week | month | all

        $query = WatchHistory::where('user_id', $user->id)
            ->orderByDesc('watched_at');

        // Type filter
        if ($type) {
            $query->where('type', 'LIKE', "%{$type}%");
        }

        // Search by title
        if ($search) {
            $query->where('title', 'LIKE', "%{$search}%");
        }

        // Period filter
        $query->when($period === 'today',  fn ($q) => $q->whereDate('watched_at', today()))
              ->when($period === 'week',   fn ($q) => $q->where('watched_at', '>=', now()->subDays(7)))
              ->when($period === 'month',  fn ($q) => $q->where('watched_at', '>=', now()->subDays(30)));

        $history = $query->paginate(24)->withQueryString();

        // Stats
        $totalWatched    = WatchHistory::where('user_id', $user->id)->count();
        $watchedToday    = WatchHistory::where('user_id', $user->id)->whereDate('watched_at', today())->count();
        $watchedThisWeek = WatchHistory::where('user_id', $user->id)->where('watched_at', '>=', now()->subDays(7))->count();

        // Distinct types for filter dropdown
        $availableTypes = WatchHistory::where('user_id', $user->id)
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->filter()
            ->values();

        return view('user.watch-history', compact(
            'history',
            'totalWatched',
            'watchedToday',
            'watchedThisWeek',
            'availableTypes',
            'type',
            'search',
            'period'
        ));
    }

    /**
     * Remove a single history entry.
     */
    public function destroy(Request $request, WatchHistory $watchHistory): RedirectResponse
    {
        // Ensure user owns this record
        abort_if($watchHistory->user_id !== $request->user()->id, 403);

        $watchHistory->delete();

        return back()->with('success', 'History entry removed.');
    }

    /**
     * Clear ALL history for the authenticated user.
     */
    public function clearAll(Request $request): RedirectResponse
    {
        WatchHistory::where('user_id', $request->user()->id)->delete();

        return redirect()->route('watch-history.index')
            ->with('success', 'Your watch history has been cleared.');
    }
}
