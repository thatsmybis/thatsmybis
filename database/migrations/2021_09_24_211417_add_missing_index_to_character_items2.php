<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingIndexToCharacterItems2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_items2', function (Blueprint $table) {
            $table->index('list_number');
        });
        Schema::table('guilds', function (Blueprint $table) {
            $table->index('current_wishlist_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_items2', function (Blueprint $table) {
            //
        });
    }
}
