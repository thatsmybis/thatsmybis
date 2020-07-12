<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call([ContentTableSeeder::class]);
        // $this->seedContentTable();
        $this->seedUsersTable();
    }

    private function seedUsersTable() {
        $faker = Faker::create();

        $specs = ['holy', 'frost', 'prot', 'arcane', 'resto', 'daggers', 'sword', 'enhancement'];
        $races = ['orc', 'tauren', 'undead', 'troll'];

        for ($i = 0; $i < 5; $i++) {
            DB::table('users')->insert([
                'username'         => $faker->firstName,
                'email'            => $faker->unique()->safeEmail,
                'discord_username' => $faker->name . $faker->numberBetween(1000, 9999),
                'discord_id'       => Str::random(10),
                'password'         => null,
                'spec'             => $specs[array_rand($specs)],
                'alts'             => '60 ' . $races[array_rand($races)] . ' ' . $classes[array_rand($classes)],
                'rank'             => $faker->numberBetween(1,14),
                'rank_goal'        => $faker->numberBetween(1,14),
                'public_note'      => $faker->sentences(3, true),
                'officer_note'     => $faker->sentences(3, true),
                'personal_note'    => $faker->sentences(3, true),
            ]);
        }
    }

    // For whatever reason, it couldn't find the class to run this so I'm just hacking it like so:
    private function seedContentTable() {
        $content = [
            [
                'title'          => 'PvP Resources',
                'slug'           => 'pvp',
                'is_news'        => 0,
                'content'        => '# PvP Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
                'created_at'     => '2019-11-20 00:00:00',
            ],
            [
                'title'          => 'PvE Resources',
                'slug'           => 'pve',
                'is_news'        => 0,
                'content'        => '# PvE Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
                'created_at'     => '2019-11-20 00:00:00',
            ],
            [
                'title'          => 'Druid Resources',
                'slug'           => 'druid',
                'is_news'        => 0,
                'content'        => '# Druid Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
                'created_at'     => '2019-11-20 00:00:00',
            ],
            [
                'title'          => 'Hunter Resources',
                'slug'           => 'hunter',
                'is_news'        => 0,
                'content'        => '# Hunter Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
                'created_at'     => '2019-11-20 00:00:00',
            ],
            [
                'title'          => 'Mage Resources',
                'slug'           => 'mage',
                'is_news'        => 0,
                'content'        => '# Mage Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
                'created_at'     => '2019-11-20 00:00:00',
            ],
            [
                'title'          => 'Priest Resources',
                'slug'           => 'priest',
                'is_news'        => 0,
                'content'        => '# Priest Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
                'created_at'     => '2019-11-20 00:00:00',
            ],
            [
                'title'          => 'Rogue Resources',
                'slug'           => 'rogue',
                'is_news'        => 0,
                'content'        => '# Rogue Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
                'created_at'     => '2019-11-20 00:00:00',
            ],
            [
                'title'          => 'Shaman Resources',
                'slug'           => 'shaman',
                'is_news'        => 0,
                'content'        => '# Shaman Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
                'created_at'     => '2019-11-20 00:00:00',
            ],
            [
                'title'          => 'Warlock Resources',
                'slug'           => 'warlock',
                'is_news'        => 0,
                'content'        => '# Warlock Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
                'created_at'     => '2019-11-20 00:00:00',
            ],
            [
                'title'          => 'Warrior Resources',
                'slug'           => 'warrior',
                'is_news'        => 0,
                'content'        => '# Warrior Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
                'created_at'     => '2019-11-20 00:00:00',
            ],
            [
                'title'          => 'Use This Website',
                'slug'           => 'use_this_website',
                'is_news'        => 1,
                'content'        => 'We\'re making this website to help manage all of the static content such as guides that are cluttering up the Discord server, and to help with administration of the guild and the loot council.

**This website will not replace Discord for communications**; all communications are still meant to take place on Discord.

<iframe src="http://www.strawpoll.me/embed_1/18889845/r" style="width:680px;height:541px;border:0;">Loading poll...</iframe>',
                'user_id'        => 1,
                'last_edited_by' => null,
                'created_at'     => '2019-11-20 00:00:00',
            ],
        ];

        foreach ($content as $content) {
            DB::table('content')->insert([
                'title'          => $content['title'],
                'slug'           => $content['slug'],
                'is_news'        => $content['is_news'],
                'content'        => $content['content'],
                'user_id'        => $content['user_id'],
                'last_edited_by' => $content['last_edited_by'],
                'created_at'     => $content['created_at'],
            ]);
        }
    }
}
