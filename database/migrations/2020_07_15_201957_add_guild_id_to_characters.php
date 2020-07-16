<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuildIdToCharacters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->bigInteger('guild_id')->unsigned()->nullable()->after('member_id');

            $table->foreign('guild_id')->references('id')->on('guilds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            //
        });
    }
}
