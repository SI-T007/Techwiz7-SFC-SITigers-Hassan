<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReleaseRequest;
use App\Http\Requests\UpdateReleaseRequest;
use App\Models\Category;
use App\Models\Release;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminReleaseController extends Controller
{
    /**
     * Display a listing of upcoming and past releases.
     */
    public function index(Request $request): View
    {
        $query = Release::with('category')->latest('release_date');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('developer_publisher', 'like', "%{$search}%")
                  ->orWhere('platform', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        $releases = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $counts = [
            'total' => Release::count(),
            'upcoming' => Release::where('release_date', '>=', now()->toDateString())->count(),
            'released' => Release::where('status', 'released')->orWhere('release_date', '<', now()->toDateString())->count(),
        ];

        return view('admin.releases.index', compact('releases', 'categories', 'counts'));
    }

    /**
     * Show form to create a new release entry.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.releases.create', compact('categories'));
    }

    /**
     * Store newly created release entry in storage.
     */
    public function store(StoreReleaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('cover_image')) {
            $imagePath = $request->file('cover_image')->store('releases', 'public');
        }

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Release::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $tags = $this->parseTags($request->input('tags'));

        $release = Release::create([
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => $slug,
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'cover_image' => $imagePath,
            'release_date' => $validated['release_date'],
            'platform' => $validated['platform'] ?? null,
            'developer_publisher' => $validated['developer_publisher'] ?? null,
            'external_url' => $validated['external_url'] ?? null,
            'status' => $validated['status'],
            'tags' => $tags,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return redirect()->route('admin.releases.index')
            ->with('success', 'Release "' . $release->title . '" created successfully.')
            ->with('status', 'Release "' . $release->title . '" created successfully.');
    }

    /**
     * Show form for editing an existing release.
     */
    public function edit(Release $release): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.releases.edit', compact('release', 'categories'));
    }

    /**
     * Update specified release in storage.
     */
    public function update(UpdateReleaseRequest $request, Release $release): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($release->cover_image && Storage::disk('public')->exists($release->cover_image)) {
                Storage::disk('public')->delete($release->cover_image);
            }
            $release->cover_image = $request->file('cover_image')->store('releases', 'public');
        }

        if ($release->title !== $validated['title']) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (Release::where('slug', $slug)->where('id', '!=', $release->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $release->slug = $slug;
        }

        $tags = $this->parseTags($request->input('tags'));

        $release->category_id = $validated['category_id'] ?? null;
        $release->title = $validated['title'];
        $release->type = $validated['type'];
        $release->description = $validated['description'] ?? null;
        $release->release_date = $validated['release_date'];
        $release->platform = $validated['platform'] ?? null;
        $release->developer_publisher = $validated['developer_publisher'] ?? null;
        $release->external_url = $validated['external_url'] ?? null;
        $release->status = $validated['status'];
        $release->tags = $tags;
        $release->is_featured = $request->boolean('is_featured');
        $release->save();

        return redirect()->route('admin.releases.index')
            ->with('success', 'Release "' . $release->title . '" updated successfully.')
            ->with('status', 'Release "' . $release->title . '" updated successfully.');
    }

    /**
     * Remove specified release from storage.
     */
    public function destroy(Release $release): RedirectResponse
    {
        if ($release->cover_image && Storage::disk('public')->exists($release->cover_image)) {
            Storage::disk('public')->delete($release->cover_image);
        }

        $title = $release->title;
        $release->delete();

        return redirect()->route('admin.releases.index')
            ->with('success', 'Release "' . $title . '" deleted successfully.')
            ->with('status', 'Release "' . $title . '" deleted successfully.');
    }

    /**
     * Helper to parse tags input (comma string or array).
     */
    protected function parseTags($input): ?array
    {
        if (is_array($input)) {
            return array_values(array_filter(array_map('trim', $input)));
        }

        if (is_string($input) && trim($input) !== '') {
            return array_values(array_filter(array_map('trim', explode(',', $input))));
        }

        return null;
    }
}
