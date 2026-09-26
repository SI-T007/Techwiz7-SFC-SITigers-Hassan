<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Services\Chatbot\ChatbotProviderInterface::class,
            \App\Services\Chatbot\GeminiAiProvider::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate for administrator-only operations
        Gate::define('admin-access', function (User $user) {
            return $user->isAdmin();
        });

        // Gate for managing users
        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        // Gate for editing a specific user profile
        Gate::define('edit-profile', function (User $user, int $targetUserId) {
            return $user->id === $targetUserId || $user->isAdmin();
        });

        // Unique Animated Pagination Component Across the Entire App
        Paginator::defaultView('vendor.pagination.fanhub');
        Paginator::defaultSimpleView('vendor.pagination.fanhub');
    }
}
