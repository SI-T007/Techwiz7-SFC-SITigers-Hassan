<?php

namespace Tests\Feature;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Content;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BookmarkAndRatingTest extends TestCase
{
    protected User $user;
    protected User $otherUser;
    protected Category $category;
    protected Content $content;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::firstOrCreate(
            ['slug' => 'anime'],
            [
                'name' => 'Anime',
                'description' => 'Anime universe content',
                'icon' => 'bi-tv',
                'is_active' => true,
            ]
        );

        $this->user = User::firstOrCreate(
            ['email' => 'bm_test_user@fanhub.test'],
            [
                'name' => 'BM Test User',
                'password' => Hash::make('Password@123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->otherUser = User::firstOrCreate(
            ['email' => 'bm_other_user@fanhub.test'],
            [
                'name' => 'BM Other User',
                'password' => Hash::make('Password@123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->content = Content::firstOrCreate(
            ['slug' => 'neon-genesis-evangelion-canon-lore'],
            [
                'category_id' => $this->category->id,
                'user_id' => $this->user->id,
                'title' => 'Neon Genesis Evangelion Canon Lore',
                'type' => 'article',
                'description' => 'Complete exploration of EVA units and angels.',
                'body' => 'In 2015, the angels descended upon Tokyo-3.',
                'status' => 'published',
                'views' => 120,
                'popularity' => 50,
                'published_at' => now(),
            ]
        );

        // Clear existing test bookmarks and ratings for this content
        Bookmark::where('bookmarkable_type', Content::class)
            ->where('bookmarkable_id', $this->content->id)
            ->delete();

        Rating::where('ratable_type', Content::class)
            ->where('ratable_id', $this->content->id)
            ->delete();
    }

    public function test_guest_cannot_toggle_bookmark(): void
    {
        $response = $this->postJson(route('bookmarks.toggle'), [
            'bookmarkable_type' => 'content',
            'bookmarkable_id' => $this->content->id,
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('bookmarks', [
            'bookmarkable_id' => $this->content->id,
            'bookmarkable_type' => Content::class,
        ]);
    }

    public function test_authenticated_user_can_bookmark_content(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('bookmarks.toggle'), [
            'bookmarkable_type' => 'content',
            'bookmarkable_id' => $this->content->id,
            'note' => 'Must re-read section 3.',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'is_bookmarked' => true,
            ]);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $this->user->id,
            'bookmarkable_type' => Content::class,
            'bookmarkable_id' => $this->content->id,
            'note' => 'Must re-read section 3.',
        ]);
    }

    public function test_authenticated_user_can_toggle_remove_bookmark(): void
    {
        Bookmark::create([
            'user_id' => $this->user->id,
            'bookmarkable_type' => Content::class,
            'bookmarkable_id' => $this->content->id,
            'note' => 'Initial note',
        ]);

        $response = $this->actingAs($this->user)->postJson(route('bookmarks.toggle'), [
            'bookmarkable_type' => 'content',
            'bookmarkable_id' => $this->content->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'is_bookmarked' => false,
            ]);

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $this->user->id,
            'bookmarkable_type' => Content::class,
            'bookmarkable_id' => $this->content->id,
        ]);
    }

    public function test_authenticated_user_can_view_own_bookmarks_page(): void
    {
        Bookmark::create([
            'user_id' => $this->user->id,
            'bookmarkable_type' => Content::class,
            'bookmarkable_id' => $this->content->id,
            'note' => 'A unique reading note on EVA',
        ]);

        $response = $this->actingAs($this->user)->get(route('bookmarks.index'));

        $response->assertOk()
            ->assertSee('My Saved Bookmarks')
            ->assertSee('Neon Genesis Evangelion Canon Lore')
            ->assertSee('A unique reading note on EVA');
    }

