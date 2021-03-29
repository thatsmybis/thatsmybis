<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTiersToGuildItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guild_items', function (Blueprint $table) {
            $table->string('tier_label', 3)->nullable()->after('updated_by');
            $table->unsignedTinyInteger('tier')->nullable()->after('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guild_items', function (Blueprint $table) {
            //
        });
    }
}
