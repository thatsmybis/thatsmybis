<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertPrioPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::insert('INSERT INTO `permissions` (`inherit_id`, `name`, `slug`, `description`, `role_note`, `created_at`)
            VALUES
                (null, "prios", "{\"edit\":true}", "Edit priorities for items", "raid_leader", "2020-08-18 00:00:00");');
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