    public function test_user_cannot_modify_or_delete_another_users_bookmark(): void
    {
        $bookmark = Bookmark::create([
            'user_id' => $this->otherUser->id,
            'bookmarkable_type' => Content::class,
            'bookmarkable_id' => $this->content->id,
            'note' => 'Other user secret note',
        ]);

        // Attempt note update
        $updateResponse = $this->actingAs($this->user)->postJson(route('bookmarks.note', $bookmark->id), [
            'note' => 'Malicious update attempt',
        ]);
        $updateResponse->assertForbidden();

        // Attempt delete
        $deleteResponse = $this->actingAs($this->user)->deleteJson(route('bookmarks.destroy', $bookmark->id));
        $deleteResponse->assertForbidden();

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'note' => 'Other user secret note',
        ]);
    }

    public function test_user_can_manage_personal_bookmark_notes(): void
    {
        $bookmark = Bookmark::create([
            'user_id' => $this->user->id,
            'bookmarkable_type' => Content::class,
            'bookmarkable_id' => $this->content->id,
            'note' => 'Original note',
        ]);

        $response = $this->actingAs($this->user)->postJson(route('bookmarks.note', $bookmark->id), [
            'note' => 'Updated lore analysis note with new conclusions',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'note' => 'Updated lore analysis note with new conclusions',
            ]);

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'note' => 'Updated lore analysis note with new conclusions',
        ]);
    }

    public function test_guest_cannot_submit_rating(): void
    {
        $response = $this->postJson(route('ratings.store'), [
            'ratable_type' => 'content',
            'ratable_id' => $this->content->id,
            'score' => 5,
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('ratings', [
            'ratable_type' => Content::class,
            'ratable_id' => $this->content->id,
        ]);
    }

    public function test_authenticated_user_can_submit_5_star_rating(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('ratings.store'), [
            'ratable_type' => 'content',
            'ratable_id' => $this->content->id,
            'score' => 5,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'user_score' => 5,
                'average_score' => 5.0,
                'ratings_count' => 1,
            ]);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $this->user->id,
            'ratable_type' => Content::class,
            'ratable_id' => $this->content->id,
            'score' => 5,
        ]);
    }

    public function test_invalid_rating_score_is_rejected(): void
    {
        // Score too high (6)
        $responseHigh = $this->actingAs($this->user)->postJson(route('ratings.store'), [
            'ratable_type' => 'content',
            'ratable_id' => $this->content->id,
            'score' => 6,
        ]);
        $responseHigh->assertStatus(422)
            ->assertJsonValidationErrors(['score']);

        // Score too low (0)
        $responseLow = $this->actingAs($this->user)->postJson(route('ratings.store'), [
            'ratable_type' => 'content',
            'ratable_id' => $this->content->id,
            'score' => 0,
        ]);
        $responseLow->assertStatus(422)
            ->assertJsonValidationErrors(['score']);
    }

    public function test_user_can_update_rating_and_prevent_duplicate_records(): void
    {
        // First rating: 4 stars
        $this->actingAs($this->user)->postJson(route('ratings.store'), [
            'ratable_type' => 'content',
            'ratable_id' => $this->content->id,
            'score' => 4,
        ]);

        // Second rating by same user: updated to 5 stars
        $response = $this->actingAs($this->user)->postJson(route('ratings.store'), [
            'ratable_type' => 'content',
            'ratable_id' => $this->content->id,
            'score' => 5,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'user_score' => 5,
                'ratings_count' => 1,
            ]);

        // Assert only ONE rating row exists for this user + content
        $this->assertEquals(1, Rating::where('user_id', $this->user->id)
            ->where('ratable_id', $this->content->id)
            ->count());

        $this->assertDatabaseHas('ratings', [
            'user_id' => $this->user->id,
            'score' => 5,
        ]);
    }

    public function test_aggregate_rating_is_calculated_accurately(): void
    {
        // User 1 rates 4
        $this->actingAs($this->user)->postJson(route('ratings.store'), [
            'ratable_type' => 'content',
            'ratable_id' => $this->content->id,
            'score' => 4,
        ]);

        // User 2 rates 5
        $response = $this->actingAs($this->otherUser)->postJson(route('ratings.store'), [
            'ratable_type' => 'content',
            'ratable_id' => $this->content->id,
            'score' => 5,
        ]);

        // Average of 4 and 5 is 4.5
        $response->assertOk()
            ->assertJson([
                'success' => true,
                'average_score' => 4.5,
                'ratings_count' => 2,
            ]);
    }

    public function test_user_can_remove_their_rating(): void
    {
        Rating::create([
            'user_id' => $this->user->id,
            'ratable_type' => Content::class,
            'ratable_id' => $this->content->id,
            'score' => 5,
        ]);

        $response = $this->actingAs($this->user)->deleteJson(route('ratings.destroy'), [
            'ratable_type' => 'content',
            'ratable_id' => $this->content->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'user_score' => null,
                'ratings_count' => 0,
            ]);

        $this->assertDatabaseMissing('ratings', [
            'user_id' => $this->user->id,
            'ratable_id' => $this->content->id,
        ]);
    }
}
