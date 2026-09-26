<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\UserActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): View
    {
        $query = Category::withCount([
            'contents',
            'characters',
            'events',
            'releases',
            'media',
            'fanSubmissions',
            'users',
        ]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $categories = $query->orderBy('sort_order', 'asc')->paginate(15)->withQueryString();

        $metrics = [
            'total' => Category::count(),
            'active' => Category::where('status', 'active')->count(),
            'inactive' => Category::where('status', 'inactive')->count(),
        ];

        return view('admin.categories.index', compact('categories', 'metrics'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'cover_image_url' => ['nullable', 'url', 'max:1000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        // Ensure unique slug
        $baseSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('categories', 'public');
        } elseif (!empty($validated['cover_image_url'])) {
            $coverImagePath = $validated['cover_image_url'];
        }

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'bi-stars',
            'cover_image' => $coverImagePath,
            'status' => $validated['status'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator created fandom category: {$category->name}"
        );

        return redirect()->route('admin.categories.index')->with('success', "Fandom category '{$category->name}' was created successfully.");
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'cover_image_url' => ['nullable', 'url', 'max:1000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $coverImagePath = $category->cover_image;
        if ($request->hasFile('cover_image')) {
            if ($category->cover_image && !filter_var($category->cover_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($category->cover_image);
            }
            $coverImagePath = $request->file('cover_image')->store('categories', 'public');
        } elseif (!empty($validated['cover_image_url'])) {
            $coverImagePath = $validated['cover_image_url'];
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'bi-stars',
            'cover_image' => $coverImagePath,
            'status' => $validated['status'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator updated fandom category: {$category->name}"
        );

        return redirect()->route('admin.categories.index')->with('success', "Fandom category '{$category->name}' was updated successfully.");
    }

    /**
     * Toggle category active status.
     */
    public function toggleStatus(Category $category): RedirectResponse
    {
        $newStatus = $category->status === 'active' ? 'inactive' : 'active';
        $category->update(['status' => $newStatus]);

        return back()->with('success', "Category '{$category->name}' status changed to {$newStatus}.");
    }

    /**
     * Safely remove the specified category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Safe Deletion Verification
        $hasContents = $category->contents()->exists();
        $hasCharacters = $category->characters()->exists();
        $hasEvents = $category->events()->exists();
        $hasReleases = $category->releases()->exists();
        $hasMedia = $category->media()->exists();
        $hasSubmissions = $category->fanSubmissions()->exists();

        if ($hasContents || $hasCharacters || $hasEvents || $hasReleases || $hasMedia || $hasSubmissions) {
            $relatedItems = [];
            if ($hasContents) $relatedItems[] = 'Content Articles';
            if ($hasCharacters) $relatedItems[] = 'Characters';
            if ($hasEvents) $relatedItems[] = 'Events';
            if ($hasReleases) $relatedItems[] = 'Releases';
            if ($hasMedia) $relatedItems[] = 'Media Items';
            if ($hasSubmissions) $relatedItems[] = 'Fan Submissions';

            $relatedList = implode(', ', $relatedItems);

            return back()->with('error', "Cannot delete category '{$category->name}' because it is linked to active records ({$relatedList}). Please deactivate the category or reassign its linked records first.");
        }

        // Clean up pivot relationships
        $category->users()->detach();

        if ($category->cover_image && !filter_var($category->cover_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($category->cover_image);
        }

        $catName = $category->name;
        $category->delete();

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator deleted fandom category: {$catName}"
        );

        return redirect()->route('admin.categories.index')->with('success', "Fandom category '{$catName}' has been permanently deleted.");
    }
}
