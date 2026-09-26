<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates: ratings (5-star rating system — polymorphic, prevents duplicates)
     */
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Polymorphic — can rate Content, Media, Character, etc.
            $table->morphs('ratable'); // Creates ratable_id + ratable_type + index

            $table->unsignedTinyInteger('score'); // 1–5 stars
            $table->timestamps();

            // A user can only rate the same item once (prevent duplicates)
            $table->unique(['user_id', 'ratable_id', 'ratable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
