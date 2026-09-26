<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\UserActivity;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the user's public/portal profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user()->load(['profile', 'favoriteCategories', 'activities']);
        $activities = $user->activities()->take(10)->get();

        return view('user.profile.show', compact('user', 'activities'));
    }

    /**
     * Show the profile edit form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load(['profile', 'favoriteCategories']);
        
        // Ensure user has a profile record
        if (!$user->profile) {
            $user->profile()->create([
                'display_name' => $user->name,
                'display_preferences' => UserProfile::defaultDisplayPreferences(),
                'accessibility_preferences' => UserProfile::defaultAccessibilityPreferences(),
            ]);
            $user->load('profile');
        }

        $allCategories = Category::active()->get();
        $selectedCategoryIds = $user->favoriteCategories->pluck('id')->toArray();

        return view('user.profile.edit', compact('user', 'allCategories', 'selectedCategoryIds'));
    }

    /**
     * Update user's personal details.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'display_name' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:500'],
        ]);

        $user->update([
            'name' => $validated['name'],
        ]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'display_name' => $validated['display_name'] ?? $user->name,
                'bio' => $validated['bio'] ?? null,
                'location' => $validated['location'] ?? null,
                'website' => $validated['website'] ?? null,
            ]
        );

        UserActivity::record($user->id, 'profile.update', 'Updated personal profile details');

        return back()->with('success', 'Profile information updated successfully!');
    }

    /**
     * Upload / update avatar image.
     */
    public function updateAvatar(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
        ]);

        $user = $request->user();
        $profile = $user->profile ?? $user->profile()->create(['display_name' => $user->name]);

        // Delete old avatar if present in storage
        if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
            Storage::disk('public')->delete($profile->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $profile->update(['avatar' => $path]);

        UserActivity::record($user->id, 'profile.avatar', 'Uploaded a new avatar image');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully!',
                'avatar_url' => asset('storage/' . $path),
            ]);
        }

        return back()->with('success', 'Avatar updated successfully!');
    }

    /**
     * Update favorite fandom categories.
     */
    public function updateFandoms(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ]);

        $user = $request->user();
        $categoryIds = $request->input('categories', []);

        $user->favoriteCategories()->sync($categoryIds);

        UserActivity::record(
            $user->id,
            'fandoms.sync',
            'Updated favorite fandoms (' . count($categoryIds) . ' selected)'
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Favorite fandoms updated!',
                'count' => count($categoryIds),
            ]);
        }

        return back()->with('success', 'Favorite fandoms updated successfully!');
    }

    /**
     * Update preferences (theme, accessibility).
     */
    public function updatePreferences(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'theme' => ['required', Rule::in(['dark', 'light', 'system'])],
            'animations' => ['nullable', 'boolean'],
            'language' => ['nullable', 'string', 'max:10'],
            'reduced_motion' => ['nullable', 'boolean'],
            'high_contrast' => ['nullable', 'boolean'],
            'font_size' => ['required', Rule::in(['small', 'normal', 'large'])],
        ]);

        $user = $request->user();
        $profile = $user->profile ?? $user->profile()->create(['display_name' => $user->name]);

        $displayPrefs = [
            'theme' => $validated['theme'],
            'animations' => $request->boolean('animations', true),
            'language' => $validated['language'] ?? 'en',
        ];

        $accessPrefs = [
            'reduced_motion' => $request->boolean('reduced_motion', false),
            'high_contrast' => $request->boolean('high_contrast', false),
            'font_size' => $validated['font_size'],
        ];

        $profile->update([
            'display_preferences' => $displayPrefs,
            'accessibility_preferences' => $accessPrefs,
        ]);

        UserActivity::record($user->id, 'preferences.update', 'Updated UI and accessibility preferences');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Preferences saved successfully!',
                'preferences' => [
                    'display' => $displayPrefs,
                    'accessibility' => $accessPrefs,
                ],
            ]);
        }

        return back()->with('success', 'Preferences saved successfully!');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        UserActivity::record($user->id, 'security.password', 'Changed account password');

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Delete user account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Prevent sole admin from deleting their own account
        if ($user->isAdmin() && \App\Models\User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the only administrator account.');
        }

        if ($user->profile && $user->profile->avatar && Storage::disk('public')->exists($user->profile->avatar)) {
            Storage::disk('public')->delete($user->profile->avatar);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Your account has been deleted. We are sad to see you go!');
    }
}
