<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TestDbAccessPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->bigInteger('raid_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('raid_id')->references('id')->on('raids');
        });
        Schema::table('test', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->unsigned()->after('raid_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
        Schema::table('test', function (Blueprint $table) {
            $table->string('name', 50)->change();
        });

        Schema::drop('test');
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
