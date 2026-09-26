<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Character;
use App\Models\Content;
use App\Models\Event;
use App\Models\Release;
use Tests\TestCase;

class PublicWebsiteShellTest extends TestCase
{
    public function test_homepage_loads_with_real_database_stats_and_sections(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Verify Hero & Core Sections
        $response->assertSee('One Organized Universe');
        $response->assertSee('For Every Fandom');
        $response->assertSee('The 8 Core Fandom Realms');
        $response->assertSee('Character Archives Spotlight');
        $response->assertSee('Upcoming Release Drops');
        $response->assertSee('Universe Visual Sitemap');

        // Verify database categories rendered
        $categories = Category::active()->take(4)->get();
        foreach ($categories as $cat) {
            $response->assertSee($cat->slug);
        }
    }

    public function test_explore_page_loads_and_filters_content(): void
    {
        $response = $this->get('/explore');
        $response->assertStatus(200);
        $response->assertSee('Explore Lore');
        $response->assertSee('Discovery Hub');

        // Test category filter
        $animeCat = Category::where('slug', 'anime')->first();
        if ($animeCat) {
            $filterResponse = $this->get("/explore?category={$animeCat->slug}");
            $filterResponse->assertStatus(200);
        }
    }

    public function test_content_detail_page_loads_with_breadcrumbs(): void
    {
        $content = Content::published()->first();
        if ($content) {
            $response = $this->get("/explore/{$content->slug}");
            $response->assertStatus(200);
            $response->assertSee($content->title);
            $response->assertSee('Explore');
        }
    }

    public function test_fandoms_directory_and_single_fandom_hub_loads(): void
    {
        $response = $this->get('/fandoms');
        $response->assertStatus(200);
        $response->assertSee('Fandom Universes');
        $response->assertSee('Fandoms Active');

        $category = Category::active()->first();
        $this->assertNotNull($category);

        $hubResponse = $this->get("/fandoms/{$category->slug}");
        $hubResponse->assertStatus(200);
        $hubResponse->assertSee($category->name);
        $hubResponse->assertSee('Verified Fandom Universe');
    }

    public function test_characters_directory_and_character_show_loads(): void
    {
        $response = $this->get('/characters');
        $response->assertStatus(200);
        $response->assertSee('Fandom Heroes');

        $character = Character::active()->first();
        if ($character) {
            $showResponse = $this->get("/characters/{$character->slug}");
            $showResponse->assertStatus(200);
            $showResponse->assertSee($character->name);
            $showResponse->assertSee('Canonical Biography');
        }
    }

    public function test_releases_tracker_page_loads(): void
    {
        $response = $this->get('/releases');
        $response->assertStatus(200);
        $response->assertSee('Upcoming Fandom Releases');
        $response->assertSee('Drop Schedule');
    }

    public function test_events_page_loads(): void
    {
        $response = $this->get('/events');
        $response->assertStatus(200);
        $response->assertSee('Fandom Gatherings');
        $response->assertSee('Event Calendar');
    }

    public function test_community_and_about_pages_load(): void
    {
        $commResponse = $this->get('/community');
        $commResponse->assertStatus(200);
        $commResponse->assertSee('The Fan Collective');

        $aboutResponse = $this->get('/about');
        $aboutResponse->assertStatus(200);
        $aboutResponse->assertSee('About Fan Hub Plus');
        $aboutResponse->assertSee('Clean MVC Architecture');
    }

    public function test_custom_404_error_page_renders_with_design_system(): void
    {
        $response = $this->get('/non-existent-sector-12345');
        $response->assertStatus(404);
        $response->assertSee('404 Lost in Space');
        $response->assertSee('Return to Base');
    }
}
