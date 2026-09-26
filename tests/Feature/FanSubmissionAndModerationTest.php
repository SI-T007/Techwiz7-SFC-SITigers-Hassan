<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FanSubmission;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FanSubmissionAndModerationTest extends TestCase
{
    protected User $user;
    protected User $otherUser;
    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::firstOrCreate(
            ['slug' => 'anime'],
            [
                'name' => 'Anime',
                'description' => 'Anime fandom universe',
                'icon' => 'bi-tv',
                'is_active' => true,
            ]
        );

        $this->user = User::firstOrCreate(
            ['email' => 'fan_author@fanhub.test'],
            [
                'name' => 'Fan Author',
                'password' => Hash::make('Password@123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->otherUser = User::firstOrCreate(
            ['email' => 'fan_other@fanhub.test'],
            [
                'name' => 'Fan Other',
                'password' => Hash::make('Password@123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'fan_admin@fanhub.test'],
            [
                'name' => 'Moderator Admin',
                'password' => Hash::make('Password@123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    public function test_guest_cannot_access_submission_form(): void
    {
        $response = $this->get(route('community.submit'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_submission_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('community.submit'));
        $response->assertOk();
        $response->assertSee('Submit Fan Creation');
    }

    public function test_authenticated_user_can_submit_fan_content_and_it_starts_pending(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('fan_art_preview.jpg', 120, 'image/jpeg');

        $response = $this->actingAs($this->user)->post(route('community.submit.store'), [
            'category_id' => $this->category->id,
            'title' => 'The Eva Unit-01 Awakening Analysis',
            'type' => 'fan_art',
            'content' => 'A deeply detailed fan artwork and technical breakdown of Eva-01 berserk state.',
            'media_file' => $file,
            'source_url' => 'https://artstation.com/artwork/example',
        ]);

        $response->assertRedirect(route('community.my-submissions'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('fan_submissions', [
            'user_id' => $this->user->id,
            'title' => 'The Eva Unit-01 Awakening Analysis',
            'status' => 'pending',
            'type' => 'fan_art',
        ]);
    }

    public function test_user_can_view_own_submissions_dashboard(): void
    {
        $submission = FanSubmission::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'My Personal Lore Essay',
            'type' => 'fan_fiction',
            'body' => 'An alternate timeline scenario for Tokyo-3.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get(route('community.my-submissions'));
        $response->assertOk();
        $response->assertSee('My Personal Lore Essay');
        $response->assertSee('Pending Review');

        $submission->delete();
    }

    public function test_user_cannot_delete_another_users_submission(): void
    {
        $submission = FanSubmission::create([
            'user_id' => $this->otherUser->id,
            'category_id' => $this->category->id,
            'title' => 'Other User Exclusive Story',
            'type' => 'fan_fiction',
            'body' => 'Secret fan fiction lore.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->delete(route('community.submissions.destroy', $submission));
        $response->assertForbidden();

        $this->assertDatabaseHas('fan_submissions', [
            'id' => $submission->id,
        ]);

        $submission->delete();
    }

    public function test_user_cannot_delete_an_approved_submission(): void
    {
        $submission = FanSubmission::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Approved Masterpiece',
            'type' => 'fan_fiction',
            'body' => 'Approved community content.',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->user)->delete(route('community.submissions.destroy', $submission));
        $response->assertForbidden();

        $this->assertDatabaseHas('fan_submissions', [
            'id' => $submission->id,
        ]);

        $submission->delete();
    }

    public function test_guest_cannot_view_unapproved_submission_detail(): void
    {
        $submission = FanSubmission::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Hidden Draft Submission',
            'type' => 'fan_art',
            'body' => 'Private draft content',
            'status' => 'pending',
        ]);

        $response = $this->get(route('community.submissions.show', $submission));
        $response->assertNotFound();

        $submission->delete();
    }

    public function test_guest_and_user_can_view_approved_submission_detail(): void
    {
        $submission = FanSubmission::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Public Approved Showcase',
            'type' => 'cosplay',
            'body' => 'Handmade handcrafted Asuka armor suit and materials breakdown.',
            'status' => 'approved',
        ]);

        $response = $this->get(route('community.submissions.show', $submission));
        $response->assertOk();
        $response->assertSee('Public Approved Showcase');
        $response->assertSee('Handmade handcrafted Asuka armor suit');

        $submission->delete();
    }

    public function test_regular_user_cannot_access_admin_moderation_queue(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.submissions.index'));
        $response->assertForbidden();
    }

    public function test_regular_user_cannot_approve_or_reject_submission(): void
    {
        $submission = FanSubmission::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Hacker Target Submission',
            'type' => 'cosplay',
            'body' => 'Testing unauthorized approval endpoint.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->otherUser)->post(route('admin.submissions.approve', $submission));
        $response->assertForbidden();

        $responseReject = $this->actingAs($this->otherUser)->post(route('admin.submissions.reject', $submission), [
            'rejection_reason' => 'Unauthorized rejection',
        ]);
        $responseReject->assertForbidden();

        $this->assertDatabaseHas('fan_submissions', [
            'id' => $submission->id,
            'status' => 'pending',
        ]);

        $submission->delete();
    }

    public function test_admin_can_view_moderation_queue(): void
    {
        $submission = FanSubmission::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Queue Inspection Item',
            'type' => 'cosplay',
            'body' => 'Review item for administrator.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.submissions.index'));
        $response->assertOk();
        $response->assertSee('Queue Inspection Item');

        $submission->delete();
    }

    public function test_admin_can_approve_submission(): void
    {
        $submission = FanSubmission::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Submission to Approve',
            'type' => 'fan_fiction',
            'body' => 'High quality story awaiting admin review.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.submissions.approve', $submission));
        $response->assertRedirect(route('admin.submissions.index'));
        $response->assertSessionHas('success');

        $submission->refresh();
        $this->assertEquals('approved', $submission->status);
        $this->assertEquals($this->admin->id, $submission->reviewed_by);

        $submission->delete();
    }

    public function test_admin_can_reject_submission_with_reason(): void
    {
        $submission = FanSubmission::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Submission to Reject',
            'type' => 'fan_art',
            'body' => 'Low resolution submission not meeting standard guidelines.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.submissions.reject', $submission), [
            'rejection_reason' => 'Image does not meet quality requirements or resolution standard.',
        ]);

        $response->assertRedirect(route('admin.submissions.index'));
        $response->assertSessionHas('success');

        $submission->refresh();
        $this->assertEquals('rejected', $submission->status);
        $this->assertEquals('Image does not meet quality requirements or resolution standard.', $submission->rejection_reason);
        $this->assertEquals($this->admin->id, $submission->reviewed_by);

        $submission->delete();
    }
}
