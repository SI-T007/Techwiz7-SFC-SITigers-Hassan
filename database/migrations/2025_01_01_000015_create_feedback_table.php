<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates: feedback (user feedback — bugs, suggestions, queries)
     */
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Null = guest
            $table->string('name', 255)->nullable();    // For guests who are not logged in
            $table->string('email', 255)->nullable();   // For guests
            $table->enum('type', ['bug', 'suggestion', 'query', 'other'])->default('query');
            $table->string('subject', 500);
            $table->text('message');

            // Admin management
            $table->enum('status', ['new', 'read', 'replied', 'archived'])->default('new');
            $table->text('admin_reply')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
