<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserCharacterFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('personal_note', 5000)->nullable()->after('discord_avatar');
            $table->string('officer_note', 1000)->nullable()->after('discord_avatar');
            $table->string('note', 1000)->nullable()->after('discord_avatar');
            $table->string('loot_received', 3000)->nullable()->after('discord_avatar');
            $table->string('wishlist', 1000)->nullable()->after('discord_avatar');
            $table->string('raid_group', 50)->nullable()->after('discord_avatar');
            $table->string('rank_goal', 50)->nullable()->after('discord_avatar');
            $table->string('rank', 50)->nullable()->after('discord_avatar');
            $table->string('alts', 400)->nullable()->after('discord_avatar');
            $table->string('recipes', 1000)->nullable()->after('discord_avatar');
            $table->string('professions', 1000)->nullable()->after('discord_avatar');
            $table->string('spec', 50)->nullable()->after('discord_avatar');
            $table->string('class', 20)->nullable()->after('discord_avatar');
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
