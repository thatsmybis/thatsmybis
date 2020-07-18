<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariousNameKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guilds', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->index('username');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('username');
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
