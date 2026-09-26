<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates: fan_submissions (user-submitted content — requires admin approval)
     */
    public function up(): void
    {
        Schema::create('fan_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 500);
            $table->text('body')->nullable();              // Rich text content
            $table->enum('type', ['fan_art', 'fan_fiction', 'fan_video', 'cosplay', 'other'])->default('other');
            $table->string('featured_image', 500)->nullable();
            $table->string('external_url', 1000)->nullable(); // For video submissions

            // Admin review workflow
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->softDeletes(); // Soft delete — admin may need to recover submissions
            $table->timestamps();

            $table->index('user_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fan_submissions');
    }
};
