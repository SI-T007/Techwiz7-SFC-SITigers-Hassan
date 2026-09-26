<?php

namespace App\Services\Chatbot;

use App\Models\Category;
use App\Models\Character;
use App\Models\Content;
use App\Models\Event;
use App\Models\Release;
use Illuminate\Support\Str;

class DatabaseQueryService
{
    /**
     * Inspect user query and return dynamic database-driven response if applicable.
     *
     * @param string $query
     * @return array|null Returns ['text' => string, 'source' => 'database', 'metadata' => array] or null
     */
    public function queryDatabase(string $query): ?array
    {
        $q = strtolower(trim($query));

        // 1. Fandom Categories Query
        if (Str::contains($q, ['what fandom', 'available fandom', 'fandoms', 'categories', 'list categories', 'fandom universe'])) {
            $categories = Category::active()->orderBy('sort_order')->get();
            if ($categories->isNotEmpty()) {
                $list = $categories->map(fn($c) => "• **{$c->name}**: {$c->description}")->implode("\n");
                $text = "According to the Fan Hub Plus universe, here are the available core fandoms:\n\n{$list}\n\nYou can explore all fandom hubs anytime at [/fandoms](" . route('fandoms.index') . ").";
                return [
                    'text' => $text,
                    'source' => 'database',
                    'metadata' => ['type' => 'categories', 'count' => $categories->count()],
                ];
            }
        }

        // 2. Upcoming Events Query
        if (Str::contains($q, ['upcoming event', 'upcoming events', 'events', 'conventions', 'gatherings', 'event calendar'])) {
            $events = Event::upcoming()->take(3)->get();
            if ($events->isNotEmpty()) {
                $list = $events->map(function ($ev) {
                    $date = $ev->starts_at ? $ev->starts_at->format('M d, Y') : 'TBA';
                    $location = $ev->city ? " in {$ev->city}, {$ev->country}" : '';
                    return "• **[{$ev->title}](" . route('events.show', $ev->slug) . ")** ({$date}{$location})";
                })->implode("\n");

                $text = "Here are the upcoming fandom gatherings on the calendar:\n\n{$list}\n\nBrowse the complete list or search near your location at [/events](" . route('events.index') . ") or view the monthly grid at [/events/calendar](" . route('events.calendar') . ").";
                return [
                    'text' => $text,
                    'source' => 'database',
                    'metadata' => ['type' => 'events', 'count' => $events->count()],
                ];
            }
        }

        // 3. Upcoming Releases Query
        if (Str::contains($q, ['upcoming release', 'upcoming releases', 'releases', 'drops', 'release radar', 'what releases'])) {
            $releases = Release::upcoming()->take(3)->get();
            if ($releases->isNotEmpty()) {
                $list = $releases->map(function ($rel) {
                    $date = $rel->release_date ? $rel->release_date->format('M d, Y') : 'TBA';
                    $platform = $rel->platform ? " [{$rel->platform}]" : '';
                    return "• **[{$rel->title}](" . route('releases.show', $rel->slug) . ")**{$platform} — Releasing {$date}";
                })->implode("\n");

                $text = "Here are the latest drops scheduled on the Releases Radar:\n\n{$list}\n\nTrack the countdown and explore all upcoming merchandise, games, and anime drops at [/releases](" . route('releases.index') . ").";
                return [
                    'text' => $text,
                    'source' => 'database',
                    'metadata' => ['type' => 'releases', 'count' => $releases->count()],
                ];
            }
        }

        // 4. Character Profiles Query
        if (Str::contains($q, ['characters', 'character profiles', 'who is in', 'lore heroes', 'character roster'])) {
            $characters = Character::active()->take(4)->get();
            if ($characters->isNotEmpty()) {
                $list = $characters->map(function ($ch) {
                    $franchise = $ch->franchise ? " ({$ch->franchise})" : '';
                    return "• **[{$ch->name}](" . route('characters.show', $ch->slug) . ")**{$franchise}";
                })->implode("\n");

                $text = "Here are some of the legendary heroes and characters in our database:\n\n{$list}\n\nDiscover all abilities, biographies, and lore entries at [/characters](" . route('characters.index') . ").";
                return [
                    'text' => $text,
                    'source' => 'database',
                    'metadata' => ['type' => 'characters', 'count' => $characters->count()],
                ];
            }
        }

        // 5. Specific Fandom Content Query (e.g. "show me anime", "gaming lore")
        foreach (['anime', 'gaming', 'movies', 'tv-shows', 'k-pop', 'comics', 'manga', 'cosplay'] as $fandomSlug) {
            if (Str::contains($q, [$fandomSlug, str_replace('-', ' ', $fandomSlug)]) && Str::contains($q, ['content', 'lore', 'articles', 'show', 'recommend', 'tell me about'])) {
                $cat = Category::where('slug', $fandomSlug)->first();
                if ($cat) {
                    $contents = Content::where('category_id', $cat->id)->published()->take(3)->get();
                    if ($contents->isNotEmpty()) {
                        $list = $contents->map(fn($c) => "• **[{$c->title}](" . route('explore.show', $c->slug) . ")** — " . Str::limit($c->description, 70))->implode("\n");
                        $text = "Here are top **{$cat->name}** lore entries from Fan Hub Plus:\n\n{$list}\n\nExplore more in the [{$cat->name} Hub](" . route('fandoms.show', $cat->slug) . ").";
                        return [
                            'text' => $text,
                            'source' => 'database',
                            'metadata' => ['type' => 'fandom_content', 'category' => $cat->name],
                        ];
                    }
                }
            }
        }

        // 6. Platform How-To FAQs
        if (Str::contains($q, ['how to bookmark', 'bookmark content', 'save article', 'how do bookmarks work'])) {
            return [
                'text' => "To bookmark any lore article, character, event, or release drop:\n\n1. Sign in to your Fan Hub Plus account.\n2. Click the **★ Bookmark** button on any content page.\n3. Add optional personal notes (e.g. key lore takeaways or flight plans).\n4. Review and manage all your saved items at [/bookmarks](" . route('bookmarks.index') . ").",
                'source' => 'database',
                'metadata' => ['type' => 'help_bookmarks'],
            ];
        }

        if (Str::contains($q, ['submit fan work', 'fan submission', 'upload cosplay', 'submit art', 'fan collective'])) {
            return [
                'text' => "Fan creators can submit their original fanfiction, cosplay photos, fan art, and music covers to the Fan Collective:\n\n1. Sign in and navigate to [/community/submit](" . route('community.submit') . ").\n2. Select your fandom category, submission type, title, and media link.\n3. Submit for moderation review. Once approved by our team, it will appear in the public Community Showcase!",
                'source' => 'database',
                'metadata' => ['type' => 'help_submission'],
            ];
        }

        if (Str::contains($q, ['toggle theme', 'dark mode', 'light mode', 'switch theme', 'change background'])) {
            return [
                'text' => "You can switch between **Dark Mode** and **Light Mode** at any time by clicking the theme toggle button (moon/sun icon) in the navigation bar. Your visual preference is automatically persisted!",
                'source' => 'database',
                'metadata' => ['type' => 'help_theme'],
            ];
        }

        return null;
    }
}
