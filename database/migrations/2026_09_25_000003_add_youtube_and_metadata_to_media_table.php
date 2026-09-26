<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            if (! Schema::hasColumn('media', 'youtube_video_id')) {
                $table->string('youtube_video_id', 64)->nullable()->after('external_url');
                $table->index('youtube_video_id');
            }
            if (! Schema::hasColumn('media', 'media_type')) {
                $table->string('media_type', 64)->default('trailer')->after('type');
                $table->index('media_type');
            }
            if (! Schema::hasColumn('media', 'quality')) {
                $table->string('quality', 32)->default('HD')->after('media_type');
            }
            if (! Schema::hasColumn('media', 'duration')) {
                $table->string('duration', 32)->nullable()->after('quality');
            }
            if (! Schema::hasColumn('media', 'language')) {
                $table->string('language', 64)->nullable()->after('duration');
            }
            if (! Schema::hasColumn('media', 'release_date')) {
                $table->date('release_date')->nullable()->after('language');
            }
            if (! Schema::hasColumn('media', 'tags')) {
                $table->json('tags')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex(['youtube_video_id']);
            $table->dropIndex(['media_type']);
            $table->dropColumn([
                'youtube_video_id',
                'media_type',
                'quality',
                'duration',
                'language',
                'release_date',
                'tags',
            ]);
        });
    }
};
