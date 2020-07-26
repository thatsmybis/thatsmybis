<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedAtValueToItemItemSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_item_sources', function (Blueprint $table) {
            DB::update('UPDATE `item_item_sources` SET `created_at` = "2020-07-26 00:00:00";');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_item_sources', function (Blueprint $table) {
            //
        });
    }
}
