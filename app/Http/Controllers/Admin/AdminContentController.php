<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\UpdateContentRequest;
use App\Models\Category;
use App\Models\Content;
use App\Models\Genre;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminContentController extends Controller
{
    /**
     * Display a listing of all content items.
     */
    public function index(Request $request): View
    {
        $query = Content::with(['category', 'author', 'genres'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $contents = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.content.index', compact('contents', 'categories'));
    }

    /**
     * Show the form for creating a new content entry.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $genres = Genre::orderBy('name')->get();

        return view('admin.content.create', compact('categories', 'genres'));
    }

    /**
     * Store a newly created content item in database.
     */
    public function store(StoreContentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Generate unique slug
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Content::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        // Handle thumbnail upload securely
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('content/thumbnails', 'public');
        }

        $content = Content::create([
            'category_id' => $validated['category_id'],
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'body' => $validated['body'] ?? null,
            'thumbnail' => $thumbnailPath,
            'media_url' => $validated['media_url'] ?? null,
            'release_date' => $validated['release_date'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published' ? now() : null,
            'views' => 0,
            'popularity' => 10,
        ]);

        // Sync genres
        if (! empty($validated['genres'])) {
            $content->genres()->sync($validated['genres']);
        }

        // If media_url is provided, also record in media table
        if (! empty($validated['media_url'])) {
            Media::create([
                'category_id' => $content->category_id,
                'content_id' => $content->id,
                'user_id' => $request->user()->id,
                'title' => $content->title . ' (Media Asset)',
                'type' => $content->type === 'trailer' ? 'trailer' : ($content->type === 'video' ? 'video' : 'image'),
                'external_url' => $validated['media_url'],
                'status' => 'published',
            ]);
        }

        return redirect()->route('admin.content.index')
            ->with('status', "Content item '{$content->title}' published successfully.");
    }

    /**
     * Show the form for editing the specified content.
     */
    public function edit(Content $content): View
    {
        $content->load(['genres', 'category']);
        $categories = Category::orderBy('name')->get();
        $genres = Genre::orderBy('name')->get();

        return view('admin.content.edit', compact('content', 'categories', 'genres'));
    }

    /**
     * Update the specified content item in database.
     */
    public function update(UpdateContentRequest $request, Content $content): RedirectResponse
    {
        $validated = $request->validated();

        // If title changed, update slug safely
        if ($validated['title'] !== $content->title) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (Content::where('slug', $slug)->where('id', '!=', $content->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $content->slug = $slug;
        }

        // Handle thumbnail upload securely
        if ($request->hasFile('thumbnail')) {
            if ($content->thumbnail && Storage::disk('public')->exists($content->thumbnail)) {
                Storage::disk('public')->delete($content->thumbnail);
            }
            $content->thumbnail = $request->file('thumbnail')->store('content/thumbnails', 'public');
        }

        $content->category_id = $validated['category_id'];
        $content->title = $validated['title'];
        $content->type = $validated['type'];
        $content->description = $validated['description'] ?? null;
        $content->body = $validated['body'] ?? null;
        $content->media_url = $validated['media_url'] ?? null;
        $content->release_date = $validated['release_date'] ?? null;
        $content->is_featured = $request->boolean('is_featured');
        $content->status = $validated['status'];

        if ($validated['status'] === 'published' && ! $content->published_at) {
            $content->published_at = now();
        }

        $content->save();

        // Sync genres
        $content->genres()->sync($validated['genres'] ?? []);

        return redirect()->route('admin.content.index')
            ->with('status', "Content '{$content->title}' updated successfully.");
    }

    /**
     * Remove the specified content (soft delete).
     */
    public function destroy(Content $content): RedirectResponse
    {
        $title = $content->title;
        $content->delete();

        return redirect()->route('admin.content.index')
            ->with('status', "Content '{$title}' moved to archive / deleted.");
    }
}
