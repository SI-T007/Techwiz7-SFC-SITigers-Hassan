<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(
            ['email' => 'feedback_user@fanhub.test'],
            [
                'name' => 'Feedback User',
                'password' => Hash::make('Password@123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'feedback_admin@fanhub.test'],
            [
                'name' => 'Support Admin',
                'password' => Hash::make('Password@123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    public function test_guest_can_access_feedback_form(): void
    {
        $response = $this->get(route('feedback.create'));
        $response->assertOk();
        $response->assertSee('Platform Feedback & Bug Reports');
    }

    public function test_authenticated_user_can_submit_feedback_and_it_records_user_id(): void
    {
        $response = $this->actingAs($this->user)->post(route('feedback.store'), [
            'name' => 'Feedback User',
            'email' => 'feedback_user@fanhub.test',
            'type' => 'suggestion',
            'subject' => 'Dark Mode Contrast in Table Headers',
            'message' => 'Please consider adjusting table header contrast when viewing on OLED screens.',
        ]);

        $response->assertRedirect(route('feedback.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('feedback', [
            'user_id' => $this->user->id,
            'subject' => 'Dark Mode Contrast in Table Headers',
            'type' => 'suggestion',
            'status' => 'new',
        ]);
    }

    public function test_feedback_requires_valid_data(): void
    {
        $response = $this->post(route('feedback.store'), []);
        $response->assertSessionHasErrors(['type', 'subject', 'message']);
    }

    public function test_guest_can_submit_feedback(): void
    {
        $response = $this->post(route('feedback.store'), [
            'name' => 'Anonymous Visitor',
            'email' => 'visitor@example.com',
            'type' => 'bug',
            'subject' => 'Broken Link on External Lore',
            'message' => 'Encountered a 404 response on an external reference link.',
        ]);

        $response->assertRedirect(route('feedback.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('feedback', [
            'user_id' => null,
            'name' => 'Anonymous Visitor',
            'type' => 'bug',
            'status' => 'new',
        ]);
    }

    public function test_regular_user_cannot_access_admin_feedback_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.feedback.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_feedback_list(): void
    {
        $feedback = Feedback::create([
            'name' => 'Test Reporter',
            'email' => 'test@fanhub.test',
            'type' => 'query',
            'subject' => 'How to become a verified contributor?',
            'message' => 'I would love to apply for community lore editor status.',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.feedback.index'));
        $response->assertOk();
        $response->assertSee('How to become a verified contributor?');

        $feedback->delete();
    }

    public function test_admin_can_reply_to_feedback_and_update_status(): void
    {
        $feedback = Feedback::create([
            'name' => 'Inquiring Fan',
            'email' => 'fan@fanhub.test',
            'type' => 'query',
            'subject' => 'Event schedule question',
            'message' => 'When is the next community anime meetup scheduled?',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.feedback.reply', $feedback), [
            'admin_reply' => 'The next meetup will take place on November 15th at Tokyo convention center.',
        ]);

        $response->assertRedirect(route('admin.feedback.show', $feedback));
        $response->assertSessionHas('success');

        $feedback->refresh();
        $this->assertEquals('replied', $feedback->status);
        $this->assertEquals('The next meetup will take place on November 15th at Tokyo convention center.', $feedback->admin_reply);
        $this->assertEquals($this->admin->id, $feedback->replied_by);
        $this->assertNotNull($feedback->replied_at);

        $feedback->delete();
    }

    public function test_admin_can_update_feedback_status_directly(): void
    {
        $feedback = Feedback::create([
            'name' => 'Status Test User',
            'email' => 'status@fanhub.test',
            'type' => 'bug',
            'subject' => 'Minor CSS typo',
            'message' => 'Fixed in latest release.',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.feedback.status', $feedback), [
            'status' => 'archived',
        ]);

        $response->assertRedirect(route('admin.feedback.show', $feedback));
        $response->assertSessionHas('success');

        $feedback->refresh();
        $this->assertEquals('archived', $feedback->status);

        $feedback->delete();
    }
}
