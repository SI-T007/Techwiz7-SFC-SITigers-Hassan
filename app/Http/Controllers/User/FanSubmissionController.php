<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFanSubmissionRequest;
use App\Models\Category;
use App\Models\FanSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FanSubmissionController extends Controller
{
    /**
     * Show the fan content submission form.
     */
    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();

        $breadcrumbs = [
            ['label' => 'Community', 'url' => route('community.index')],
            ['label' => 'Submit Fan Creation', 'url' => ''],
        ];

        return view('pages.community.submit', compact('categories', 'breadcrumbs'));
    }

    /**
     * Store a new fan submission in the database with status = 'pending'.
     */
    public function store(StoreFanSubmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = null;
        $file = $request->file('featured_image') ?? $request->file('media_file');
        if ($file) {
            $imagePath = $file->store('submissions/images', 'public');
        }

        $body = $validated['body'] ?? $validated['content'] ?? null;
        $externalUrl = $validated['external_url'] ?? $validated['source_url'] ?? null;

        $submission = FanSubmission::create([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'type' => $validated['type'],
            'body' => $body,
            'featured_image' => $imagePath,
            'external_url' => $externalUrl,
            'status' => 'pending',
        ]);

        return redirect()->route('community.my-submissions')
            ->with('success', 'Your fan creation "' . $submission->title . '" has been submitted for moderation! It will appear publicly once approved.')
            ->with('status', 'Your fan creation "' . $submission->title . '" has been submitted for moderation! It will appear publicly once approved.');
    }

    /**
     * Display the authenticated user's submissions history.
     */
    public function mySubmissions(Request $request): View
    {
        $query = $request->user()->fanSubmissions()->with(['category'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $submissions = $query->paginate(10)->withQueryString();

        $breadcrumbs = [
            ['label' => 'Community', 'url' => route('community.index')],
            ['label' => 'My Submissions', 'url' => ''],
        ];

        return view('pages.community.my-submissions', compact('submissions', 'breadcrumbs'));
    }

    /**
     * Delete an unapproved/pending submission belonging to the user.
     */
    public function destroy(Request $request, FanSubmission $submission): RedirectResponse
    {
        if ($submission->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action on fan submission.');
        }

        if ($submission->isApproved()) {
            abort(403, 'Approved fan submissions cannot be deleted by users.');
        }

        if ($submission->featured_image && Storage::disk('public')->exists($submission->featured_image)) {
            Storage::disk('public')->delete($submission->featured_image);
        }

        $title = $submission->title;
        $submission->delete();

        return redirect()->route('community.my-submissions')
            ->with('success', 'Submission "' . $title . '" has been deleted.')
            ->with('status', 'Submission "' . $title . '" has been deleted.');
    }
}
