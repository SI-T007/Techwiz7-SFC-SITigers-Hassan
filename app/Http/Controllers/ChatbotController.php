<?php

namespace App\Http\Controllers;

use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use App\Services\Chatbot\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    public function __construct(
        protected ChatbotService $chatbotService
    ) {}

    /**
     * Show Chatbot Interface.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $conversation = null;

        if ($request->filled('c')) {
            $convId = $request->input('c');
            $conversation = ChatbotConversation::where('id', $convId)
                ->when($user, fn($q) => $q->where('user_id', $user->id))
                ->when(!$user, fn($q) => $q->where('session_id', session()->getId()))
                ->with('messages')
                ->first();
        }

        if (! $conversation) {
            $conversation = $this->chatbotService->getOrCreateConversation($user, session()->getId());
            $conversation->load('messages');
        }

        $suggestedQuestions = [
            'What fandoms are available?',
            'What upcoming events are on the calendar?',
            'What releases are coming soon on the radar?',
            'How do I bookmark lore and characters?',
            'How can I submit original fan creations?',
            'Tell me about the available character profiles',
            'How do I toggle Dark and Light mode?',
        ];

        $recentConversations = $user
            ? ChatbotConversation::where('user_id', $user->id)
                ->latest('updated_at')
                ->take(5)
                ->get()
            : collect();

        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'AI Fan Assistant', 'url' => ''],
        ];

        return view('pages.chatbot.index', compact(
            'conversation',
            'suggestedQuestions',
            'recentConversations',
            'breadcrumbs'
        ));
    }

    /**
     * Send a message in a conversation (AJAX Endpoint).
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'conversation_id' => ['nullable', 'integer'],
        ]);

        $user = $request->user();
        $conversation = null;

        if ($request->filled('conversation_id')) {
            $conversation = ChatbotConversation::find($request->input('conversation_id'));
            if ($conversation) {
                // Check ownership
                if ($user && $conversation->user_id && $conversation->user_id !== $user->id) {
                    return response()->json(['error' => 'Unauthorized conversation access.'], 403);
                }
                if (!$user && $conversation->session_id && $conversation->session_id !== session()->getId()) {
                    return response()->json(['error' => 'Unauthorized session access.'], 403);
                }
            }
        }

        if (! $conversation) {
            $conversation = $this->chatbotService->getOrCreateConversation($user, session()->getId());
        }

        $assistantMessage = $this->chatbotService->handleUserMessage(
            $conversation,
            $request->input('message')
        );

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'title' => $conversation->title,
            'message' => [
                'id' => $assistantMessage->id,
                'sender' => 'assistant',
                'message' => $assistantMessage->message,
                'source' => $assistantMessage->source,
                'created_at' => $assistantMessage->created_at->format('H:i'),
            ],
        ]);
    }

    /**
     * View conversation history for authenticated users.
     */
    public function history(Request $request): View
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login')->with('info', 'Please sign in to view your chat conversation history.');
        }

        $conversations = ChatbotConversation::where('user_id', $user->id)
            ->withCount('messages')
            ->with('latestMessage')
            ->latest('updated_at')
            ->paginate(12);

        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'AI Fan Assistant', 'url' => route('chatbot.index')],
            ['label' => 'Conversation History', 'url' => ''],
        ];

        return view('pages.chatbot.history', compact('conversations', 'breadcrumbs'));
    }

    /**
     * Start a fresh conversation.
     */
    public function newConversation(Request $request): RedirectResponse
    {
        $user = $request->user();
        $conversation = ChatbotConversation::create([
            'user_id' => $user?->id,
            'session_id' => $user ? null : session()->getId(),
            'title' => 'New Conversation',
        ]);

        return redirect()->route('chatbot.index', ['c' => $conversation->id]);
    }

    /**
     * Delete a conversation.
     */
    public function destroy(Request $request, ChatbotConversation $conversation): RedirectResponse
    {
        $user = $request->user();
        if ($user && $conversation->user_id !== $user->id) {
            abort(403, 'Unauthorized conversation deletion.');
        }

        if (!$user && $conversation->session_id !== session()->getId()) {
            abort(403, 'Unauthorized conversation deletion.');
        }

        $conversation->delete();

        return back()->with('success', 'Conversation was deleted successfully.');
    }
}
