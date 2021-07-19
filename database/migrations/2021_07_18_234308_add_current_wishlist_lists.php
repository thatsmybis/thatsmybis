<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentWishlistLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_items', function (Blueprint $table) {
            $table->tinyInteger('list_number')->unsigned()->default(1)->after('received_at');
        });
        Schema::table('guilds', function (Blueprint $table) {
            $table->tinyInteger('current_wishlist_number')->unsigned()->default(1)->after('max_wishlist_items');
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
