<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates: releases (upcoming releases: anime, games, movies, TV, comics, manga)
     */
    public function up(): void
    {
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 500);
            $table->string('slug', 500)->unique();
            $table->enum('type', ['anime', 'game', 'movie', 'tv_series', 'comic', 'manga', 'album', 'merchandise', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->string('cover_image', 500)->nullable();
            $table->date('release_date');
            $table->string('platform', 255)->nullable();          // For games: "PC, PS5, Xbox"
            $table->string('developer_publisher', 255)->nullable();
            $table->string('external_url', 1000)->nullable();
            $table->enum('status', ['announced', 'upcoming', 'released', 'cancelled'])->default('upcoming');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('category_id');
            $table->index('type');
            $table->index('status');
            $table->index('release_date');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
