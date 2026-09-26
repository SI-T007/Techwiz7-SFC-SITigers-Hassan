<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates: user_profiles (one-to-one with users)
     */
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('avatar', 500)->nullable();
            $table->string('display_name', 100)->nullable();
            $table->text('bio')->nullable();
            $table->string('location', 255)->nullable();
            $table->string('website', 500)->nullable();

            // Display & accessibility preferences (JSON blob — flexible)
            $table->json('display_preferences')->nullable();
            // e.g. {"theme":"dark","language":"en","animations":true}

            $table->json('accessibility_preferences')->nullable();
            // e.g. {"reduced_motion":false,"high_contrast":false,"font_size":"medium"}

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
