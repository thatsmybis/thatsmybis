<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuildsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guilds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('discord_id')->unsigned();
            $table->bigInteger('admin_role_id')->unsigned()->nullable();
            $table->bigInteger('gm_role_id')->unsigned()->nullable();
            $table->bigInteger('officer_role_id')->unsigned()->nullable();
            $table->bigInteger('raid_leader_role_id')->unsigned()->nullable();
            $table->bigInteger('class_leader_role_id')->unsigned()->nullable();
            $table->string('member_role_ids', 500)->nullable();
            $table->string('calendar_link')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guilds');
    }
}
