<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnDeleteSetNullToRoleForeignKeysAndVariousIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raids', function (Blueprint $table) {
            $table->dropForeign('raids_role_id_foreign');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->dropForeign('characters_raid_id_foreign');
            $table->foreign('raid_id')->references('id')->on('raids')->onDelete('set null');
        });

        Schema::table('character_items', function (Blueprint $table) {
            $table->index('type');
        });

        Schema::table('guilds', function (Blueprint $table) {
            $table->index('slug');
        });

        Schema::table('instances', function (Blueprint $table) {
            $table->index('slug');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->index('banned_at');
        });

        Schema::table('raids', function (Blueprint $table) {
            $table->index('slug');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->index('discord_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
