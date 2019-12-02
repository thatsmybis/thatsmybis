<?php

use Illuminate\Database\Seeder;

class ConentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $content = [
            [
                'title'          => 'PvP Resources',
                'slug'           => 'pvp',
                'category'        => 0,
                'content'        => '# PvP Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
            ],
            [
                'title'          => 'PvE Resources',
                'slug'           => 'pve',
                'category'        => 0,
                'content'        => '# PvE Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
            ],
            [
                'title'          => 'Druid Resources',
                'slug'           => 'druid',
                'category'        => 0,
                'content'        => '# Druid Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
            ],
            [
                'title'          => 'Hunter Resources',
                'slug'           => 'hunter',
                'category'        => 0,
                'content'        => '# Hunter Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
            ],
            [
                'title'          => 'Mage Resources',
                'slug'           => 'mage',
                'category'        => 0,
                'content'        => '# Mage Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
            ],
            [
                'title'          => 'Priest Resources',
                'slug'           => 'priest',
                'category'        => 0,
                'content'        => '# Priest Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
            ],
            [
                'title'          => 'Rogue Resources',
                'slug'           => 'rogue',
                'category'        => 0,
                'content'        => '# Rogue Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
            ],
            [
                'title'          => 'Shaman Resources',
                'slug'           => 'shaman',
                'category'        => 0,
                'content'        => '# Shaman Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
            ],
            [
                'title'          => 'Warlock Resources',
                'slug'           => 'warlock',
                'category'        => 0,
                'content'        => '# Warlock Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
            ],
            [
                'title'          => 'Warrior Resources',
                'slug'           => 'warrior',
                'category'        => 0,
                'content'        => '# Warrior Resources',
                'user_id'        => 1,
                'last_edited_by' => null,
            ],
            [
                'title'          => 'Use This Website',
                'slug'           => 'use_this_website',
                'category'        => 1,
                'content'        => "We're making this website to help manage all of the static content such as guides that are cluttering up the Discord server, and to help with administration of the guild and the loot council.\n\n**This website will not replace Discord for communications**; all communications are still meant to take place on Discord.",
                'user_id'        => 1,
                'last_edited_by' => null,
            ],
        ];

        foreach ($content as $content) {
            DB::table('content')->insert([
                'title'          => $content['title'],
                'slug'           => $content['slug'],
                'category'        => $content['category'],
                'content'        => $content['content'],
                'user_id'        => $content['user_id'],
                'last_edited_by' => $content['last_edited_by'],
            ]);
        }
    }
}
