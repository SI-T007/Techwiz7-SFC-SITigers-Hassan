<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Character;
use App\Models\Content;
use App\Models\Event;
use App\Models\Release;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    /**
     * Whitelist of bookmarkable types.
     */
    protected array $morphMap = [
        'content' => Content::class,
        'character' => Character::class,
        'release' => Release::class,
        'event' => Event::class,
        Content::class => Content::class,
        Character::class => Character::class,
        Release::class => Release::class,
        Event::class => Event::class,
    ];

    /**
     * Display the user's bookmarks collection.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = $user->bookmarks()->with('bookmarkable')->latest();

        if ($request->filled('type')) {
            $typeAlias = strtolower($request->input('type'));
            if (isset($this->morphMap[$typeAlias])) {
                $query->where('bookmarkable_type', $this->morphMap[$typeAlias]);
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('note', 'like', "%{$search}%");
        }

        $bookmarks = $query->paginate(12)->withQueryString();

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('user.dashboard')],
            ['label' => 'My Bookmarks', 'url' => ''],
        ];

        return view('pages.bookmarks.index', compact('bookmarks', 'breadcrumbs'));
    }

    /**
     * Toggle bookmark state for an item.
     */
    public function toggle(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'bookmarkable_type' => ['required', 'string'],
            'bookmarkable_id' => ['required', 'integer'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $rawType = $request->input('bookmarkable_type');
        $type = $this->morphMap[$rawType] ?? null;

        if (! $type || ! class_exists($type)) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Invalid resource type for bookmarking.'], 422);
            }
            return back()->with('error', 'Invalid resource type.');
        }

        $id = (int) $request->input('bookmarkable_id');
        $item = $type::find($id);

        if (! $item) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Resource not found.'], 404);
            }
            return back()->with('error', 'Resource not found.');
        }

        $userId = $request->user()->id;
        $existing = Bookmark::where('user_id', $userId)
            ->where('bookmarkable_type', $type)
            ->where('bookmarkable_id', $id)
            ->first();

        if ($existing) {
            $existing->delete();
            $isBookmarked = false;
            $message = 'Removed from your bookmarks.';
            $bookmarkId = null;
        } else {
            $bookmark = Bookmark::create([
                'user_id' => $userId,
                'bookmarkable_type' => $type,
                'bookmarkable_id' => $id,
                'note' => $request->input('note'),
            ]);
            $isBookmarked = true;
            $message = 'Saved to your universe bookmarks!';
            $bookmarkId = $bookmark->id;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_bookmarked' => $isBookmarked,
                'bookmark_id' => $bookmarkId,
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Update personal note on an existing bookmark.
     */
    public function updateNote(Request $request, Bookmark $bookmark): JsonResponse|RedirectResponse
    {
        if ($bookmark->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action on bookmark.');
        }

        $validated = $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $bookmark->update([
            'note' => $validated['note'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bookmark note updated successfully.',
                'note' => $bookmark->note,
            ]);
        }

        return back()->with('status', 'Note updated successfully.');
    }

    /**
     * Delete an existing bookmark.
     */
    public function destroy(Request $request, Bookmark $bookmark): JsonResponse|RedirectResponse
    {
        if ($bookmark->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action on bookmark.');
        }

        $bookmark->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bookmark removed successfully.',
            ]);
        }

        return back()->with('status', 'Bookmark removed successfully.');
    }
}
