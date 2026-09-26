<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates: user_activities (lightweight activity log for personalized dashboard)
     */
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Type of activity performed
            $table->enum('activity_type', [
                'viewed_content',
                'bookmarked',
                'rated',
                'submitted_feedback',
                'submitted_fan_content',
                'updated_profile',
            ]);

            // Polymorphic reference to the item the activity is about
            $table->string('subject_type')->nullable(); // Model class name
            $table->unsignedBigInteger('subject_id')->nullable();

            $table->text('description')->nullable(); // Human-readable activity description
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('activity_type');
            $table->index(['subject_type', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
