<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRowsToPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            DB::insert('INSERT INTO `permissions` (`inherit_id`, `name`, `slug`, `description`, `role_note`, `created_at`)
            VALUES
                (null, "guild",                   "{\"view\":true,\"edit\":true}",                                                  "Edit guild settings",                                  "guild_master", "2020-07-26 00:00:00"),
                (null, "raids",                   "{\"view\":true,\"edit\":true,\"create\":true,\"disable\":true,\"enable\":true}", "Create and edit raids",                                "officer",      "2020-07-26 00:00:00"),
                (null, "discord-roles",           "{\"view\":true,\"sync\":true}",                                                  "View and sync Discord roles",                          "officer",      "2020-07-26 00:00:00"),
                (null, "characters",              "{\"view\":true,\"edit\":true,\"create\":true,\"inactive\":true,\"loot\":true}",  "Create and edit other people\'s characters",           "officer",      "2020-07-26 00:00:00"),
                (null, "officer-notes",           "{\"view\":true,\"edit\":true,\"create\":true}",                                  "View and edit officer notes",                          "officer",      "2020-07-26 00:00:00"),
                (null, "items",                   "{\"edit\":true}",                                                                "Edit item notes",                                      "officer",      "2020-07-26 00:00:00"),
                (null, "raid-loot",               "{\"view\":true,\"edit\":true,\"create\":true}",                                  "Assign raid loot",                                     "raid_leader",  "2020-07-26 00:00:00"),
                ;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            //
        });
    }
}
