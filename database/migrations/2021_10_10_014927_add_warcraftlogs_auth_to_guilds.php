<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarcraftlogsAuthToGuilds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guilds', function (Blueprint $table) {
            $table->timestamp('warcraftlogs_token_expiry')->nullable()->after('expansion_id');
            $table->string('warcraftlogs_refresh_token', 900)->nullable()->after('expansion_id');
            $table->string('warcraftlogs_token', 1100)->nullable()->after('expansion_id');
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
