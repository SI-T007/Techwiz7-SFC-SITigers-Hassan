<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Models\Category;
use App\Models\Content;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminMediaController extends Controller
{
    /**
     * Display a listing of all media assets.
     */
    public function index(Request $request): View
    {
        $query = Media::with(['category', 'content', 'uploader'])
            ->withAvg('ratings as ratings_avg', 'score')
            ->withCount('ratings')
            ->latest('id');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('media_type')) {
            $query->where('media_type', $request->input('media_type'));
        }

        if ($request->filled('quality')) {
            $query->where('quality', $request->input('quality'));
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('youtube_video_id', 'like', "%{$search}%");
            });
        }

        $mediaItems = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.media.index', compact('mediaItems', 'categories'));
    }

    /**
     * Show the form for creating a new media asset.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $contents = Content::published()->orderBy('title')->get();

        return view('admin.media.create', compact('categories', 'contents'));
    }

    /**
     * Store a newly created media asset in database.
     */
    public function store(StoreMediaRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('media/files', 'public');
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('media/thumbnails', 'public');
        }

        $externalUrl = $validated['youtube_url'] ?? $validated['external_url'] ?? null;
        $youtubeVideoId = Media::extractYouTubeId($externalUrl);

        $media = Media::create([
            'category_id' => $validated['category_id'] ?? null,
            'content_id' => $validated['content_id'] ?? null,
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'type' => $validated['type'] ?? 'video',
            'media_type' => $validated['media_type'] ?? 'movie_trailer',
            'quality' => $validated['quality'] ?? 'HD',
            'duration' => $validated['duration'] ?? null,
            'language' => $validated['language'] ?? null,
            'release_date' => $validated['release_date'] ?? null,
            'file_path' => $filePath,
            'external_url' => $externalUrl,
            'youtube_video_id' => $youtubeVideoId,
            'thumbnail' => $thumbnailPath,
            'description' => $validated['description'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'status' => $validated['status'],
            'views' => 0,
        ]);

        return redirect()->route('admin.media.index')
            ->with('success', 'Media asset "' . $media->title . '" created successfully.')
            ->with('status', 'Media asset "' . $media->title . '" created successfully.');
    }

    /**
     * Show the form for editing the media asset.
     */
    public function edit(Media $media): View
    {
        $categories = Category::orderBy('name')->get();
        $contents = Content::published()->orderBy('title')->get();

        return view('admin.media.edit', compact('media', 'categories', 'contents'));
    }

    /**
     * Update the specified media asset in database.
     */
    public function update(UpdateMediaRequest $request, Media $media): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            if ($media->file_path && Storage::disk('public')->exists($media->file_path)) {
                Storage::disk('public')->delete($media->file_path);
            }
            $media->file_path = $request->file('file')->store('media/files', 'public');
        }

        if ($request->hasFile('thumbnail')) {
            if ($media->thumbnail && Storage::disk('public')->exists($media->thumbnail)) {
                Storage::disk('public')->delete($media->thumbnail);
            }
            $media->thumbnail = $request->file('thumbnail')->store('media/thumbnails', 'public');
        }

        $externalUrl = $validated['youtube_url'] ?? $validated['external_url'] ?? $media->external_url;
        $youtubeVideoId = Media::extractYouTubeId($externalUrl) ?: $media->youtube_video_id;

        $media->category_id = $validated['category_id'] ?? null;
        $media->content_id = $validated['content_id'] ?? null;
        $media->title = $validated['title'];
        $media->type = $validated['type'] ?? $media->type;
        $media->media_type = $validated['media_type'] ?? $media->media_type;
        $media->quality = $validated['quality'] ?? $media->quality;
        $media->duration = $validated['duration'] ?? $media->duration;
        $media->language = $validated['language'] ?? $media->language;
        $media->release_date = $validated['release_date'] ?? $media->release_date;
        $media->external_url = $externalUrl;
        $media->youtube_video_id = $youtubeVideoId;
        $media->description = $validated['description'] ?? null;
        $media->tags = $validated['tags'] ?? $media->tags;
        $media->is_featured = $request->boolean('is_featured');
        $media->status = $validated['status'];
        $media->save();

        return redirect()->route('admin.media.index')
            ->with('success', 'Media asset "' . $media->title . '" updated successfully.')
            ->with('status', 'Media asset "' . $media->title . '" updated successfully.');
    }

    /**
     * Remove the specified media asset from database.
     */
    public function destroy(Media $media): RedirectResponse
    {
        if ($media->file_path && Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }
        if ($media->thumbnail && Storage::disk('public')->exists($media->thumbnail)) {
            Storage::disk('public')->delete($media->thumbnail);
        }

        $title = $media->title;
        $media->delete();

        return redirect()->route('admin.media.index')
            ->with('success', 'Media asset "' . $title . '" deleted successfully.')
            ->with('status', 'Media asset "' . $title . '" deleted successfully.');
    }
}
