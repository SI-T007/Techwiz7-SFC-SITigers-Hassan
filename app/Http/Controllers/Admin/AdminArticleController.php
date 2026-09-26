<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Content;
use App\Models\Genre;
use App\Models\UserActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminArticleController extends Controller
{
    /**
     * Display a listing of articles.
     */
    public function index(Request $request): View
    {
        $query = Content::where('type', 'article')->with(['category', 'user']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        $articles = $query->latest('published_at')->paginate(15)->withQueryString();
        $categories = Category::active()->get();

        $metrics = [
            'total' => Content::where('type', 'article')->count(),
            'published' => Content::where('type', 'article')->where('status', 'published')->count(),
            'featured' => Content::where('type', 'article')->where('is_featured', true)->count(),
            'draft' => Content::where('type', 'article')->where('status', 'draft')->count(),
        ];

        return view('admin.articles.index', compact('articles', 'categories', 'metrics'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create(): View
    {
        $categories = Category::active()->get();
        $genres = Genre::all();
        return view('admin.articles.create', compact('categories', 'genres'));
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:500'],
            'slug' => ['nullable', 'string', 'max:500', 'unique:contents,slug'],
            'description' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'thumbnail_url' => ['nullable', 'url', 'max:1000'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['exists:genres,id'],
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $baseSlug = $slug;
        $counter = 1;
        while (Content::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('articles', 'public');
        } elseif (!empty($validated['thumbnail_url'])) {
            $thumbnailPath = $validated['thumbnail_url'];
        }

        $publishedAt = $validated['published_at'] ?? ($validated['status'] === 'published' ? now() : null);

        $article = Content::create([
            'category_id' => $validated['category_id'],
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'type' => 'article',
            'description' => $validated['description'] ?? null,
            'body' => $validated['body'],
            'thumbnail' => $thumbnailPath,
            'is_featured' => $request->boolean('is_featured'),
            'status' => $validated['status'],
            'published_at' => $publishedAt,
        ]);

        if (!empty($validated['genres'])) {
            $article->genres()->sync($validated['genres']);
        }

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator published article: {$article->title}"
        );

        return redirect()->route('admin.articles.index')->with('success', "Lore article '{$article->title}' was created successfully.");
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(Content $article): View
    {
        $categories = Category::active()->get();
        $genres = Genre::all();
        $selectedGenres = $article->genres->pluck('id')->toArray();

        return view('admin.articles.edit', compact('article', 'categories', 'genres', 'selectedGenres'));
    }

    /**
     * Update the specified article in storage.
     */
    public function update(Request $request, Content $article): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:500'],
            'slug' => ['required', 'string', 'max:500', Rule::unique('contents', 'slug')->ignore($article->id)],
            'description' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'thumbnail_url' => ['nullable', 'url', 'max:1000'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['exists:genres,id'],
        ]);

        $thumbnailPath = $article->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail && !filter_var($article->thumbnail, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($article->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('articles', 'public');
        } elseif (!empty($validated['thumbnail_url'])) {
            $thumbnailPath = $validated['thumbnail_url'];
        }

        $publishedAt = $validated['published_at'] ?? ($validated['status'] === 'published' && !$article->published_at ? now() : $article->published_at);

        $article->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'] ?? null,
            'body' => $validated['body'],
            'thumbnail' => $thumbnailPath,
            'is_featured' => $request->boolean('is_featured'),
            'status' => $validated['status'],
            'published_at' => $publishedAt,
        ]);

        if (isset($validated['genres'])) {
            $article->genres()->sync($validated['genres']);
        }

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator updated article: {$article->title}"
        );

        return redirect()->route('admin.articles.index')->with('success', "Lore article '{$article->title}' was updated successfully.");
    }

    /**
     * Toggle featured status for article.
     */
    public function toggleFeatured(Content $article): RedirectResponse
    {
        $newFeatured = !$article->is_featured;
        $article->update(['is_featured' => $newFeatured]);

        $statusStr = $newFeatured ? 'marked as featured' : 'unmarked from featured';
        return back()->with('success', "Article '{$article->title}' was {$statusStr}.");
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroy(Content $article): RedirectResponse
    {
        $artTitle = $article->title;
        $article->genres()->detach();
        $article->delete();

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator deleted article: {$artTitle}"
        );

        return redirect()->route('admin.articles.index')->with('success', "Article '{$artTitle}' was deleted.");
    }
}
