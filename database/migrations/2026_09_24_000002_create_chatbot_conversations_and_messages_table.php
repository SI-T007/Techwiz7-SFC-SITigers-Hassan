<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Phase 08 Chatbot Conversations and Messages.
     */
    public function up(): void
    {
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 255)->default('New Conversation');
            $table->string('session_id', 255)->nullable()->index();
            $table->timestamps();

            $table->index('user_id');
            $table->index('created_at');
        });

        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chatbot_conversations')->cascadeOnDelete();
            $table->enum('sender', ['user', 'assistant'])->default('user');
            $table->text('message');
            $table->string('source', 50)->default('knowledge_base'); // 'knowledge_base', 'database', 'ai', 'fallback'
            $table->foreignId('matched_kb_id')->nullable()->constrained('chatbot_knowledge')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('conversation_id');
            $table->index('sender');
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_messages');
        Schema::dropIfExists('chatbot_conversations');
    }
};
