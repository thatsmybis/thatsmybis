<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRaidToRaidGroupsEverywhere extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_items', function (Blueprint $table) {
            $table->dropForeign('character_items_raid_id_foreign');
        });
        Schema::table('characters', function (Blueprint $table) {
            $table->dropForeign('characters_raid_id_foreign');
        });
        Schema::table('content', function (Blueprint $table) {
            $table->dropForeign('content_raid_id_foreign');
        });
        Schema::table('batches', function (Blueprint $table) {
            $table->dropForeign('batches_raid_id_foreign');
        });
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign('audit_logs_raid_id_foreign');
        });
        Schema::table('raids', function (Blueprint $table) {
            $table->dropForeign('raids_guild_id_foreign');
            $table->dropForeign('raids_role_id_foreign');
        });

        Schema::rename('raids', 'raid_groups');

        Schema::table('character_items', function (Blueprint $table) {
            $table->renameColumn('raid_id', 'raid_group_id');
            $table->foreign('raid_group_id')->references('id')->on('raid_groups');
            $table->renameIndex('character_items_raid_id_foreign', 'character_items_raid_group_id_foreign');
        });
        Schema::table('characters', function (Blueprint $table) {
            $table->renameColumn('raid_id', 'raid_group_id');
            $table->foreign('raid_group_id')->references('id')->on('raid_groups');
            $table->renameIndex('characters_raid_id_foreign', 'characters_raid_group_id_foreign');
        });
        Schema::table('content', function (Blueprint $table) {
            $table->renameColumn('raid_id', 'raid_group_id');
            $table->foreign('raid_group_id')->references('id')->on('raid_groups');
            $table->renameIndex('content_raid_id_foreign', 'content_raid_group_id_foreign');
        });
        Schema::table('batches', function (Blueprint $table) {
            $table->renameColumn('raid_id', 'raid_group_id');
            $table->foreign('raid_group_id')->references('id')->on('raid_groups');
            $table->renameIndex('batches_raid_id_foreign', 'batches_raid_group_id_foreign');
        });
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->renameColumn('raid_id', 'raid_group_id');
            $table->foreign('raid_group_id')->references('id')->on('raid_groups');
            $table->renameIndex('audit_logs_raid_id_foreign', 'audit_logs_raid_group_id_foreign');
        });

        Schema::table('raid_groups', function (Blueprint $table) {
            $table->foreign('guild_id')->references('id')->on('guilds');
            $table->foreign('role_id')->references('id')->on('roles');

            $table->renameIndex('raids_disabled_at_index', 'raid_groups_disabled_at_index');
            $table->renameIndex('raids_guild_id_foreign', 'raid_groups_guild_id_foreign');
            $table->renameIndex('raids_role_id_foreign', 'raid_groups_role_id_foreign');
            $table->renameIndex('raids_slug_index', 'raid_groups_slug_index');
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
