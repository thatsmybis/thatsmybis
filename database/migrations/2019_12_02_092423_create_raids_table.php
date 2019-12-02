<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raids', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug');
            $table->bigInteger('discord_channel_id')->nullable();
            $table->bigInteger('discord_role_id')->nullable();
            $table->timestamps();
        });

        Schema::table('content', function (Blueprint $table) {
            $table->bigInteger('raid_id')->unsigned()->nullable()->after('user_id');
            $table->foreign('raid_id')->references('id')->on('raids');
        });

        /*
        INSERT INTO `raids` (`name`, `slug`, `discord_channel_id`, `discord_role_id`)
        VALUES
            ("Myth Raid", "myth_raid", 641092767530876928, 640647183087173738),
            ("Night Raid", "night_raid", 639522941138370576, 640647320521670687),
            ("Weekend Raid", "weekend_raid", 641092795922382858, 640647260798976062);
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raids');
    }
}
