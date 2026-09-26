<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Content;
use App\Models\Media;
use App\Models\Rating;
use App\Models\Release;
use App\Models\UserActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Whitelist of ratable types.
     */
    protected array $morphMap = [
        'content' => Content::class,
        Content::class => Content::class,
        'media' => Media::class,
        Media::class => Media::class,
        'character' => Character::class,
        Character::class => Character::class,
        'release' => Release::class,
        Release::class => Release::class,
    ];

    /**
     * Store or update a rating for a ratable item.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'ratable_type' => ['required', 'string'],
            'ratable_id' => ['required', 'integer'],
            'score' => ['required', 'integer', 'between:1,5'],
        ]);

        $rawType = $request->input('ratable_type');
        $type = $this->morphMap[$rawType] ?? null;

        if (! $type || ! class_exists($type)) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Invalid resource type for rating.'], 422);
            }
            return back()->with('error', 'Invalid resource type.');
        }

        $id = (int) $request->input('ratable_id');
        $item = $type::find($id);

        if (! $item) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Resource not found.'], 404);
            }
            return back()->with('error', 'Resource not found.');
        }

        $userId = $request->user()->id;
        $score = (int) $request->input('score');

        $rating = Rating::updateOrCreate(
            [
                'user_id' => $userId,
                'ratable_type' => $type,
                'ratable_id' => $id,
            ],
            [
                'score' => $score,
            ]
        );

        // Recalculate aggregates
        $avgScore = Rating::where('ratable_type', $type)->where('ratable_id', $id)->avg('score');
        $ratingsCount = Rating::where('ratable_type', $type)->where('ratable_id', $id)->count();
        $formattedAvg = $avgScore ? round((float) $avgScore, 1) : null;

        // Optionally update popularity cache if Content
        if ($item instanceof Content) {
            $item->increment('popularity', 5);
        }

        $itemTitle = $item->title ?? $item->name ?? 'item';
        UserActivity::create([
            'user_id' => $userId,
            'description' => "Rated '{$itemTitle}' {$score}/5 stars",
            'ip_address' => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your rating of ' . $score . ' stars has been recorded!',
                'user_score' => $score,
                'average_score' => $formattedAvg,
                'ratings_count' => $ratingsCount,
            ]);
        }

        return back()->with('status', 'Your rating has been saved.');
    }

    /**
     * Delete an existing user rating by ratable_type and ratable_id.
     */
    public function destroy(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'ratable_type' => ['required', 'string'],
            'ratable_id' => ['required', 'integer'],
        ]);

        $rawType = $request->input('ratable_type');
        $type = $this->morphMap[$rawType] ?? null;

        if (! $type) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Invalid resource type.'], 422);
            }
            return back()->with('error', 'Invalid resource type.');
        }

        $id = (int) $request->input('ratable_id');
        $rating = Rating::where('user_id', $request->user()->id)
            ->where('ratable_type', $type)
            ->where('ratable_id', $id)
            ->first();

        if ($rating) {
            $item = $type::find($id);
            $itemTitle = $item?->title ?? $item?->name ?? 'item';
            $rating->delete();

            UserActivity::create([
                'user_id' => $request->user()->id,
                'description' => "Removed rating from '{$itemTitle}'",
                'ip_address' => $request->ip(),
            ]);
        }

        $avgScore = Rating::where('ratable_type', $type)->where('ratable_id', $id)->avg('score');
        $ratingsCount = Rating::where('ratable_type', $type)->where('ratable_id', $id)->count();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rating removed.',
                'user_score' => null,
                'average_score' => $avgScore ? round((float) $avgScore, 1) : null,
                'ratings_count' => $ratingsCount,
            ]);
        }

        return back()->with('status', 'Rating removed.');
    }

    /**
     * Delete rating directly by rating ID.
     */
    public function destroyRating(Rating $rating, Request $request): JsonResponse|RedirectResponse
    {
        if ($rating->user_id !== $request->user()->id) {
            abort(403);
        }

        $type = $rating->ratable_type;
        $id = $rating->ratable_id;
        $item = $type && class_exists($type) ? $type::find($id) : null;
        $itemTitle = $item?->title ?? $item?->name ?? 'item';

        $rating->delete();

        UserActivity::create([
            'user_id' => $request->user()->id,
            'description' => "Removed rating from '{$itemTitle}'",
            'ip_address' => $request->ip(),
        ]);

        $avgScore = Rating::where('ratable_type', $type)->where('ratable_id', $id)->avg('score');
        $ratingsCount = Rating::where('ratable_type', $type)->where('ratable_id', $id)->count();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rating removed.',
                'ratings_count' => $ratingsCount,
                'average_score' => $avgScore ? round((float) $avgScore, 1) : null,
            ]);
        }

        return back()->with('status', 'Rating removed.');
    }
}
