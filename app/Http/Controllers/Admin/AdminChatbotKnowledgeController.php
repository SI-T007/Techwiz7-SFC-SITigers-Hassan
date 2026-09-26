<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChatbotKnowledgeBase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminChatbotKnowledgeController extends Controller
{
    /**
     * List all knowledge base entries with search & filters.
     */
    public function index(Request $request): View
    {
        $query = ChatbotKnowledgeBase::with('category');

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $entries = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();

        $metrics = [
            'total' => ChatbotKnowledgeBase::count(),
            'active' => ChatbotKnowledgeBase::where('is_active', true)->count(),
            'inactive' => ChatbotKnowledgeBase::where('is_active', false)->count(),
        ];

        return view('admin.chatbot.index', compact('entries', 'categories', 'metrics'));
    }

    /**
     * Show form to create new knowledge base entry.
     */
    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.chatbot.create', compact('categories'));
    }

    /**
     * Store a new knowledge base entry.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:5000'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $keywordsArray = [];
        if (!empty($validated['keywords'])) {
            $keywordsArray = array_map('trim', explode(',', $validated['keywords']));
            $keywordsArray = array_filter($keywordsArray);
        }

        ChatbotKnowledgeBase::create([
            'category_id' => $validated['category_id'] ?? null,
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'keywords' => $keywordsArray,
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
        ]);

        return redirect()->route('admin.chatbot.knowledge.index')->with('success', 'Knowledge entry created successfully.');
    }

    /**
     * Show edit form for knowledge base entry.
     */
    public function edit(ChatbotKnowledgeBase $knowledge): View
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.chatbot.edit', compact('knowledge', 'categories'));
    }

    /**
     * Update knowledge base entry.
     */
    public function update(Request $request, ChatbotKnowledgeBase $knowledge): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:5000'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $keywordsArray = [];
        if (!empty($validated['keywords'])) {
            $keywordsArray = array_map('trim', explode(',', $validated['keywords']));
            $keywordsArray = array_filter($keywordsArray);
        }

        $knowledge->update([
            'category_id' => $validated['category_id'] ?? null,
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'keywords' => $keywordsArray,
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : false,
        ]);

        return redirect()->route('admin.chatbot.knowledge.index')->with('success', 'Knowledge entry updated successfully.');
    }

    /**
     * Toggle status (active/inactive).
     */
    public function toggleStatus(ChatbotKnowledgeBase $knowledge): RedirectResponse
    {
        $knowledge->update(['is_active' => ! $knowledge->is_active]);
        return back()->with('success', 'Knowledge entry status updated.');
    }

    /**
     * Delete knowledge base entry.
     */
    public function destroy(ChatbotKnowledgeBase $knowledge): RedirectResponse
    {
        $knowledge->delete();
        return redirect()->route('admin.chatbot.knowledge.index')->with('success', 'Knowledge entry deleted successfully.');
    }
}
