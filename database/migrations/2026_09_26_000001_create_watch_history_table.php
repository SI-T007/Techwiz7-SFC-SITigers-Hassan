<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates: watch_history — tracks every Content/Media page visit per user.
     * Polymorphic so it works for both Content and Media models.
     */
    public function up(): void
    {
        Schema::create('watch_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Polymorphic reference: App\Models\Content or App\Models\Media
            $table->string('watchable_type');
            $table->unsignedBigInteger('watchable_id');

            // Cached snapshot so history survives content deletion
            $table->string('title', 500);
            $table->string('thumbnail', 1000)->nullable();
            $table->string('type', 80)->nullable();         // 'content', 'media', 'trailer', etc.
            $table->string('url', 1000)->nullable();         // Direct link back

            $table->timestamp('watched_at')->useCurrent();

            $table->index('user_id');
            $table->index(['watchable_type', 'watchable_id']);
            $table->index('watched_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_history');
    }
};
