<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates: chatbot_knowledge (FAQ/knowledge base for optional AI chatbot)
     * Creates: chatbot_queries (log of user queries for admin review)
     * Isolated module — can be enabled/disabled independently.
     */
    public function up(): void
    {
        // Knowledge base managed by admins
        Schema::create('chatbot_knowledge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->text('question');
            $table->text('answer');
            $table->json('keywords')->nullable(); // For matching/search
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('helpful_count')->default(0); // Thumbs up count
            $table->timestamps();

            $table->index('category_id');
            $table->index('is_active');
        });

        // Log of user chatbot interactions
        Schema::create('chatbot_queries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('query');                          // What the user asked
            $table->text('response')->nullable();           // What was returned
            $table->foreignId('knowledge_id')
                  ->nullable()
                  ->constrained('chatbot_knowledge')
                  ->nullOnDelete(); // Which KB entry matched
            $table->boolean('was_helpful')->nullable();     // User feedback on response
            $table->string('session_id', 255)->nullable(); // For guest sessions
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('knowledge_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_queries');
        Schema::dropIfExists('chatbot_knowledge');
    }
};
