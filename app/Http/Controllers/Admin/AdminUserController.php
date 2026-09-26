<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a listing of registered users.
     */
    public function index(Request $request): View
    {
        $query = User::with(['profile', 'favoriteCategories'])
            ->withCount(['bookmarks', 'ratings', 'fanSubmissions']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
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

        $metrics = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'regular' => User::where('role', 'user')->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'metrics'));
    }

    /**
     * Display the specified user profile & platform history.
     */
    public function show(User $user): View
    {
        $user->load(['profile', 'favoriteCategories'])
             ->loadCount(['bookmarks', 'ratings', 'fanSubmissions', 'conversations']);

        $recentActivities = UserActivity::where('user_id', $user->id)
            ->latest('created_at')
            ->take(15)
            ->get();

        return view('admin.users.show', compact('user', 'recentActivities'));
    }

    /**
     * Update user clearance role.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['user', 'admin'])],
        ]);

        $newRole = $validated['role'];

        // Self-Protection Rule 1: Cannot modify own role
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot modify your own administrative role privileges.');
        }

        // Self-Protection Rule 2: Cannot demote the last remaining administrator
        if ($newRole === 'user' && $user->role === 'admin') {
            $activeAdminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($activeAdminCount <= 1) {
                return back()->with('error', 'Cannot demote the final active administrator on the platform.');
            }
        }

        $oldRole = $user->role;
        $user->update(['role' => $newRole]);

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator changed user #{$user->id} ({$user->email}) role from '{$oldRole}' to '{$newRole}'"
        );

        return back()->with('success', "User '{$user->name}' role has been updated to {$newRole}.");
    }

    /**
     * Toggle user account active status.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Self-Protection: Admin cannot deactivate themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own administrative account.');
        }

        // Cannot deactivate the final remaining admin
        if ($user->isAdmin() && $user->is_active) {
            $activeAdminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($activeAdminCount <= 1) {
                return back()->with('error', 'Cannot deactivate the only active administrator on the platform.');
            }
        }

        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        $statusStr = $newStatus ? 'activated' : 'deactivated';

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator {$statusStr} user #{$user->id} ({$user->email})"
        );

        return back()->with('success', "User account for '{$user->name}' has been {$statusStr}.");
    }

    /**
     * Delete user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own active administrator account.');
        }

        if ($user->isAdmin()) {
            $activeAdminCount = User::where('role', 'admin')->count();
            if ($activeAdminCount <= 1) {
                return back()->with('error', 'Cannot delete the final administrator account.');
            }
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $user->delete();

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator deleted user {$userEmail}"
        );

        return redirect()->route('admin.users.index')->with('success', "User '{$userName}' has been deleted.");
    }
}
