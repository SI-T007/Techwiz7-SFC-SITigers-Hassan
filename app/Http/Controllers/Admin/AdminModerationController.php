<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FanSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminModerationController extends Controller
{
    /**
     * Display the moderation queue for fan submissions.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'pending');

        $query = FanSubmission::with(['user.profile', 'category', 'reviewer'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $submissions = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $counts = [
            'pending' => FanSubmission::where('status', 'pending')->count(),
            'approved' => FanSubmission::where('status', 'approved')->count(),
            'rejected' => FanSubmission::where('status', 'rejected')->count(),
            'all' => FanSubmission::count(),
        ];

        return view('admin.submissions.index', compact('submissions', 'categories', 'counts', 'status'));
    }

    /**
     * Inspect a fan submission in detail.
     */
    public function show(FanSubmission $submission): View
    {
        $submission->load(['user.profile', 'category', 'reviewer']);

        return view('admin.submissions.show', compact('submission'));
    }

    /**
     * Approve a fan submission.
     */
    public function approve(Request $request, FanSubmission $submission): RedirectResponse
    {
        $submission->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'published_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()->route('admin.submissions.index')
            ->with('success', 'Submission "' . $submission->title . '" has been approved and published to the community portal.')
            ->with('status', 'Submission "' . $submission->title . '" has been approved and published to the community portal.');
    }

    /**
     * Reject a fan submission with reason.
     */
    public function reject(Request $request, FanSubmission $submission): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $submission->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        return redirect()->route('admin.submissions.index')
            ->with('success', 'Submission "' . $submission->title . '" has been marked as rejected.')
            ->with('status', 'Submission "' . $submission->title . '" has been marked as rejected.');
    }
}
