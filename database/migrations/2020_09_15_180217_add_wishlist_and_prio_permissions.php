<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWishlistAndPrioPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guilds', function (Blueprint $table) {
            $table->boolean('is_wishlist_private')->default(0)->after('calendar_link');
            $table->boolean('is_prio_private')->default(0)->after('calendar_link');
        });

        DB::insert('INSERT INTO `permissions` (`inherit_id`, `name`, `slug`, `description`, `role_note`, `created_at`)
            VALUES (null, "wishlists", "{\"view\":true}", "View other peoples\' wishlists", "raid_leader", "2020-09-15 00:00:00");');

        DB::update('UPDATE `permissions` SET `slug` = "{\"view\":true,\"edit\":true}", `description` = "View and edit priorities for items" WHERE `name` = "prios";');

        DB::update('UPDATE `permissions` SET `description` = "Create and edit other peoples\' characters and loot" WHERE `name` = "characters";');
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
