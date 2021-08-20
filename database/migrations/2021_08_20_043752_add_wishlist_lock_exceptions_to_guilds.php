<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWishlistLockExceptionsToGuilds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guilds', function (Blueprint $table) {
            $table->string('wishlist_locked_exceptions', 60)->nullable()->after('is_wishlist_locked');
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
