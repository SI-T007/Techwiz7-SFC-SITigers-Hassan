<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Character;
use App\Models\UserActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCharacterController extends Controller
{
    /**
     * Display a listing of characters.
     */
    public function index(Request $request): View
    {
        $query = Character::with('category');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('franchise', 'like', "%{$search}%")
                  ->orWhere('biography', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $characters = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::active()->get();

        $metrics = [
            'total' => Character::count(),
            'active' => Character::where('status', 'active')->count(),
            'featured' => Character::where('is_featured', true)->count(),
        ];

        return view('admin.characters.index', compact('characters', 'categories', 'metrics'));
    }

    /**
     * Show the form for creating a new character profile.
     */
    public function create(): View
    {
        $categories = Category::active()->get();
        return view('admin.characters.create', compact('categories'));
    }

    /**
     * Store a newly created character profile.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:characters,slug'],
            'franchise' => ['required', 'string', 'max:255'],
            'biography' => ['required', 'string'],
            'abilities' => ['nullable', 'string'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'profile_image_url' => ['nullable', 'url', 'max:1000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'cover_image_url' => ['nullable', 'url', 'max:1000'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $baseSlug = $slug;
        $counter = 1;
        while (Character::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('characters/profile', 'public');
        } elseif (!empty($validated['profile_image_url'])) {
            $profileImagePath = $validated['profile_image_url'];
        }

        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('characters/cover', 'public');
        } elseif (!empty($validated['cover_image_url'])) {
            $coverImagePath = $validated['cover_image_url'];
        }

        $character = Character::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'franchise' => $validated['franchise'],
            'biography' => $validated['biography'],
            'abilities' => $validated['abilities'] ?? null,
            'profile_image' => $profileImagePath,
            'cover_image' => $coverImagePath,
            'is_featured' => $request->boolean('is_featured'),
            'status' => $validated['status'],
        ]);

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator created character profile: {$character->name}"
        );

        return redirect()->route('admin.characters.index')->with('success', "Character profile '{$character->name}' was created successfully.");
    }

    /**
     * Show the form for editing the character profile.
     */
    public function edit(Character $character): View
    {
        $categories = Category::active()->get();
        return view('admin.characters.edit', compact('character', 'categories'));
    }

    /**
     * Update the character profile in storage.
     */
    public function update(Request $request, Character $character): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('characters', 'slug')->ignore($character->id)],
            'franchise' => ['required', 'string', 'max:255'],
            'biography' => ['required', 'string'],
            'abilities' => ['nullable', 'string'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'profile_image_url' => ['nullable', 'url', 'max:1000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'cover_image_url' => ['nullable', 'url', 'max:1000'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $profileImagePath = $character->profile_image;
        if ($request->hasFile('profile_image')) {
            if ($character->profile_image && !filter_var($character->profile_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($character->profile_image);
            }
            $profileImagePath = $request->file('profile_image')->store('characters/profile', 'public');
        } elseif (!empty($validated['profile_image_url'])) {
            $profileImagePath = $validated['profile_image_url'];
        }

        $coverImagePath = $character->cover_image;
        if ($request->hasFile('cover_image')) {
            if ($character->cover_image && !filter_var($character->cover_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($character->cover_image);
            }
            $coverImagePath = $request->file('cover_image')->store('characters/cover', 'public');
        } elseif (!empty($validated['cover_image_url'])) {
            $coverImagePath = $validated['cover_image_url'];
        }

        $character->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'franchise' => $validated['franchise'],
            'biography' => $validated['biography'],
            'abilities' => $validated['abilities'] ?? null,
            'profile_image' => $profileImagePath,
            'cover_image' => $coverImagePath,
            'is_featured' => $request->boolean('is_featured'),
            'status' => $validated['status'],
        ]);

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator updated character profile: {$character->name}"
        );

        return redirect()->route('admin.characters.index')->with('success', "Character profile '{$character->name}' was updated successfully.");
    }

    /**
     * Remove the specified character from storage.
     */
    public function destroy(Character $character): RedirectResponse
    {
        if ($character->profile_image && !filter_var($character->profile_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($character->profile_image);
        }
        if ($character->cover_image && !filter_var($character->cover_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($character->cover_image);
        }

        $charName = $character->name;
        $character->delete();

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator deleted character profile: {$charName}"
        );

        return redirect()->route('admin.characters.index')->with('success', "Character profile '{$charName}' was deleted.");
    }
}
