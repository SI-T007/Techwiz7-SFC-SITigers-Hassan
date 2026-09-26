<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Character;
use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use App\Models\Content;
use App\Models\Genre;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminControlCenterAndAnalyticsTest extends TestCase
{
    protected User $admin;
    protected User $regularUser;
    protected User $targetUser;
    protected Category $categorySciFi;
    protected Genre $genreCyberpunk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_p9_test@fanhub.test'],
            [
                'name' => 'Admin Phase09 Tester',
                'password' => Hash::make('Password@123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->regularUser = User::firstOrCreate(
            ['email' => 'user_p9_test@fanhub.test'],
            [
                'name' => 'User Phase09 Tester',
                'password' => Hash::make('Password@123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->targetUser = User::firstOrCreate(
            ['email' => 'target_p9_test@fanhub.test'],
            [
                'name' => 'Target User Tester',
                'password' => Hash::make('Password@123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->categorySciFi = Category::firstOrCreate(
            ['slug' => 'scifi-admin-test'],
            [
                'name' => 'Sci-Fi Admin Test',
                'description' => 'Sci-Fi universe testing',
                'icon' => 'bi-rocket-takeoff',
                'status' => 'active',
                'sort_order' => 10,
            ]
        );

        $this->genreCyberpunk = Genre::firstOrCreate(
            ['slug' => 'cyberpunk-admin-test'],
            [
                'name' => 'Cyberpunk Admin Genre',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN AUTHORIZATION & ACCESS CONTROL TESTS
    |--------------------------------------------------------------------------
    */

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_admin_control_center(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.dashboard'));
        $response->assertStatus(403);

        $analyticsResp = $this->actingAs($this->regularUser)->get(route('admin.analytics.index'));
        $analyticsResp->assertStatus(403);

        $usersResp = $this->actingAs($this->regularUser)->get(route('admin.users'));
        $usersResp->assertStatus(403);

        $categoriesResp = $this->actingAs($this->regularUser)->get(route('admin.categories.index'));
        $categoriesResp->assertStatus(403);
    }

    public function test_admin_can_access_dashboard_and_modules(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Universe Control Center');
        $response->assertSee('Registered Users');
    }

    /*
    |--------------------------------------------------------------------------
    | USER MANAGEMENT & ADMIN SELF-PROTECTION TESTS
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_users_directory(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users', ['search' => $this->targetUser->email]));
        $response->assertStatus(200);
        $response->assertSee('Registered User Accounts');
        $response->assertSee($this->targetUser->email);
    }

    public function test_admin_can_view_user_profile_audit(): void
    {
        UserActivity::record($this->targetUser->id, 'viewed_content', 'Target user read an article');

        $response = $this->actingAs($this->admin)->get(route('admin.users.show', $this->targetUser));
        $response->assertStatus(200);
        $response->assertSee('User Profile Audit');
        $response->assertSee($this->targetUser->name);
        $response->assertSee('Target user read an article');
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $this->targetUser->update(['is_active' => true]);

        $response = $this->actingAs($this->admin)->post(route('admin.users.toggle-status', $this->targetUser));
        $response->assertRedirect();

        $this->targetUser->refresh();
        $this->assertFalse($this->targetUser->is_active);

        // Reset
        $this->targetUser->update(['is_active' => true]);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.toggle-status', $this->admin));
        $response->assertSessionHas('error');

        $this->admin->refresh();
        $this->assertTrue($this->admin->is_active);
    }

    public function test_admin_can_update_user_role(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.role', $this->targetUser), [
            'role' => 'admin',
        ]);

        $response->assertSessionHas('success');
        $this->targetUser->refresh();
        $this->assertEquals('admin', $this->targetUser->role);

        // Reset to user
        $this->targetUser->update(['role' => 'user']);
    }

    public function test_admin_cannot_demote_self(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.role', $this->admin), [
            'role' => 'user',
        ]);

        $response->assertSessionHas('error');
        $this->admin->refresh();
        $this->assertEquals('admin', $this->admin->role);
    }

    /*
    |--------------------------------------------------------------------------
    | FANDOM CATEGORY MANAGEMENT & SAFE DELETION TESTS
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_create_category(): void
    {
        $uid = uniqid();
        $catName = 'Mecha Robot ' . $uid;
        $uniqueSlug = 'mecha-robot-' . $uid;

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => $catName,
            'slug' => $uniqueSlug,
            'description' => 'Giant robot battles and tactical mech simulators.',
            'icon' => 'bi-robot',
            'status' => 'active',
            'sort_order' => 5,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'slug' => $uniqueSlug,
            'name' => $catName,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $uid = uniqid();
        $category = Category::create([
            'name' => 'Original Retro ' . $uid,
            'slug' => 'orig-retro-' . $uid,
            'status' => 'active',
            'sort_order' => 1,
        ]);

        $updatedName = 'Updated Retro ' . $uid;
        $updatedSlug = 'upd-retro-' . $uid;
        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category), [
            'name' => $updatedName,
            'slug' => $updatedSlug,
            'description' => 'Updated description text',
            'icon' => 'bi-joystick',
            'status' => 'inactive',
            'sort_order' => 2,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $updatedName,
            'status' => 'inactive',
        ]);
    }

    public function test_safe_category_deletion_prevents_deleting_linked_category(): void
    {
        $uid = uniqid();
        $linkedCategory = Category::create([
            'name' => 'Linked Univ ' . $uid,
            'slug' => 'linked-univ-' . $uid,
            'status' => 'active',
        ]);

        // Attach content to this category
        Content::create([
            'category_id' => $linkedCategory->id,
            'title' => 'Lore Item Linked ' . $uid,
            'slug' => 'lore-item-linked-' . $uid,
            'type' => 'article',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $linkedCategory));
        $response->assertSessionHas('error');

        // Category must still exist to avoid cascading corruption
        $this->assertDatabaseHas('categories', ['id' => $linkedCategory->id]);
    }

    public function test_admin_can_delete_unlinked_category(): void
    {
        $uid = uniqid();
        $unlinkedCategory = Category::create([
            'name' => 'Unlinked Temp ' . $uid,
            'slug' => 'unlinked-temp-' . $uid,
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $unlinkedCategory));
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $unlinkedCategory->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | CHARACTER MANAGEMENT TESTS
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_create_character(): void
    {
        $charSlug = 'major-motoko-' . uniqid();
        $response = $this->actingAs($this->admin)->post(route('admin.characters.store'), [
            'category_id' => $this->categorySciFi->id,
            'name' => 'Major Motoko Test',
            'slug' => $charSlug,
            'franchise' => 'Ghost in the Shell',
            'biography' => 'Cyborg squad commander of Section 9.',
            'abilities' => 'Thermo-optic camouflage, advanced cyberbrain hacking',
            'status' => 'active',
            'is_featured' => '1',
        ]);

        $response->assertRedirect(route('admin.characters.index'));
        $this->assertDatabaseHas('characters', [
            'slug' => $charSlug,
            'franchise' => 'Ghost in the Shell',
            'is_featured' => true,
        ]);
    }

    public function test_admin_can_update_character(): void
    {
        $char = Character::create([
            'category_id' => $this->categorySciFi->id,
            'name' => 'Batou Test',
            'slug' => 'batou-' . uniqid(),
            'franchise' => 'Ghost in the Shell',
            'biography' => 'Section 9 second in command.',
            'status' => 'active',
        ]);

        $updCharSlug = 'batou-mod-' . uniqid();
        $response = $this->actingAs($this->admin)->put(route('admin.characters.update', $char), [
            'category_id' => $this->categorySciFi->id,
            'name' => 'Batou Modified',
            'slug' => $updCharSlug,
            'franchise' => 'Ghost in the Shell SAC',
            'biography' => 'Updated biography notes.',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.characters.index'));
        $this->assertDatabaseHas('characters', [
            'id' => $char->id,
            'name' => 'Batou Modified',
        ]);
    }

    public function test_admin_can_delete_character(): void
    {
        $char = Character::create([
            'category_id' => $this->categorySciFi->id,
            'name' => 'Togusa Test',
            'slug' => 'togusa-' . uniqid(),
            'franchise' => 'Section 9',
            'biography' => 'Detective specialist.',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.characters.destroy', $char));
        $response->assertRedirect(route('admin.characters.index'));
        $this->assertDatabaseMissing('characters', ['id' => $char->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | ARTICLE MANAGEMENT TESTS
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_create_lore_article(): void
    {
        $artSlug = 'origins-matrix-' . uniqid();
        $response = $this->actingAs($this->admin)->post(route('admin.articles.store'), [
            'category_id' => $this->categorySciFi->id,
            'title' => 'Origins of the Multiverse Matrix',
            'slug' => $artSlug,
            'description' => 'A deep dive into cross-dimensional space anomalies.',
            'body' => 'Comprehensive breakdown of temporal shifts and parallel realities.',
            'status' => 'published',
            'is_featured' => '1',
            'genres' => [$this->genreCyberpunk->id],
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseHas('contents', [
            'slug' => $artSlug,
            'type' => 'article',
            'status' => 'published',
            'is_featured' => true,
        ]);
    }

    public function test_admin_can_toggle_article_featured_spotlight(): void
    {
        $article = Content::create([
            'category_id' => $this->categorySciFi->id,
            'title' => 'Cyber Warfare Tactics Spotlight',
            'slug' => 'cyber-warfare-' . uniqid(),
            'type' => 'article',
            'body' => 'Tactics guide.',
            'is_featured' => false,
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.articles.toggle-featured', $article));
        $response->assertRedirect();

        $article->refresh();
        $this->assertTrue($article->is_featured);
    }

    /*
    |--------------------------------------------------------------------------
    | CHATBOT CONVERSATIONS TELEMETRY & MANAGEMENT TESTS
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_conversations_telemetry(): void
    {
        $convTitle = 'Admin Telemetry Inspection ' . uniqid();
        $conversation = ChatbotConversation::create([
            'user_id' => $this->targetUser->id,
            'title' => $convTitle,
        ]);

        ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'user',
            'message' => 'What is the release date for GTA VI?',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.chatbot.conversations.index'));
        $response->assertStatus(200);
        $response->assertSee('Chatbot Conversations', false);
        $response->assertSee($convTitle);
    }

    public function test_admin_can_view_conversation_transcript(): void
    {
        $convTitle = 'Transcript Test Subject ' . uniqid();
        $conversation = ChatbotConversation::create([
            'user_id' => $this->targetUser->id,
            'title' => $convTitle,
        ]);

        ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'user',
            'message' => 'Hello Bot Test Message!',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.chatbot.conversations.show', $conversation));
        $response->assertStatus(200);
        $response->assertSee($convTitle);
        $response->assertSee('Hello Bot Test Message!');
    }

    public function test_admin_can_delete_conversation(): void
    {
        $conversation = ChatbotConversation::create([
            'user_id' => $this->targetUser->id,
            'title' => 'Spam Conversation to Remove ' . uniqid(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.chatbot.conversations.destroy', $conversation));
        $response->assertRedirect(route('admin.chatbot.conversations.index'));
        $this->assertDatabaseMissing('chatbot_conversations', ['id' => $conversation->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | PLATFORM ANALYTICS TESTS
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_platform_analytics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.analytics.index'));
        $response->assertStatus(200);
        $response->assertSee('Platform Analytics', false);
        $response->assertSee('Active Users in Period', false);
        $response->assertSee('Popular Fandom Categories', false);
        $response->assertSee('Chatbot Intelligence Telemetry', false);
    }

    public function test_analytics_date_filters_execute_cleanly(): void
    {
        foreach (['today', '7_days', '30_days', '90_days', 'all_time'] as $range) {
            $response = $this->actingAs($this->admin)->get(route('admin.analytics.index', ['range' => $range]));
            $response->assertStatus(200);
            $response->assertSee('Platform Analytics', false);
        }
    }
}
