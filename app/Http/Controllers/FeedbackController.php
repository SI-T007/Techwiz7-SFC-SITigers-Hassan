<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    /**
     * Show the platform feedback form.
     */
    public function create(): View
    {
        $breadcrumbs = [
            ['label' => 'Feedback', 'url' => ''],
        ];

        return view('pages.feedback', compact('breadcrumbs'));
    }

    /**
     * Store new user feedback.
     */
    public function store(StoreFeedbackRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        Feedback::create([
            'user_id' => $user?->id,
            'name' => $user ? $user->name : ($validated['name'] ?? 'Visitor'),
            'email' => $user ? $user->email : ($validated['email'] ?? null),
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        return redirect()->route('feedback.create')
            ->with('success', 'Thank you for your feedback! Our platform team reviews every submission to enhance the universe.')
            ->with('status', 'Thank you for your feedback! Our platform team reviews every submission to enhance the universe.');
    }
}
