<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Content;
use App\Models\Media;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MultimediaTest extends TestCase
{
    protected User $user;
    protected User $secondUser;
    protected User $admin;
    protected Category $category;
    protected Content $content;
    protected Media $publishedMedia;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::firstOrCreate(
            ['slug' => 'gaming'],
            [
                'name' => 'Gaming',
                'description' => 'Gaming fandom universe',
                'icon' => 'bi-controller',
                'is_active' => true,
            ]
        );

        $this->user = User::firstOrCreate(
            ['email' => 'media_visitor@fanhub.test'],
            [
                'name' => 'Media Visitor',
                'password' => Hash::make('Password@123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->secondUser = User::firstOrCreate(
            ['email' => 'media_reviewer@fanhub.test'],
            [
                'name' => 'Media Reviewer',
                'password' => Hash::make('Password@123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'media_admin@fanhub.test'],
            [
                'name' => 'Media Admin',
                'password' => Hash::make('Password@123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->content = Content::firstOrCreate(
            ['slug' => 'elden-ring-shadow-of-erdtree'],
            [
                'category_id' => $this->category->id,
                'user_id' => $this->admin->id,
                'title' => 'Elden Ring: Shadow of the Erdtree Lore',
                'type' => 'article',
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        $this->publishedMedia = Media::updateOrCreate(
            ['youtube_video_id' => 'E3Huy2cdIH0'],
            [
                'content_id' => $this->content->id,
                'category_id' => $this->category->id,
                'user_id' => $this->admin->id,
                'title' => 'Elden Ring Official Cinematic Trailer',
                'type' => 'trailer',
                'media_type' => 'gaming_cinematic',
                'quality' => '4K',
                'duration' => '02:59',
                'language' => 'English',
                'release_date' => '2021-06-10',
                'tags' => ['Elden Ring', 'Cinematic', 'FromSoftware'],
                'description' => 'Official high definition gameplay showcase and story trailer.',
                'external_url' => 'https://www.youtube.com/watch?v=E3Huy2cdIH0',
                'status' => 'published',
                'is_featured' => true,
            ]
        );
    }

    public function test_guest_can_view_public_multimedia_hub(): void
    {
        $response = $this->get(route('multimedia.index'));
        $response->assertOk();
        $response->assertSee('Multimedia Center');
        $response->assertSee('Elden Ring Official Cinematic Trailer');
    }

    public function test_youtube_video_id_extraction_handles_various_url_formats(): void
    {
        $standardUrl = 'https://www.youtube.com/watch?v=M_OauHnAFc8';
        $shortUrl = 'https://youtu.be/M_OauHnAFc8';
        $embedUrl = 'https://www.youtube.com/embed/M_OauHnAFc8';
        $shortsUrl = 'https://www.youtube.com/shorts/M_OauHnAFc8';
        $withParamsUrl = 'https://www.youtube.com/watch?v=M_OauHnAFc8&t=45s&feature=shared';

        $this->assertEquals('M_OauHnAFc8', Media::extractYouTubeId($standardUrl));
        $this->assertEquals('M_OauHnAFc8', Media::extractYouTubeId($shortUrl));
        $this->assertEquals('M_OauHnAFc8', Media::extractYouTubeId($embedUrl));
        $this->assertEquals('M_OauHnAFc8', Media::extractYouTubeId($shortsUrl));
        $this->assertEquals('M_OauHnAFc8', Media::extractYouTubeId($withParamsUrl));
        $this->assertNull(Media::extractYouTubeId('https://vimeo.com/123456'));
        $this->assertNull(Media::extractYouTubeId('javascript:alert(1)'));
    }

    public function test_youtube_embed_url_is_safely_generated(): void
    {
        $this->assertEquals(
            'https://www.youtube.com/embed/E3Huy2cdIH0?rel=0&modestbranding=1',
            $this->publishedMedia->youtube_embed_url
        );
    }

    public function test_youtube_thumbnail_url_returns_correct_pattern(): void
    {
        $this->assertEquals(
            'https://img.youtube.com/vi/E3Huy2cdIH0/maxresdefault.jpg',
            $this->publishedMedia->youtube_thumbnail_url
        );
    }

    public function test_media_type_and_category_filtering_works(): void
    {
        // Filter by media_type
        $responseCinematic = $this->get(route('multimedia.index', ['media_type' => 'gaming_cinematic']));
        $responseCinematic->assertOk();
        $responseCinematic->assertSee('Elden Ring Official Cinematic Trailer');

        // Filter by anime song (should not contain this gaming cinematic)
        $responseSong = $this->get(route('multimedia.index', ['media_type' => 'anime_song']));
        $responseSong->assertOk();
        $responseSong->assertDontSee('Elden Ring Official Cinematic Trailer');

        // Filter by fandom category
        $responseCategory = $this->get(route('multimedia.index', ['fandom' => 'gaming']));
        $responseCategory->assertOk();
        $responseCategory->assertSee('Elden Ring Official Cinematic Trailer');
    }

    public function test_quality_and_search_filtering(): void
    {
        // Filter by 4K quality
        $responseQuality = $this->get(route('multimedia.index', ['quality' => '4K']));
        $responseQuality->assertOk();
        $responseQuality->assertSee('Elden Ring Official Cinematic Trailer');

        // Filter by search query
        $responseSearch = $this->get(route('multimedia.index', ['search' => 'Elden Ring']));
        $responseSearch->assertOk();
        $responseSearch->assertSee('Elden Ring Official Cinematic Trailer');

        // Filter by non-matching search
        $responseNonMatching = $this->get(route('multimedia.index', ['search' => 'NonExistentVideoQueryXYZ']));
        $responseNonMatching->assertOk();
        $responseNonMatching->assertDontSee('Elden Ring Official Cinematic Trailer');
    }

    public function test_guest_can_view_media_detail_page_with_youtube_embed(): void
    {
        $response = $this->get(route('multimedia.show', $this->publishedMedia));
        $response->assertOk();
        $response->assertSee('Elden Ring Official Cinematic Trailer');
        $response->assertSee('Gaming');
        $response->assertSee('https://www.youtube.com/embed/E3Huy2cdIH0?rel=0&amp;modestbranding=1', false);
        $response->assertSee('4K');
    }

    public function test_authenticated_user_can_rate_media_and_update_existing_rating(): void
    {
        $media = Media::create([
            'title' => 'Fresh Test Media for Rating',
            'category_id' => $this->category->id,
            'user_id' => $this->admin->id,
            'media_type' => 'gaming_trailer',
            'quality' => 'HD',
            'youtube_video_id' => 'abcTest1234',
            'status' => 'published',
        ]);

        // 1. Initial 5-star rating by user
        $response1 = $this->actingAs($this->user)->postJson('/ratings', [
            'ratable_type' => 'media',
            'ratable_id' => $media->id,
            'score' => 5,
        ]);

        $response1->assertOk();
        $response1->assertJson([
            'success' => true,
            'user_score' => 5,
        ]);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $this->user->id,
            'ratable_type' => Media::class,
            'ratable_id' => $media->id,
            'score' => 5,
        ]);

        // 2. User updates rating to 4 stars (should update record, not duplicate)
        $response2 = $this->actingAs($this->user)->postJson('/ratings', [
            'ratable_type' => 'media',
            'ratable_id' => $media->id,
            'score' => 4,
        ]);

        $response2->assertOk();
        $response2->assertJson([
            'success' => true,
            'user_score' => 4,
        ]);

        // Check only 1 rating exists for this user/media
        $userRatingsCount = Rating::where('user_id', $this->user->id)
            ->where('ratable_type', Media::class)
            ->where('ratable_id', $media->id)
            ->count();

        $this->assertEquals(1, $userRatingsCount);

        // 3. Second user rates 5 stars
        $this->actingAs($this->secondUser)->postJson('/ratings', [
            'ratable_type' => 'media',
            'ratable_id' => $media->id,
            'score' => 5,
        ]);

        // Average should be (4 + 5) / 2 = 4.5
        $media->refresh();
        $this->assertEquals(4.5, $media->ratings_avg);
        $this->assertEquals(2, $media->ratings_count);

        $media->delete();
    }

    public function test_draft_media_cannot_be_viewed_publicly(): void
    {
        $draftMedia = Media::create([
            'category_id' => $this->category->id,
            'title' => 'Secret Unreleased Cutscene',
            'type' => 'video',
            'media_type' => 'gaming_cinematic',
            'description' => 'Top secret upcoming DLC scene.',
            'status' => 'draft',
        ]);

        $response = $this->get(route('multimedia.show', $draftMedia));
        $response->assertNotFound();

        $draftMedia->delete();
    }

    public function test_regular_user_cannot_access_admin_media_cms(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.media.index'));
        $response->assertForbidden();

        $responseCreate = $this->actingAs($this->user)->get(route('admin.media.create'));
        $responseCreate->assertForbidden();
    }

    public function test_admin_can_access_media_cms_and_create_youtube_media(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.media.index'));
        $response->assertOk();
        $response->assertSee('Manage Multimedia Assets');

        $responseStore = $this->actingAs($this->admin)->post(route('admin.media.store'), [
            'title' => 'Chainsaw Man — Main Official Trailer',
            'category_id' => $this->category->id,
            'media_type' => 'anime_trailer',
            'quality' => '4K',
            'duration' => '01:45',
            'language' => 'Japanese',
            'release_date' => '2022-09-19',
            'tags' => ['Chainsaw Man', 'Denji', 'Makima', 'MAPPA'],
            'description' => 'Official cinematic trailer for Chainsaw Man produced by MAPPA.',
            'external_url' => 'https://www.youtube.com/watch?v=v4yLeNt-kCU',
            'status' => 'published',
            'is_featured' => 1,
        ]);

        $responseStore->assertRedirect(route('admin.media.index'));
        $responseStore->assertSessionHas('success');

        $this->assertDatabaseHas('media', [
            'title' => 'Chainsaw Man — Main Official Trailer',
            'youtube_video_id' => 'v4yLeNt-kCU',
            'media_type' => 'anime_trailer',
            'quality' => '4K',
            'status' => 'published',
            'is_featured' => 1,
        ]);
    }

    public function test_admin_can_update_media(): void
    {
        $media = Media::create([
            'title' => 'Beta Footage Reel',
            'category_id' => $this->category->id,
            'type' => 'video',
            'media_type' => 'gaming_gameplay',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.media.update', $media), [
            'title' => 'Final Launch Trailer — 4K Edition',
            'category_id' => $this->category->id,
            'media_type' => 'gaming_trailer',
            'quality' => '4K',
            'status' => 'published',
            'external_url' => 'https://www.youtube.com/watch?v=QdBZY2fkU-0',
        ]);

        $response->assertRedirect(route('admin.media.index'));
        $response->assertSessionHas('success');

        $media->refresh();
        $this->assertEquals('Final Launch Trailer — 4K Edition', $media->title);
        $this->assertEquals('QdBZY2fkU-0', $media->youtube_video_id);
        $this->assertEquals('gaming_trailer', $media->media_type);
        $this->assertEquals('4K', $media->quality);
        $this->assertEquals('published', $media->status);

        $media->delete();
    }

    public function test_admin_can_delete_media(): void
    {
        $media = Media::create([
            'title' => 'Media to Delete',
            'category_id' => $this->category->id,
            'type' => 'image',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.media.destroy', $media));
        $response->assertRedirect(route('admin.media.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('media', [
            'id' => $media->id,
        ]);
    }
}
