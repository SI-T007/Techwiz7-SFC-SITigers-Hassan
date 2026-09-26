<?php

namespace Tests\Feature;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Event;
use App\Models\Release;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventAndReleaseTest extends TestCase
{
    protected User $admin;
    protected User $regularUser;
    protected User $otherUser;
    protected Category $categoryGaming;
    protected Category $categoryAnime;
    protected Event $eventTokyo;
    protected Event $eventSanDiego;
    protected Release $releaseGta;
    protected Release $releaseAnime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_events_test@fanhub.test'],
            [
                'name' => 'Admin Event Tester',
                'password' => Hash::make('Password@123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->regularUser = User::firstOrCreate(
            ['email' => 'regular_events_test@fanhub.test'],
            [
                'name' => 'Regular Event Tester',
                'password' => Hash::make('Password@123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->otherUser = User::firstOrCreate(
            ['email' => 'other_events_test@fanhub.test'],
            [
                'name' => 'Other Event Tester',
                'password' => Hash::make('Password@123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->categoryGaming = Category::firstOrCreate(
            ['slug' => 'gaming'],
            [
                'name' => 'Gaming',
                'description' => 'Gaming universe',
                'icon' => 'bi-controller',
                'status' => 'active',
                'sort_order' => 2,
            ]
        );

        $this->categoryAnime = Category::firstOrCreate(
            ['slug' => 'anime'],
            [
                'name' => 'Anime',
                'description' => 'Anime universe',
                'icon' => 'bi-tv',
                'status' => 'active',
                'sort_order' => 1,
            ]
        );

        $this->eventTokyo = Event::updateOrCreate(
            ['slug' => 'tokyo-game-show-test-2026'],
            [
                'category_id' => $this->categoryGaming->id,
                'title' => 'Tokyo Game Show Test 2026',
                'event_type' => 'convention',
                'starts_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'ends_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'city' => 'Chiba',
                'country' => 'Japan',
                'venue_name' => 'Makuhari Messe Test',
                'latitude' => 35.64830000,
                'longitude' => 140.03470000,
                'ticket_url' => 'https://tgs.nikkeibp.co.jp',
                'description' => 'International gaming convention in Chiba, Japan.',
                'status' => 'upcoming',
                'is_featured' => true,
            ]
        );

        $this->eventSanDiego = Event::updateOrCreate(
            ['slug' => 'san-diego-comic-con-test-2026'],
            [
                'category_id' => $this->categoryGaming->id,
                'title' => 'San Diego Comic-Con Test 2026',
                'event_type' => 'convention',
                'starts_at' => now()->addDays(4)->format('Y-m-d H:i:s'),
                'ends_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'city' => 'San Diego',
                'country' => 'United States',
                'venue_name' => 'San Diego Convention Center Test',
                'latitude' => 32.70720000,
                'longitude' => -117.16330000,
                'ticket_url' => 'https://www.comic-con.org',
                'description' => 'World famous comic and entertainment convention.',
                'status' => 'upcoming',
                'is_featured' => true,
            ]
        );

        $this->releaseGta = Release::updateOrCreate(
            ['slug' => 'test-gta-vi-release'],
            [
                'category_id' => $this->categoryGaming->id,
                'title' => 'Test Grand Theft Auto VI Release',
                'type' => 'game',
                'description' => 'Anticipated open world game release.',
                'release_date' => now()->addDays(2)->toDateString(),
                'platform' => 'PS5, Xbox',
                'developer_publisher' => 'Rockstar Games',
                'status' => 'upcoming',
                'tags' => ['Pre-Order', 'Collector\'s Edition'],
                'external_url' => 'https://www.rockstargames.com',
                'is_featured' => true,
            ]
        );

        $this->releaseAnime = Release::updateOrCreate(
            ['slug' => 'test-chainsaw-man-movie-release'],
            [
                'category_id' => $this->categoryAnime->id,
                'title' => 'Test Chainsaw Man Movie Release',
                'type' => 'anime',
                'description' => 'Anime movie worldwide premiere.',
                'release_date' => now()->addDays(3)->toDateString(),
                'platform' => 'Theatrical',
                'developer_publisher' => 'MAPPA',
                'status' => 'upcoming',
                'tags' => ['Limited Edition'],
                'external_url' => 'https://chainsawman.dog',
                'is_featured' => true,
            ]
        );

        // Clear existing bookmarks for clean test state
        Bookmark::whereIn('user_id', [$this->regularUser->id, $this->otherUser->id])->delete();
    }

    /* --------------------------------------------------------------------------
     * 1. Public Events Tests
     * -------------------------------------------------------------------------- */

    public function test_public_events_page_loads_successfully(): void
    {
        $response = $this->get(route('events.index'));
        $response->assertStatus(200);
        $response->assertSee('Fandom Gatherings');
        $response->assertSee($this->eventTokyo->title);
    }

    public function test_events_search_filters_results(): void
    {
        $response = $this->get(route('events.index', ['search' => 'Tokyo Game Show']));
        $response->assertStatus(200);
        $response->assertSee('Tokyo Game Show Test 2026');
        $response->assertDontSee('San Diego Comic-Con Test 2026');
    }

    public function test_events_city_filter_works(): void
    {
        $response = $this->get(route('events.index', ['city' => 'Chiba']));
        $response->assertStatus(200);
        $response->assertSee('Tokyo Game Show Test 2026');
        $response->assertDontSee('San Diego Comic-Con Test 2026');
    }

    public function test_events_category_filter_works(): void
    {
        $response = $this->get(route('events.index', ['category' => $this->categoryGaming->slug]));
        $response->assertStatus(200);
        $response->assertSee($this->eventTokyo->title);
    }

    public function test_events_nearby_geolocation_works_safely(): void
    {
        // Coordinates near Chiba, Japan (Tokyo center: 35.6762, 139.6503)
        $response = $this->get(route('events.index', [
            'lat' => 35.6762,
            'lng' => 139.6503,
            'radius' => 100,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tokyo Game Show Test 2026');
        $response->assertSee('Showing fandom gatherings sorted by distance');
    }

    public function test_events_nearby_handles_invalid_coordinates_safely(): void
    {
        $response = $this->get(route('events.index', [
            'lat' => 'invalid_lat',
            'lng' => 9999,
        ]));

        $response->assertStatus(200);
    }

    public function test_event_detail_page_loads_with_venue_and_ticket_link(): void
    {
        $response = $this->get(route('events.show', $this->eventTokyo->slug));
        $response->assertStatus(200);
        $response->assertSee($this->eventTokyo->title);
        $response->assertSee('Makuhari Messe Test');
        $response->assertSee('Official Tickets');
        $response->assertSee('Add to Calendar (.ics)');
        $response->assertSee('Venue Location');
    }

    public function test_event_calendar_page_loads_with_events(): void
    {
        $response = $this->get(route('events.calendar'));
        $response->assertStatus(200);
        $response->assertSee('Interactive Calendar');
        $response->assertSee('Fandom Gatherings');
    }

    public function test_event_ics_export_returns_valid_icalendar_stream(): void
    {
        $response = $this->get(route('events.ics', $this->eventTokyo));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
        $this->assertStringContainsString('BEGIN:VCALENDAR', $response->getContent());
        $this->assertStringContainsString('SUMMARY:Tokyo Game Show Test 2026', $response->getContent());
        $this->assertStringContainsString('END:VCALENDAR', $response->getContent());
    }

    public function test_invalid_event_slug_returns_404(): void
    {
        $response = $this->get(route('events.show', 'non-existent-event-slug-xyz'));
        $response->assertStatus(404);
    }

    /* --------------------------------------------------------------------------
     * 2. Public Releases Tests
     * -------------------------------------------------------------------------- */

    public function test_public_releases_page_loads_successfully(): void
    {
        $response = $this->get(route('releases.index'));
        $response->assertStatus(200);
        $response->assertSee('Fandom Release Radar');
        $response->assertSee($this->releaseGta->title);
    }

    public function test_releases_search_filters_results(): void
    {
        $response = $this->get(route('releases.index', ['search' => 'Grand Theft Auto']));
        $response->assertStatus(200);
        $response->assertSee($this->releaseGta->title);
        $response->assertDontSee($this->releaseAnime->title);
    }

    public function test_releases_category_filter_works(): void
    {
        $response = $this->get(route('releases.index', ['category' => $this->categoryAnime->slug]));
        $response->assertStatus(200);
        $response->assertSee($this->releaseAnime->title);
        $response->assertDontSee($this->releaseGta->title);
    }

    public function test_releases_tag_filter_works(): void
    {
        $response = $this->get(route('releases.index', ['tag' => 'Pre-Order']));
        $response->assertStatus(200);
        $response->assertSee($this->releaseGta->title);
        $response->assertDontSee($this->releaseAnime->title);
    }

    public function test_release_detail_page_loads_with_countdown_and_tags(): void
    {
        $response = $this->get(route('releases.show', $this->releaseGta->slug));
        $response->assertStatus(200);
        $response->assertSee($this->releaseGta->title);
        $response->assertSee('Rockstar Games');
        $response->assertSee('Pre-Order');
        $response->assertSee('Official Product / Store Page');
        $response->assertSee('countdownContainer');
    }

    public function test_invalid_release_slug_returns_404(): void
    {
        $response = $this->get(route('releases.show', 'non-existent-release-slug-xyz'));
        $response->assertStatus(404);
    }

    /* --------------------------------------------------------------------------
     * 3. Bookmarking Events & Releases
     * -------------------------------------------------------------------------- */

    public function test_authenticated_user_can_bookmark_event(): void
    {
        $response = $this->actingAs($this->regularUser)->postJson(route('bookmarks.toggle'), [
            'bookmarkable_type' => 'event',
            'bookmarkable_id' => $this->eventTokyo->id,
            'note' => 'Plan flights to Japan for TGS.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_bookmarked' => true]);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $this->regularUser->id,
            'bookmarkable_type' => Event::class,
            'bookmarkable_id' => $this->eventTokyo->id,
        ]);
    }

    public function test_authenticated_user_can_bookmark_release(): void
    {
        $response = $this->actingAs($this->regularUser)->postJson(route('bookmarks.toggle'), [
            'bookmarkable_type' => 'release',
            'bookmarkable_id' => $this->releaseGta->id,
            'note' => 'Pre-order collector edition immediately.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_bookmarked' => true]);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $this->regularUser->id,
            'bookmarkable_type' => Release::class,
            'bookmarkable_id' => $this->releaseGta->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_bookmark(): void
    {
        $bookmark = Bookmark::create([
            'user_id' => $this->regularUser->id,
            'bookmarkable_type' => Event::class,
            'bookmarkable_id' => $this->eventSanDiego->id,
            'note' => 'Private bookmark',
        ]);

        // otherUser tries to delete regularUser's bookmark
        $response = $this->actingAs($this->otherUser)->delete(route('bookmarks.destroy', $bookmark));
        $response->assertStatus(403);

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
        ]);
    }

    /* --------------------------------------------------------------------------
     * 4. Admin Event Management (CRUD & Authorization)
     * -------------------------------------------------------------------------- */

    public function test_guest_cannot_access_admin_event_index(): void
    {
        $response = $this->get(route('admin.events.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_admin_event_index(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.events.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_event_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.events.index'));
        $response->assertStatus(200);
        $response->assertSee('Events Management');
    }

    public function test_admin_can_create_event(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post(route('admin.events.store'), [
            'title' => 'Gamescom Cologne Auto Test',
            'category_id' => $this->categoryGaming->id,
            'event_type' => 'convention',
            'starts_at' => now()->addMonths(4)->format('Y-m-d H:i:s'),
            'ends_at' => now()->addMonths(4)->addDays(4)->format('Y-m-d H:i:s'),
            'city' => 'Cologne',
            'country' => 'Germany',
            'venue_name' => 'Koelnmesse Center',
            'address' => 'Messepl. 1, 50679 Koeln',
            'latitude' => 50.94630000,
            'longitude' => 6.98060000,
            'ticket_url' => 'https://www.gamescom.global',
            'description' => 'Heart of Gaming in Cologne Germany.',
            'status' => 'upcoming',
            'is_featured' => 1,
        ]);

        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', [
            'title' => 'Gamescom Cologne Auto Test',
            'city' => 'Cologne',
            'country' => 'Germany',
        ]);
    }

    public function test_admin_event_creation_rejects_invalid_ticket_url(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.events.store'), [
            'title' => 'Malicious URL Event Test',
            'category_id' => $this->categoryGaming->id,
            'event_type' => 'convention',
            'starts_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'city' => 'Berlin',
            'ticket_url' => 'javascript:alert(1)',
        ]);

        $response->assertSessionHasErrors(['ticket_url']);
    }

    public function test_admin_can_update_event(): void
    {
        $event = Event::create([
            'category_id' => $this->categoryGaming->id,
            'title' => 'Event To Update',
            'slug' => 'event-to-update-' . uniqid(),
            'event_type' => 'convention',
            'starts_at' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'city' => 'Seoul',
            'country' => 'South Korea',
            'venue_name' => 'COEX Center',
            'status' => 'upcoming',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.events.update', $event), [
            'title' => 'Updated Event Title Success',
            'category_id' => $this->categoryGaming->id,
            'event_type' => 'convention',
            'starts_at' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'city' => 'Seoul',
            'country' => 'South Korea',
            'venue_name' => 'COEX Hall D',
            'status' => 'upcoming',
        ]);

        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Event Title Success',
            'venue_name' => 'COEX Hall D',
        ]);
    }

    public function test_admin_can_delete_event(): void
    {
        $event = Event::create([
            'category_id' => $this->categoryGaming->id,
            'title' => 'Event To Delete',
            'slug' => 'event-to-delete-' . uniqid(),
            'event_type' => 'convention',
            'starts_at' => now()->addDays(20)->format('Y-m-d H:i:s'),
            'city' => 'Paris',
            'status' => 'upcoming',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.events.destroy', $event));
        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    /* --------------------------------------------------------------------------
     * 5. Admin Release Management (CRUD & Authorization)
     * -------------------------------------------------------------------------- */

    public function test_guest_cannot_access_admin_releases(): void
    {
        $response = $this->get(route('admin.releases.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_admin_releases(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.releases.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_releases_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.releases.index'));
        $response->assertStatus(200);
        $response->assertSee('Releases Radar CMS');
    }

    public function test_admin_can_create_release(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post(route('admin.releases.store'), [
            'title' => 'Final Fantasy XVI PC Edition Test',
            'category_id' => $this->categoryGaming->id,
            'type' => 'game',
            'release_date' => now()->addMonths(2)->toDateString(),
            'platform' => 'PC / Steam',
            'developer_publisher' => 'Square Enix',
            'tags' => 'Pre-Order, Limited Edition, 4K HDR',
            'external_url' => 'https://square-enix-games.com',
            'description' => 'Epic dark fantasy action RPG ported to PC platform.',
            'status' => 'upcoming',
            'is_featured' => 1,
        ]);

        $response->assertRedirect(route('admin.releases.index'));
        $this->assertDatabaseHas('releases', [
            'title' => 'Final Fantasy XVI PC Edition Test',
            'platform' => 'PC / Steam',
        ]);
    }

    public function test_admin_release_creation_rejects_invalid_url(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.releases.store'), [
            'title' => 'Invalid URL Release Test',
            'category_id' => $this->categoryGaming->id,
            'type' => 'game',
            'release_date' => now()->addDays(10)->toDateString(),
            'external_url' => 'not-a-valid-url',
        ]);

        $response->assertSessionHasErrors(['external_url']);
    }

    public function test_admin_can_update_release(): void
    {
        $release = Release::create([
            'category_id' => $this->categoryGaming->id,
            'title' => 'Release To Update',
            'slug' => 'release-to-update-' . uniqid(),
            'type' => 'game',
            'release_date' => now()->addDays(14)->toDateString(),
            'platform' => 'Nintendo Switch',
            'status' => 'upcoming',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.releases.update', $release), [
            'title' => 'Updated Release Title',
            'category_id' => $this->categoryGaming->id,
            'type' => 'game',
            'release_date' => now()->addDays(20)->toDateString(),
            'platform' => 'Nintendo Switch OLED',
            'tags' => 'Pre-Order, Collectible',
            'status' => 'upcoming',
        ]);

        $response->assertRedirect(route('admin.releases.index'));
        $this->assertDatabaseHas('releases', [
            'id' => $release->id,
            'title' => 'Updated Release Title',
            'platform' => 'Nintendo Switch OLED',
        ]);
    }

    public function test_admin_can_delete_release(): void
    {
        $release = Release::create([
            'category_id' => $this->categoryGaming->id,
            'title' => 'Release To Delete',
            'slug' => 'release-to-delete-' . uniqid(),
            'type' => 'game',
            'release_date' => now()->addDays(30)->toDateString(),
            'status' => 'upcoming',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.releases.destroy', $release));
        $response->assertRedirect(route('admin.releases.index'));
        $this->assertDatabaseMissing('releases', ['id' => $release->id]);
    }
}
