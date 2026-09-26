<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates: contents (central content hub for articles, videos, images, trailers, audio)
     */
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // creator/admin
            $table->string('title', 500);
            $table->string('slug', 500)->unique();
            $table->enum('type', ['article', 'video', 'audio', 'image', 'trailer'])->default('article');
            $table->text('description')->nullable();
            $table->longText('body')->nullable();            // Rich text body for articles
            $table->string('thumbnail', 500)->nullable();   // Local image path
            $table->string('media_url', 1000)->nullable();  // External embed URL (YouTube, etc.)
            $table->date('release_date')->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('popularity')->default(0); // Cached aggregate score
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->softDeletes(); // Soft delete so content can be archived, not destroyed
            $table->timestamps();

            $table->index('category_id');
            $table->index('type');
            $table->index('status');
            $table->index('is_featured');
            $table->index('published_at');
            $table->index('popularity');
            $table->index('release_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
