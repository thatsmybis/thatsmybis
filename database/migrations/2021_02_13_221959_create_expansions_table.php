<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

class CreateExpansionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expansions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('name_short', 64);
            $table->string('name_long', 64);
            $table->string('slug', 64);
            $table->boolean('is_enabled')->default(0);
            $table->timestamps();
        });

        DB::insert('INSERT INTO `expansions` (`name`, `name_short`, `name_long`, `slug`, `is_enabled`, `created_at`)
            VALUES
                ("Classic", "Classic", "Classic", "classic", 1, "2021-02-13 00:00:00"),
                ("Burning Crusade", "TBC", "The Burning Crusade", "burning-crusade", 0, "2021-02-13 00:00:00"),
                ("Wrath", "WoTLK", "Wrath of the Lich King", "wrath", 0, "2021-02-13 00:00:00"),
                ("Season of Discovery", "SoD", "Season of Discovery", "season-of-discovery", 0, "2024-01-10 00:00:00"),
                ("Cataclysm", "Cata", "Cataclysm", "cataclysm", 0, "2024-04-08 00:00:00");'),
                ("Mists of Pandaria", "MoP", "Mists of Pandaria", "mop", 0, "2025-05-10 00:00:00");

        Schema::table('guilds', function (Blueprint $table) {
            $table->bigInteger('expansion_id')->default(1)->unsigned()->after('discord_id');
            $table->foreign('expansion_id')->references('id')->on('expansions');
        });
    }

    // The Burning Crusade
    // Wrath of the Lich King
    // Cataclysm
    // Mists of Pandaria
    // Warlords of Draenor
    // Legion
    // Battle for Azeroth
    // Shadowlands

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expansions');
    }
}
