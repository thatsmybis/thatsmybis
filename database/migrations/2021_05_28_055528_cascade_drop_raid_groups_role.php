<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;


class CascadeDropRaidGroupsRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raid_groups', function (Blueprint $table) {
            // If your migration is failing here, it's due to a previous migration that didn't play nice.
            // Just comment out L#22 and uncomment L#21... sorry about that.
            // $table->dropForeign('raids_role_id_foreign');
            $table->dropForeign('raid_groups_role_id_foreign');
        });

        Schema::table('raid_groups', function (Blueprint $table) {
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('set null');
        });
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
