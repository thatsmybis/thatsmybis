<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertItemSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 20 and 40 man bosses
        DB::insert('INSERT INTO `item_sources` (`name`, `slug`, `instance_id`, `npc_id`, `object_id`, `order`, `created_at`)
            VALUES
                ("Recipes",      "recipes",      1, null,  null, 1, "2020-07-21 00:00:00"),
                ("Trash",        "trash",        1, null,  null, 2, "2020-07-21 00:00:00"),
                ("Shared",       "shared",       1, null,  null, 3, "2020-07-21 00:00:00"),
                ("Lucifron",     "lucifron",     1, 12118, null, 4, "2020-07-21 00:00:00"),
                ("Magmadar",     "magmadar",     1, 11982, null, 5, "2020-07-21 00:00:00"),
                ("Gehennas",     "gehennas",     1, 12259, null, 6, "2020-07-21 00:00:00"),
                ("Garr",         "garr",         1, 12057, null, 7, "2020-07-21 00:00:00"),
                ("Shazzrah",     "shazzrah",     1, 12264, null, 8, "2020-07-21 00:00:00"),
                ("Baron Geddon", "baron-geddon", 1, 12056, null, 9, "2020-07-21 00:00:00"),
                ("Golemagg",     "golemagg",     1, 11988, null, 10, "2020-07-21 00:00:00"),
                ("Sulfuron",     "sulfuron",     1, 12098, null, 11, "2020-07-21 00:00:00"),
                ("Majordomo",    "majordomo",    1, 12018, null, 12, "2020-07-21 00:00:00"),
                ("Ragnaros",     "ragnaros",     1, 11502, null, 13, "2020-07-21 00:00:00"),

                ("Onyxia", "onyxia", 2, 10184, null, 1, "2020-07-21 00:00:00"),

                ("Trash",             "trash",             3, 12459, null, 1, "2020-07-21 00:00:00"),
                ("Razorgore",         "razorgore",         3, 12435, null, 2, "2020-07-21 00:00:00"),
                ("Vaelastrasz",       "vaelastrasz",       3, 13020, null, 3, "2020-07-21 00:00:00"),
                ("Broodlord",         "broodlord",         3, 12017, null, 4, "2020-07-21 00:00:00"),
                ("Firemaw",           "firemaw",           3, 11983, null, 5, "2020-07-21 00:00:00"),
                ("Ebonroc",           "ebonroc",           3, 14601, null, 6, "2020-07-21 00:00:00"),
                ("Flamegor",          "flamegor",          3, 11981, null, 7, "2020-07-21 00:00:00"),
                ("Drake Shared Loot", "drake-shared-loot", 3, 11983, null, 8, "2020-07-21 00:00:00"),
                ("Chromaggus",        "chromaggus",        3, 14020, null, 9, "2020-07-21 00:00:00"),
                ("Nefarian",          "nefarian",          3, 11583, null, 10, "2020-07-21 00:00:00"),

                ("Tokens",          "tokens",          4, null, null, 1, "2020-07-21 00:00:00"),
                ("Trash",           "trash",           4, null, null, 2, "2020-07-21 00:00:00"),
                ("Shared",          "shared",          4, null, null, 3, "2020-07-21 00:00:00"),
                ("Jeklik",          "jeklik",          4, 14517, null, 4, "2020-07-21 00:00:00"),
                ("Venoxis",         "venoxis",         4, 14507, null, 5, "2020-07-21 00:00:00"),
                ("Mar\'li",         "marli",           4, 14510, null, 6, "2020-07-21 00:00:00"),
                ("Bloodlord",       "bloodlord",       4, 11382, null, 7, "2020-07-21 00:00:00"),
                ("Edge of Madness", "edge-of-madness", 4, 15083, null, 8, "2020-07-21 00:00:00"),
                ("Thekal",          "thekal",          4, 14509, null, 9, "2020-07-21 00:00:00"),
                ("Gahz\'ranka",     "gahzranka",       4, 15114, null, 10, "2020-07-21 00:00:00"),
                ("Arlokk",          "arlokk",          4, 14515, null, 11, "2020-07-21 00:00:00"),
                ("Jin\'do",         "jindo",           4, 11380, null, 12, "2020-07-21 00:00:00"),
                ("Hakkar",          "hakkar",          4, 14834, null, 13, "2020-07-21 00:00:00"),

                ("Enchants",       "enchants",       5, null, null, 1, "2020-07-21 00:00:00"),
                ("Tokens",         "tokens",         5, null, null, 2, "2020-07-21 00:00:00"),
                ("Trash",          "trash",          5, null, null, 3, "2020-07-21 00:00:00"),
                ("Kurinnaxx",      "kurinnaxx",      5, 15348, null, 4, "2020-07-21 00:00:00"),
                ("General Rajaxx", "general-rajaxx", 5, 15341, null, 5, "2020-07-21 00:00:00"),
                ("Moam",           "moam",           5, 15340, null, 6, "2020-07-21 00:00:00"),
                ("Buru",           "buru",           5, 15370, null, 7, "2020-07-21 00:00:00"),
                ("Ayamiss",        "ayamiss",        5, 15369, null, 8, "2020-07-21 00:00:00"),
                ("Ossirian",       "ossirian",       5, 15339, null, 9, "2020-07-21 00:00:00"),

                ("Mounts",        "mounts",        6, 21321, null, 1, "2020-07-21 00:00:00"),
                ("Enchants",      "enchants",      6, null, null, 2, "2020-07-21 00:00:00"),
                ("Tokens",        "tokens",        6, null, null, 3, "2020-07-21 00:00:00"),
                ("Trash",         "trash",         6, null, null, 4, "2020-07-21 00:00:00"),
                ("Skeram",        "skeram",        6, 15263, null, 5, "2020-07-21 00:00:00"),
                ("Bug Trio",      "bug-trio",      6, 15543, null, 6, "2020-07-21 00:00:00"),
                ("Sartura",       "sartura",       6, 15543, null, 7, "2020-07-21 00:00:00"),
                ("Fankriss",      "fankriss",      6, 15510, null, 8, "2020-07-21 00:00:00"),
                ("Viscidus",      "viscidus",      6, 15299, null, 9, "2020-07-21 00:00:00"),
                ("Huhuran",       "huhuran",       6, 15509, null, 10, "2020-07-21 00:00:00"),
                ("Twin Emperors", "twin-emperors", 6, 15276, null, 11, "2020-07-21 00:00:00"),
                ("Ouro",          "ouro",          6, 15517, null, 12, "2020-07-21 00:00:00"),
                ("C\'Thun",       "cthun",         6, 15517, null, 13, "2020-07-21 00:00:00"),

                ("Tokens",            "tokens",            7, null, null, 1, "2020-07-21 00:00:00"),
                ("Trash",             "trash",             7, null, null, 2, "2020-07-21 00:00:00"),
                ("Anub\'Rekhan",      "anubrekhan",        7, 15956, null, 3, "2020-07-21 00:00:00"),
                ("Faerlina",          "faerlina",          7, 15953, null, 4, "2020-07-21 00:00:00"),
                ("Maexxna",           "maexxna",           7, 15952, null, 5, "2020-07-21 00:00:00"),
                ("Noth",              "noth",              7, 15954, null, 6, "2020-07-21 00:00:00"),
                ("Heigan",            "heigan",            7, 15936, null, 7, "2020-07-21 00:00:00"),
                ("Loatheb",           "loatheb",           7, 16011, null, 8, "2020-07-21 00:00:00"),
                ("Razuvious",         "razuvious",         7, 16061, null, 9, "2020-07-21 00:00:00"),
                ("Gothik",            "gothik",            7, 16060, null, 10, "2020-07-21 00:00:00"),
                ("The Four Horsemen", "the-four-horsemen", 7, null, 181366, 11, "2020-07-21 00:00:00"),
                ("Patchwerk",         "patchwerk",         7, 16028, null, 12, "2020-07-21 00:00:00"),
                ("Grobbulus",         "grobbulus",         7, 15931, null, 13, "2020-07-21 00:00:00"),
                ("Gluth",             "Gluth",             7, 15932, null, 14, "2020-07-21 00:00:00"),
                ("Thaddius",          "thaddius",          7, 15928, null, 15, "2020-07-21 00:00:00"),
                ("Sapphiron",         "sapphiron",         7, 15989, null, 16, "2020-07-21 00:00:00"),
                ("Kel\'Thuzad",       "kelthuzad",         7, 15990, null, 17, "2020-07-21 00:00:00");');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
