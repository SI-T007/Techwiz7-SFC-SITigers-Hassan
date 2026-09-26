<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChatbotMessage;
use App\Models\FanSubmission;
use App\Models\Rating;
use App\Models\User;
use App\Models\UserActivity;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Show Admin Dashboard with Graphical Telemetry.
     */
    public function index(): View
    {
        $metrics = [
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'regular_users' => User::where('role', 'user')->count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'total_categories' => Category::count(),
            'total_contents' => \App\Models\Content::count(),
            'total_characters' => \App\Models\Character::count(),
            'total_articles' => \App\Models\Content::where('type', 'article')->count(),
            'total_events' => \App\Models\Event::count(),
            'total_releases' => \App\Models\Release::count(),
            'total_kb' => \App\Models\ChatbotKnowledgeBase::count(),
            'total_conversations' => \App\Models\ChatbotConversation::count(),
            'total_messages' => ChatbotMessage::count(),
            'pending_submissions' => FanSubmission::where('status', 'pending')->count(),
            'approved_submissions' => FanSubmission::where('status', 'approved')->count(),
            'rejected_submissions' => FanSubmission::where('status', 'rejected')->count(),
            'total_media' => \App\Models\Media::count(),
            'new_feedback' => \App\Models\Feedback::where('status', 'new')->count(),
            'avg_rating' => Rating::avg('score') ? round(Rating::avg('score'), 2) : 5.0,
            'total_ratings' => Rating::count(),
        ];

        // 7-day sparkline platform velocity data
        $weeklyLabels = [];
        $weeklyActivities = [];
        $weeklySignups = [];
        $weeklyBotMessages = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $startDay = (clone $day)->startOfDay();
            $endDay = (clone $day)->endOfDay();

            $weeklyLabels[] = $day->format('M d');
            $weeklyActivities[] = UserActivity::whereBetween('created_at', [$startDay, $endDay])->count();
            $weeklySignups[] = User::whereBetween('created_at', [$startDay, $endDay])->count();
            $weeklyBotMessages[] = ChatbotMessage::whereBetween('created_at', [$startDay, $endDay])->count();
        }

        $sparklineData = [
            'labels' => $weeklyLabels,
            'activities' => $weeklyActivities,
            'signups' => $weeklySignups,
            'bot_messages' => $weeklyBotMessages,
        ];

        // Fandom categories with content counts
        $topCategories = Category::withCount(['contents', 'characters', 'events', 'releases'])
            ->orderByDesc('contents_count')
            ->take(6)
            ->get();

        // Recent users
        $recentUsers = User::with('profile')
            ->latest()
            ->take(6)
            ->get();

        // Recent audit/activity entries across the system
        $recentActivities = UserActivity::with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'metrics',
            'sparklineData',
            'topCategories',
            'recentUsers',
            'recentActivities'
        ));
    }

    /**
     * List all users with search/filter.
     */
    public function users(Request $request): View
    {
        $query = User::with(['profile', 'favoriteCategories']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $isActive = $request->input('status') === 'active';
            $query->where('is_active', $isActive);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users', compact('users'));
    }

    /**
     * Toggle user active/deactivated status.
     */
    public function toggleUserStatus(Request $request, User $user): RedirectResponse
    {
        // Prevent admin from deactivating themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own administrative account.');
        }

        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        $statusStr = $newStatus ? 'activated' : 'deactivated';

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator {$statusStr} account for user '{$user->name}'",
            'App\Models\User',
            $user->id
        );

        return back()->with('success', "User '{$user->name}' account successfully {$statusStr}.");
    }

    /**
     * Change user role.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own administrative privilege role.');
        }

        $oldRole = $user->role;
        $newRole = $request->input('role');
        $user->update(['role' => $newRole]);

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Changed role for '{$user->name}' from {$oldRole} to {$newRole}",
            'App\Models\User',
            $user->id
        );

        return back()->with('success', "Privilege role for '{$user->name}' updated to " . strtoupper($newRole) . ".");
    }

    /**
     * Delete a user account (Superadmin protection).
     */
    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own active administrator account.');
        }

        $userName = $user->name;
        $user->delete();

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Deleted user account '{$userName}' and associated profile",
            'App\Models\User'
        );

        return back()->with('success', "User account '{$userName}' has been deleted.");
    }
}
