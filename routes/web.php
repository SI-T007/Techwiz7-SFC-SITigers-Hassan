<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\WatchHistoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Fan Hub Plus
|--------------------------------------------------------------------------
*/

// Public Website Shell & Discovery
Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/anime', [PublicPageController::class, 'anime'])->name('anime.index');
Route::get('/explore', [PublicPageController::class, 'explore'])->name('explore');
Route::get('/explore/{slug}', [PublicPageController::class, 'showContent'])->name('explore.show');
Route::get('/fandoms', [PublicPageController::class, 'fandoms'])->name('fandoms.index');
Route::get('/fandoms/{slug}', [PublicPageController::class, 'showFandom'])->name('fandoms.show');
Route::get('/characters', [PublicPageController::class, 'characters'])->name('characters.index');
Route::get('/characters/{slug}', [PublicPageController::class, 'showCharacter'])->name('characters.show');
Route::get('/releases', [PublicPageController::class, 'releases'])->name('releases.index');
Route::get('/releases/{slug}', [PublicPageController::class, 'showRelease'])->name('releases.show');
Route::get('/events', [PublicPageController::class, 'events'])->name('events.index');
Route::get('/events/calendar', [PublicPageController::class, 'eventCalendar'])->name('events.calendar');
Route::get('/events/nearby-data', [PublicPageController::class, 'eventsData'])->name('events.data');
Route::get('/events/{slug}', [PublicPageController::class, 'showEvent'])->name('events.show');
Route::get('/events/{event}/ics', [PublicPageController::class, 'eventIcs'])->name('events.ics');
Route::get('/community', [PublicPageController::class, 'community'])->name('community.index');
Route::get('/community/submissions/{submission}', [PublicPageController::class, 'showSubmission'])->name('community.submissions.show');
Route::get('/multimedia', [PublicPageController::class, 'multimedia'])->name('multimedia.index');
Route::get('/multimedia/{media}', [PublicPageController::class, 'showMedia'])->name('multimedia.show');
Route::get('/about', [PublicPageController::class, 'about'])->name('about');

// Smart AI Chatbot & Fan Assistant
Route::get('/chatbot', [\App\Http\Controllers\ChatbotController::class, 'index'])->name('chatbot.index');
Route::post('/chatbot/message', [\App\Http\Controllers\ChatbotController::class, 'sendMessage'])->name('chatbot.message')->middleware('throttle:30,1');
Route::get('/chatbot/history', [\App\Http\Controllers\ChatbotController::class, 'history'])->name('chatbot.history');
Route::post('/chatbot/new', [\App\Http\Controllers\ChatbotController::class, 'newConversation'])->name('chatbot.new');
Route::delete('/chatbot/conversations/{conversation}', [\App\Http\Controllers\ChatbotController::class, 'destroy'])->name('chatbot.destroy');

