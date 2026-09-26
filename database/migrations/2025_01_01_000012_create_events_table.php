<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates: events (conventions, screenings, cosplay meetups, livestreams)
     * Supports geolocation for future map integration.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 500);
            $table->string('slug', 500)->unique();
            $table->text('description')->nullable();
            $table->string('cover_image', 500)->nullable();
            $table->enum('event_type', ['convention', 'cosplay_meetup', 'screening', 'livestream', 'release_event', 'concert', 'other'])->default('other');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();

            // Location fields (supports future map/geolocation features)
            $table->string('venue_name', 500)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('country', 255)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->string('ticket_url', 1000)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_online')->default(false);
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->timestamps();

            $table->index('category_id');
            $table->index('event_type');
            $table->index('status');
            $table->index('starts_at');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
