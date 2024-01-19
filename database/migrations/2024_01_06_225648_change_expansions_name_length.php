<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeExpansionsNameLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('expansions', function (Blueprint $table) {
            $table->string('name', 64)->change();
            $table->string('name_short', 64)->change();
            $table->string('name_long', 64)->change();
            $table->string('slug', 64)->change();
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
