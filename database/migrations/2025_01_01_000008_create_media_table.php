<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates: media (standalone media assets linked optionally to content)
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('content_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // uploader
            $table->string('title', 500);
            $table->enum('type', ['image', 'video', 'audio', 'trailer'])->default('image');
            $table->string('file_path', 500)->nullable();    // For local uploads
            $table->string('external_url', 1000)->nullable(); // For YouTube/Vimeo embeds
            $table->string('thumbnail', 500)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();

            $table->index('category_id');
            $table->index('content_id');
            $table->index('type');
            $table->index('status');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
