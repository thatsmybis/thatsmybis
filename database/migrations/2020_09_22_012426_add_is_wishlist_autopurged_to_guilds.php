<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsWishlistAutopurgedToGuilds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guilds', function (Blueprint $table) {
            $table->boolean('is_prio_autopurged')->default(0)->after('is_wishlist_locked');
            $table->boolean('is_wishlist_autopurged')->default(0)->after('is_prio_autopurged');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guilds', function (Blueprint $table) {
            //
        });
    }
}
