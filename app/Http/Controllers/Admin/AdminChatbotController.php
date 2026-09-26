<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use App\Models\ChatbotKnowledgeBase;
use App\Models\ChatbotMessage;
use App\Models\UserActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminChatbotController extends Controller
{
    /**
     * Display a listing of chatbot conversations.
     */
    public function conversations(Request $request): View
    {
        $query = ChatbotConversation::with(['user', 'latestMessage'])
            ->withCount('messages');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            if ($request->input('type') === 'registered') {
                $query->whereNotNull('user_id');
            } elseif ($request->input('type') === 'guest') {
                $query->whereNull('user_id');
            }
        }

        $conversations = $query->latest('updated_at')->paginate(20)->withQueryString();

        $metrics = [
            'total_conversations' => ChatbotConversation::count(),
            'total_messages' => ChatbotMessage::count(),
            'registered_conversations' => ChatbotConversation::whereNotNull('user_id')->count(),
            'guest_conversations' => ChatbotConversation::whereNull('user_id')->count(),
            'total_kb' => ChatbotKnowledgeBase::count(),
        ];

        return view('admin.chatbot.conversations', compact('conversations', 'metrics'));
    }

    /**
     * Show the detailed transcript of a conversation for moderation review.
     */
    public function showConversation(ChatbotConversation $conversation): View
    {
        $conversation->load(['user', 'messages.knowledgeBase']);

        return view('admin.chatbot.show_conversation', compact('conversation'));
    }

    /**
     * Remove the specified conversation.
     */
    public function destroyConversation(ChatbotConversation $conversation): RedirectResponse
    {
        $convTitle = $conversation->title;
        $conversation->delete();

        UserActivity::record(
            Auth::id(),
            'updated_profile',
            "Administrator deleted chatbot conversation #{$conversation->id} ('{$convTitle}')"
        );

        return redirect()->route('admin.chatbot.conversations.index')->with('success', "Conversation '{$convTitle}' has been deleted.");
    }
}
