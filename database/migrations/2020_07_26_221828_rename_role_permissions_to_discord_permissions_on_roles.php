<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameRolePermissionsToDiscordPermissionsOnRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            // 'permissions' caused a conflict with a relationship to Permissions class
            $table->renameColumn('permissions', 'discord_permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discord_permissions_on_roles', function (Blueprint $table) {
            //
        });
    }
}
