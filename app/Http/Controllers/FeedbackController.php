<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use App\Models\UserActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    
    public function create(Request $request): View
    {
        $breadcrumbs = [
            ['label' => 'Feedback & Bug Reports', 'url' => ''],
        ];

        $initialType = $request->query('type', 'suggestion');
        if (!in_array($initialType, ['bug', 'suggestion', 'query', 'other'])) {
            $initialType = 'suggestion';
        }

        $user = $request->user();
        $userFeedbacks = $user 
            ? Feedback::where('user_id', $user->id)->with('repliedBy')->latest()->take(10)->get()
            : collect();

        return view('pages.feedback', compact('breadcrumbs', 'initialType', 'userFeedbacks'));
    }

    /**
     * Store new user feedback (Supports AJAX and standard POST).
     */
    public function store(StoreFeedbackRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $feedback = Feedback::create([
            'user_id' => $user?->id,
            'name' => $user ? $user->name : ($validated['name'] ?? 'Visitor'),
            'email' => $user ? $user->email : ($validated['email'] ?? null),
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        if ($user) {
            UserActivity::record(
                $user->id,
                'submitted_feedback',
                "Submitted {$validated['type']} feedback: {$validated['subject']}",
                Feedback::class,
                $feedback->id
            );
        }

        $successMsg = "Thank you! Your feedback has been sent to our administration team. Ticket #{$feedback->id} has been recorded.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'ticket_id' => $feedback->id,
                'feedback' => [
                    'id' => $feedback->id,
                    'type' => $feedback->type,
                    'subject' => $feedback->subject,
                    'message' => $feedback->message,
                    'status' => $feedback->status,
                    'created_at' => $feedback->created_at->diffForHumans(),
                ],
            ]);
        }

        if ($request->input('redirect_to') === 'dashboard' || str_contains(url()->previous(), 'dashboard')) {
            return redirect()->route('user.dashboard')
                ->with('success', $successMsg);
        }

        return redirect()->route('feedback.create')
            ->with('success', $successMsg)
            ->with('ticket_id', $feedback->id);
    }
}
