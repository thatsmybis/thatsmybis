<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpansionIdToInstances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->bigInteger('expansion_id')->nullable()->unsigned()->after('order');
            $table->foreign('expansion_id')->references('id')->on('expansions');
        });
        DB::update('UPDATE `instances` SET `expansion_id` = 1 WHERE `id` < 9;');
        DB::update('UPDATE `instances` SET `expansion_id` = 2 WHERE `id` > 8;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instances', function (Blueprint $table) {
            //
        });
    }
}