// User Feedback (Public form submission)
Route::get('/feedback', [\App\Http\Controllers\FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');

// Guest Only Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated Routes (Requires active session)
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Email Verification Routes
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // User Portal & Profile Management
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');

    // Watch History
    Route::get('/watch-history', [WatchHistoryController::class, 'index'])->name('watch-history.index');
    Route::delete('/watch-history/clear', [WatchHistoryController::class, 'clearAll'])->name('watch-history.clear');
    Route::delete('/watch-history/{watchHistory}', [WatchHistoryController::class, 'destroy'])->name('watch-history.destroy');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/fandoms', [ProfileController::class, 'updateFandoms'])->name('profile.fandoms');
    Route::post('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Fandom Bookmarks & Personal Notes
    Route::get('/bookmarks', [\App\Http\Controllers\User\BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks/toggle', [\App\Http\Controllers\User\BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::post('/bookmarks/{bookmark}/note', [\App\Http\Controllers\User\BookmarkController::class, 'updateNote'])->name('bookmarks.note');
    Route::delete('/bookmarks/{bookmark}', [\App\Http\Controllers\User\BookmarkController::class, 'destroy'])->name('bookmarks.destroy');

    // 5-Star Ratings Engine
    Route::post('/ratings', [\App\Http\Controllers\User\RatingController::class, 'store'])->name('ratings.store');
    Route::delete('/ratings', [\App\Http\Controllers\User\RatingController::class, 'destroy'])->name('ratings.destroy');
    Route::delete('/ratings/{rating}', [\App\Http\Controllers\User\RatingController::class, 'destroyRating'])->name('ratings.destroy-id');

    // Fan Content Submissions
    Route::get('/community/submit', [\App\Http\Controllers\User\FanSubmissionController::class, 'create'])->name('community.submit');
    Route::post('/community/submit', [\App\Http\Controllers\User\FanSubmissionController::class, 'store'])->name('community.submit.store');
    Route::get('/community/my-submissions', [\App\Http\Controllers\User\FanSubmissionController::class, 'mySubmissions'])->name('community.my-submissions');
    Route::delete('/community/my-submissions/{submission}', [\App\Http\Controllers\User\FanSubmissionController::class, 'destroy'])->name('community.submissions.destroy');
});

// Administrator Routes (Protected by auth + admin role middleware)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Admin User Management & Audit
    Route::get('/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('admin.users');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('admin.users.show');
    Route::post('/users/{user}/role', [\App\Http\Controllers\Admin\AdminUserController::class, 'updateRole'])->name('admin.users.role');
    Route::post('/users/{user}/toggle-status', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Admin Fandom Category Management
    Route::get('/categories', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::post('/categories/{category}/toggle', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'toggleStatus'])->name('admin.categories.toggle');
    Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // Admin Character Lore Management
    Route::get('/characters', [\App\Http\Controllers\Admin\AdminCharacterController::class, 'index'])->name('admin.characters.index');
    Route::get('/characters/create', [\App\Http\Controllers\Admin\AdminCharacterController::class, 'create'])->name('admin.characters.create');
    Route::post('/characters', [\App\Http\Controllers\Admin\AdminCharacterController::class, 'store'])->name('admin.characters.store');
    Route::get('/characters/{character}/edit', [\App\Http\Controllers\Admin\AdminCharacterController::class, 'edit'])->name('admin.characters.edit');
    Route::put('/characters/{character}', [\App\Http\Controllers\Admin\AdminCharacterController::class, 'update'])->name('admin.characters.update');
    Route::delete('/characters/{character}', [\App\Http\Controllers\Admin\AdminCharacterController::class, 'destroy'])->name('admin.characters.destroy');

    // Admin Lore Articles Management
    Route::get('/articles', [\App\Http\Controllers\Admin\AdminArticleController::class, 'index'])->name('admin.articles.index');
    Route::get('/articles/create', [\App\Http\Controllers\Admin\AdminArticleController::class, 'create'])->name('admin.articles.create');
    Route::post('/articles', [\App\Http\Controllers\Admin\AdminArticleController::class, 'store'])->name('admin.articles.store');
    Route::get('/articles/{article}/edit', [\App\Http\Controllers\Admin\AdminArticleController::class, 'edit'])->name('admin.articles.edit');
    Route::put('/articles/{article}', [\App\Http\Controllers\Admin\AdminArticleController::class, 'update'])->name('admin.articles.update');
    Route::post('/articles/{article}/toggle-featured', [\App\Http\Controllers\Admin\AdminArticleController::class, 'toggleFeatured'])->name('admin.articles.toggle-featured');
    Route::delete('/articles/{article}', [\App\Http\Controllers\Admin\AdminArticleController::class, 'destroy'])->name('admin.articles.destroy');

    // Admin Content Management
    Route::get('/content', [\App\Http\Controllers\Admin\AdminContentController::class, 'index'])->name('admin.content.index');
    Route::get('/content/create', [\App\Http\Controllers\Admin\AdminContentController::class, 'create'])->name('admin.content.create');
    Route::post('/content', [\App\Http\Controllers\Admin\AdminContentController::class, 'store'])->name('admin.content.store');
    Route::get('/content/{content}/edit', [\App\Http\Controllers\Admin\AdminContentController::class, 'edit'])->name('admin.content.edit');
    Route::put('/content/{content}', [\App\Http\Controllers\Admin\AdminContentController::class, 'update'])->name('admin.content.update');
    Route::delete('/content/{content}', [\App\Http\Controllers\Admin\AdminContentController::class, 'destroy'])->name('admin.content.destroy');

    // Admin Moderation Queue
    Route::get('/submissions', [\App\Http\Controllers\Admin\AdminModerationController::class, 'index'])->name('admin.submissions.index');
    Route::get('/submissions/{submission}', [\App\Http\Controllers\Admin\AdminModerationController::class, 'show'])->name('admin.submissions.show');
    Route::post('/submissions/{submission}/approve', [\App\Http\Controllers\Admin\AdminModerationController::class, 'approve'])->name('admin.submissions.approve');
    Route::post('/submissions/{submission}/reject', [\App\Http\Controllers\Admin\AdminModerationController::class, 'reject'])->name('admin.submissions.reject');

    // Admin Multimedia Management
    Route::get('/media', [\App\Http\Controllers\Admin\AdminMediaController::class, 'index'])->name('admin.media.index');
    Route::get('/media/create', [\App\Http\Controllers\Admin\AdminMediaController::class, 'create'])->name('admin.media.create');
    Route::post('/media', [\App\Http\Controllers\Admin\AdminMediaController::class, 'store'])->name('admin.media.store');
    Route::get('/media/{media}/edit', [\App\Http\Controllers\Admin\AdminMediaController::class, 'edit'])->name('admin.media.edit');
    Route::put('/media/{media}', [\App\Http\Controllers\Admin\AdminMediaController::class, 'update'])->name('admin.media.update');
    Route::delete('/media/{media}', [\App\Http\Controllers\Admin\AdminMediaController::class, 'destroy'])->name('admin.media.destroy');

    // Admin Feedback Supervision
    Route::get('/feedback', [\App\Http\Controllers\Admin\AdminFeedbackController::class, 'index'])->name('admin.feedback.index');
    Route::get('/feedback/{feedback}', [\App\Http\Controllers\Admin\AdminFeedbackController::class, 'show'])->name('admin.feedback.show');
    Route::post('/feedback/{feedback}/reply', [\App\Http\Controllers\Admin\AdminFeedbackController::class, 'reply'])->name('admin.feedback.reply');
    Route::post('/feedback/{feedback}/status', [\App\Http\Controllers\Admin\AdminFeedbackController::class, 'updateStatus'])->name('admin.feedback.status');

    // Admin Event Management
    Route::get('/events', [\App\Http\Controllers\Admin\AdminEventController::class, 'index'])->name('admin.events.index');
    Route::get('/events/create', [\App\Http\Controllers\Admin\AdminEventController::class, 'create'])->name('admin.events.create');
    Route::post('/events', [\App\Http\Controllers\Admin\AdminEventController::class, 'store'])->name('admin.events.store');
    Route::get('/events/{event}/edit', [\App\Http\Controllers\Admin\AdminEventController::class, 'edit'])->name('admin.events.edit');
    Route::put('/events/{event}', [\App\Http\Controllers\Admin\AdminEventController::class, 'update'])->name('admin.events.update');
    Route::delete('/events/{event}', [\App\Http\Controllers\Admin\AdminEventController::class, 'destroy'])->name('admin.events.destroy');

    // Admin Release Management
    Route::get('/releases', [\App\Http\Controllers\Admin\AdminReleaseController::class, 'index'])->name('admin.releases.index');
    Route::get('/releases/create', [\App\Http\Controllers\Admin\AdminReleaseController::class, 'create'])->name('admin.releases.create');
    Route::post('/releases', [\App\Http\Controllers\Admin\AdminReleaseController::class, 'store'])->name('admin.releases.store');
    Route::get('/releases/{release}/edit', [\App\Http\Controllers\Admin\AdminReleaseController::class, 'edit'])->name('admin.releases.edit');
    Route::put('/releases/{release}', [\App\Http\Controllers\Admin\AdminReleaseController::class, 'update'])->name('admin.releases.update');
    Route::delete('/releases/{release}', [\App\Http\Controllers\Admin\AdminReleaseController::class, 'destroy'])->name('admin.releases.destroy');

    // Admin Chatbot Management (Knowledge Base & Conversations)
    Route::get('/chatbot/knowledge', [\App\Http\Controllers\Admin\AdminChatbotKnowledgeController::class, 'index'])->name('admin.chatbot.knowledge.index');
    Route::get('/chatbot/knowledge/create', [\App\Http\Controllers\Admin\AdminChatbotKnowledgeController::class, 'create'])->name('admin.chatbot.knowledge.create');
    Route::post('/chatbot/knowledge', [\App\Http\Controllers\Admin\AdminChatbotKnowledgeController::class, 'store'])->name('admin.chatbot.knowledge.store');
    Route::get('/chatbot/knowledge/{knowledge}/edit', [\App\Http\Controllers\Admin\AdminChatbotKnowledgeController::class, 'edit'])->name('admin.chatbot.knowledge.edit');
    Route::put('/chatbot/knowledge/{knowledge}', [\App\Http\Controllers\Admin\AdminChatbotKnowledgeController::class, 'update'])->name('admin.chatbot.knowledge.update');
    Route::post('/chatbot/knowledge/{knowledge}/toggle', [\App\Http\Controllers\Admin\AdminChatbotKnowledgeController::class, 'toggleStatus'])->name('admin.chatbot.knowledge.toggle');
    Route::delete('/chatbot/knowledge/{knowledge}', [\App\Http\Controllers\Admin\AdminChatbotKnowledgeController::class, 'destroy'])->name('admin.chatbot.knowledge.destroy');

    Route::get('/chatbot/conversations', [\App\Http\Controllers\Admin\AdminChatbotController::class, 'conversations'])->name('admin.chatbot.conversations.index');
    Route::get('/chatbot/conversations/{conversation}', [\App\Http\Controllers\Admin\AdminChatbotController::class, 'showConversation'])->name('admin.chatbot.conversations.show');
    Route::delete('/chatbot/conversations/{conversation}', [\App\Http\Controllers\Admin\AdminChatbotController::class, 'destroyConversation'])->name('admin.chatbot.conversations.destroy');

    // Platform Analytics & Usage Telemetry
    Route::get('/analytics', [\App\Http\Controllers\Admin\AdminAnalyticsController::class, 'index'])->name('admin.analytics.index');
    Route::get('/analytics/api', [\App\Http\Controllers\Admin\AdminAnalyticsController::class, 'apiData'])->name('admin.analytics.api');
});
