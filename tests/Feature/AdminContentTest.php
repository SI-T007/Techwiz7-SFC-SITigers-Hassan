<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Content;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminContentTest extends TestCase
{
    protected User $admin;
    protected User $regularUser;
    protected Category $category;
    protected Genre $genre;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_content_test@fanhub.test'],
            [
                'name' => 'Admin Content Tester',
                'password' => Hash::make('Password@123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->regularUser = User::firstOrCreate(
            ['email' => 'regular_content_test@fanhub.test'],
            [
                'name' => 'Regular User Tester',
                'password' => Hash::make('Password@123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->category = Category::firstOrCreate(
            ['slug' => 'gaming'],
            [
                'name' => 'Gaming',
                'description' => 'Video games universe',
                'icon' => 'bi-controller',
                'is_active' => true,
            ]
        );

        $this->genre = Genre::firstOrCreate(
            ['slug' => 'action-rpg'],
            [
                'name' => 'Action RPG',
            ]
        );
    }

    public function test_guest_cannot_access_admin_content_routes(): void
    {
        $this->get(route('admin.content.index'))->assertRedirect(route('login'));
        $this->get(route('admin.content.create'))->assertRedirect(route('login'));
        $this->post(route('admin.content.store'), [])->assertRedirect(route('login'));
    }

    public function test_regular_user_is_forbidden_from_admin_content_routes(): void
    {
        $this->actingAs($this->regularUser)->get(route('admin.content.index'))->assertForbidden();
        $this->actingAs($this->regularUser)->get(route('admin.content.create'))->assertForbidden();
        $this->actingAs($this->regularUser)->post(route('admin.content.store'), [])->assertForbidden();
    }

    public function test_admin_can_view_content_management_index(): void
    {
        $testTitle = 'Elden Ring Test ' . uniqid();
        $content = Content::create([
            'category_id' => $this->category->id,
            'user_id' => $this->admin->id,
            'title' => $testTitle,
            'slug' => 'test-content-' . uniqid(),
            'type' => 'article',
            'description' => 'Complete guide to the Land of Shadow.',
            'status' => 'published',
            'views' => 200,
            'popularity' => 45,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.content.index', ['search' => $testTitle]));

        $response->assertOk()
            ->assertSee('Universe Content Hub CMS')
            ->assertSee($testTitle);
    }

    public function test_admin_can_create_new_content(): void
    {
        Storage::fake('public');

        // Use create() instead of image() to avoid GD extension requirement
        $thumbnail = UploadedFile::fake()->create('cover.jpg', 100, 'image/jpeg');

        $testTitle = 'Cyberpunk 2077 Test Article ' . time();

        $response = $this->actingAs($this->admin)->post(route('admin.content.store'), [
            'title' => $testTitle,
            'category_id' => $this->category->id,
            'type' => 'article',
            'status' => 'published',
            'description' => 'Spy-thriller analysis in Dogtown.',
            'body' => 'Detailed walkthrough and lore breakdown of Songbird and Reed.',
            'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'thumbnail' => $thumbnail,
            'release_date' => '2023-09-26',
            'is_featured' => 1,
            'genres' => [$this->genre->id],
        ]);

        $response->assertRedirect(route('admin.content.index'));

        $this->assertDatabaseHas('contents', [
            'title' => $testTitle,
            'category_id' => $this->category->id,
            'type' => 'article',
            'status' => 'published',
            'is_featured' => 1,
        ]);

        $content = Content::where('title', $testTitle)->first();
        $this->assertNotNull($content);
        $this->assertTrue($content->genres->contains($this->genre->id));
        $this->assertNotNull($content->thumbnail);
        Storage::disk('public')->assertExists($content->thumbnail);
    }

    public function test_content_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.content.store'), [
            'title' => '',
            'category_id' => 99999,
            'type' => 'invalid-type',
            'status' => 'unknown-status',
        ]);

        $response->assertSessionHasErrors(['title', 'category_id', 'type', 'status']);
    }

    public function test_admin_can_edit_and_update_content(): void
    {
        $content = Content::create([
            'category_id' => $this->category->id,
            'user_id' => $this->admin->id,
            'title' => 'Draft Content ' . time(),
            'slug' => 'draft-content-' . time(),
            'type' => 'article',
            'description' => 'Original description',
            'status' => 'draft',
            'views' => 10,
            'popularity' => 5,
        ]);

        $updatedTitle = 'Updated Lore Canon ' . time();

        $response = $this->actingAs($this->admin)->put(route('admin.content.update', $content->id), [
            'title' => $updatedTitle,
            'category_id' => $this->category->id,
            'type' => 'video',
            'status' => 'published',
            'description' => 'Updated polished description.',
            'body' => 'Full updated body text.',
            'is_featured' => 1,
            'genres' => [$this->genre->id],
        ]);

        $response->assertRedirect(route('admin.content.index'));

        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
            'title' => $updatedTitle,
            'type' => 'video',
            'status' => 'published',
            'is_featured' => 1,
        ]);
    }

    public function test_admin_can_soft_delete_content(): void
    {
        $content = Content::create([
            'category_id' => $this->category->id,
            'user_id' => $this->admin->id,
            'title' => 'Removable Content ' . time(),
            'slug' => 'removable-content-' . time(),
            'type' => 'article',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.content.destroy', $content->id));

        $response->assertRedirect(route('admin.content.index'));

        $this->assertSoftDeleted('contents', [
            'id' => $content->id,
        ]);
    }
}
