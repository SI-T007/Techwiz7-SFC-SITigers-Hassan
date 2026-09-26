<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Character;
use App\Models\Content;
use App\Models\Genre;
use App\Models\Release;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnimeSeeder extends Seeder
{
    public function run(): void
    {
        $cat = Category::firstOrCreate(
            ['slug' => 'anime'],
            [
                'name' => 'Anime',
                'description' => 'Legendary shonen classics, seasonal sensations, and world-class Japanese animation.',
                'icon' => 'bi-tv',
                'status' => 'active',
                'sort_order' => 1,
            ]
        );

        $shonen = Genre::firstOrCreate(['slug' => 'shonen'], ['name' => 'Shonen']);
        $action = Genre::firstOrCreate(['slug' => 'action'], ['name' => 'Action']);
        $fantasy = Genre::firstOrCreate(['slug' => 'fantasy'], ['name' => 'Fantasy']);
        $supernatural = Genre::firstOrCreate(['slug' => 'supernatural'], ['name' => 'Supernatural']);

        $user = User::first() ?? User::create([
            'name' => 'Anime Archon',
            'email' => 'anime@fanhub.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $animeList = [
            [
                'title' => 'Jujutsu Kaisen: Shibuya Incident',
                'slug' => 'jujutsu-kaisen-shibuya-incident',
                'description' => 'Special grade curses trap the city of Shibuya in a veil, forcing Satoru Gojo and jujutsu sorcerers into an all-out apocalyptic battle.',
                'body' => 'The Shibuya Incident marks the pivotal clash between the Jujutsu Sorcerers and Pseudo-Geto with the Disaster Curses.',
                'type' => 'video',
                'status' => 'published',
                'is_featured' => true,
                'popularity' => 98,
                'views' => 4500,
                'release_date' => '2024-01-10',
                'thumbnail' => 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=1200&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Attack on Titan: The Final Chapters',
                'slug' => 'attack-on-titan-final-chapters',
                'description' => 'The Rumbling marches across the world. The remaining scouts and warriors unite in a desperate struggle to halt Eren Jaeger.',
                'body' => 'The concluding saga of humanity struggle against titans and the existential conflict between Eldia and the world.',
                'type' => 'article',
                'status' => 'published',
                'is_featured' => true,
                'popularity' => 99,
                'views' => 6200,
                'release_date' => '2023-11-04',
                'thumbnail' => 'https://images.unsplash.com/photo-1563089145-599997674d42?w=1200&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Demon Slayer: Hashira Training Arc',
                'slug' => 'demon-slayer-hashira-training',
                'description' => 'Tanjiro undergoes rigorous physical and swordsmanship conditioning under the Hashira in preparation for the battle at the Infinity Castle.',
                'body' => 'Mastering breathing techniques and stamina under Stone Hashira Gyomei Himejima and Wind Hashira Sanemi Shinazugawa.',
                'type' => 'video',
                'status' => 'published',
                'is_featured' => false,
                'popularity' => 94,
                'views' => 3800,
                'release_date' => '2024-05-12',
                'thumbnail' => 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=1200&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Solo Leveling: Arise & Shadows',
                'slug' => 'solo-leveling-arise-shadows',
                'description' => 'Sung Jinwoo awakens a mysterious leveling system granting him infinite growth, transcending from E-rank hunter to the Shadow Monarch.',
                'body' => 'The world changed when the Gates opened. Hunter Sung Jinwoo commands the fallen shadow soldiers with the command ARISE.',
                'type' => 'article',
                'status' => 'published',
                'is_featured' => false,
                'popularity' => 96,
                'views' => 5100,
                'release_date' => '2024-03-30',
                'thumbnail' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=1200&auto=format&fit=crop&q=80',
            ]
        ];

        foreach ($animeList as $item) {
            $content = Content::firstOrCreate(
                ['slug' => $item['slug']],
                array_merge($item, [
                    'category_id' => $cat->id,
                    'user_id' => $user->id,
                    'published_at' => now(),
                ])
            );
            $content->genres()->syncWithoutDetaching([$shonen->id, $action->id, $fantasy->id, $supernatural->id]);
        }

        // Add Characters
        Character::firstOrCreate(
            ['slug' => 'satoru-gojo'],
            [
                'name' => 'Satoru Gojo',
                'franchise' => 'Jujutsu Kaisen',
                'category_id' => $cat->id,
                'abilities' => 'Limitless, Six Eyes, Infinity, Hollow Purple, Unlimited Void',
                'biography' => 'The strongest jujutsu sorcerer of modern times. Satoru Gojo wields the inherited Six Eyes and Limitless technique.',
                'is_featured' => true,
                'status' => 'active',
            ]
        );

        Character::firstOrCreate(
            ['slug' => 'sung-jinwoo'],
            [
                'name' => 'Sung Jinwoo',
                'franchise' => 'Solo Leveling',
                'category_id' => $cat->id,
                'abilities' => 'Shadow Extraction, Monarch Domain, Ruler Reach, Dragon Fear',
                'biography' => 'The Shadow Monarch and greatest hunter in the world, capable of extracting and commanding the shadows of defeated foes.',
                'is_featured' => true,
                'status' => 'active',
            ]
        );

        // Add Upcoming Release
        Release::firstOrCreate(
            ['slug' => 'chainsaw-man-reze-arc-movie'],
            [
                'title' => 'Chainsaw Man: Reze Arc Movie',
                'category_id' => $cat->id,
                'description' => 'Denji encounters the mysterious bomb devil hybrid Reze in a high-octane theatrical cinematic experience.',
                'release_date' => now()->addMonths(2),
                'platform' => 'Theatrical & Streaming',
                'status' => 'upcoming',
            ]
        );
    }
}
