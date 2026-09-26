<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFeedbackController extends Controller
{
    /**
     * Display a listing of user feedback messages.
     */
    public function index(Request $request): View
    {
        $query = Feedback::with(['user', 'repliedBy'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $feedbacks = $query->paginate(15)->withQueryString();

        $counts = [
            'new' => Feedback::where('status', 'new')->count(),
            'read' => Feedback::where('status', 'read')->count(),
            'replied' => Feedback::where('status', 'replied')->count(),
            'total' => Feedback::count(),
        ];

        return view('admin.feedback.index', compact('feedbacks', 'counts'));
    }

    /**
     * Show feedback details.
     */
    public function show(Feedback $feedback): View
    {
        if ($feedback->status === 'new') {
            $feedback->update(['status' => 'read']);
        }

        $feedback->load(['user', 'repliedBy']);

        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Post administrative response / update remarks.
     */
    public function reply(Request $request, Feedback $feedback): RedirectResponse
    {
        $request->validate([
            'admin_reply' => ['required', 'string', 'max:3000'],
        ]);

        $feedback->update([
            'admin_reply' => $request->input('admin_reply'),
            'status' => 'replied',
            'replied_by' => $request->user()->id,
            'replied_at' => now(),
        ]);

        return redirect()->route('admin.feedback.show', $feedback)
            ->with('success', 'Administrative reply recorded successfully.')
            ->with('status', 'Administrative reply recorded successfully.');
    }

    /**
     * Update feedback status (e.g. archived).
     */
    public function updateStatus(Request $request, Feedback $feedback): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:new,read,replied,archived'],
        ]);

        $feedback->update([
            'status' => $request->input('status'),
        ]);

        return redirect()->route('admin.feedback.show', $feedback)
            ->with('success', 'Feedback status updated to ' . $request->input('status') . '.')
            ->with('status', 'Feedback status updated to ' . $request->input('status') . '.');
    }
}
