<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuditorRoleToGuilds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guilds', function (Blueprint $table) {
            $table->bigInteger('auditor_role_id')->unsigned()->nullable()->after('raid_leader_role_id');
        });

        DB::insert('INSERT INTO `permissions` (`inherit_id`, `name`, `slug`, `description`, `role_note`, `created_at`)
        VALUES
            (null, "prios",     "{\"view\":true}", "View priorities for items",      "auditor", "2021-09-06 00:00:00"),
            (null, "wishlists", "{\"view\":true}", "View other peoples\' wishlists", "auditor", "2021-09-06 00:00:00");');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guilds', function (Blueprint $table) {
            //
        });
    }
}
