<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates: bookmarks (user-saved content with optional personal note)
     * Polymorphic — can bookmark Content, Character, Event, Merchandise, Release
     */
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Polymorphic: content, characters, events, merchandise, releases
            $table->morphs('bookmarkable'); // Creates bookmarkable_id + bookmarkable_type + index

            $table->text('note')->nullable(); // Personal note on the bookmark
            $table->timestamps();

            // Prevent duplicate bookmarks for same user + same item
            $table->unique(['user_id', 'bookmarkable_id', 'bookmarkable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
