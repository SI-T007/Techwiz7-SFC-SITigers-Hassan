<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminEventController extends Controller
{
    /**
     * Display a listing of fandom events.
     */
    public function index(Request $request): View
    {
        $query = Event::with('category')->latest('starts_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('venue_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('event_type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        $events = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $counts = [
            'total' => Event::count(),
            'upcoming' => Event::where('status', 'upcoming')->where('starts_at', '>=', now())->count(),
            'ongoing' => Event::where('status', 'ongoing')->count(),
            'completed' => Event::where('status', 'completed')->count(),
        ];

        return view('admin.events.index', compact('events', 'categories', 'counts'));
    }

    /**
     * Show form to create a new event.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.events.create', compact('categories'));
    }

    /**
     * Store newly created event in storage.
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('cover_image')) {
            $imagePath = $request->file('cover_image')->store('events', 'public');
        }

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Event::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $event = Event::create([
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'cover_image' => $imagePath,
            'event_type' => $validated['event_type'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'] ?? null,
            'venue_name' => $validated['venue_name'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'ticket_url' => $validated['ticket_url'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_online' => $request->boolean('is_online'),
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event "' . $event->title . '" created successfully.')
            ->with('status', 'Event "' . $event->title . '" created successfully.');
    }

    /**
     * Show form for editing an event.
     */
    public function edit(Event $event): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.events.edit', compact('event', 'categories'));
    }

    /**
     * Update an event in storage.
     */
    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($event->cover_image && Storage::disk('public')->exists($event->cover_image)) {
                Storage::disk('public')->delete($event->cover_image);
            }
            $event->cover_image = $request->file('cover_image')->store('events', 'public');
        }

        if ($event->title !== $validated['title']) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (Event::where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $event->slug = $slug;
        }

        $event->category_id = $validated['category_id'] ?? null;
        $event->title = $validated['title'];
        $event->description = $validated['description'] ?? null;
        $event->event_type = $validated['event_type'];
        $event->starts_at = $validated['starts_at'];
        $event->ends_at = $validated['ends_at'] ?? null;
        $event->venue_name = $validated['venue_name'] ?? null;
        $event->address = $validated['address'] ?? null;
        $event->city = $validated['city'] ?? null;
        $event->country = $validated['country'] ?? null;
        $event->latitude = $validated['latitude'] ?? null;
        $event->longitude = $validated['longitude'] ?? null;
        $event->ticket_url = $validated['ticket_url'] ?? null;
        $event->is_featured = $request->boolean('is_featured');
        $event->is_online = $request->boolean('is_online');
        $event->status = $validated['status'];
        $event->save();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event "' . $event->title . '" updated successfully.')
            ->with('status', 'Event "' . $event->title . '" updated successfully.');
    }

    /**
     * Remove an event from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        if ($event->cover_image && Storage::disk('public')->exists($event->cover_image)) {
            Storage::disk('public')->delete($event->cover_image);
        }

        $title = $event->title;
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event "' . $title . '" deleted successfully.')
            ->with('status', 'Event "' . $title . '" deleted successfully.');
    }
}
