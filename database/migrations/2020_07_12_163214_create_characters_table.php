<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('member_id')->unsigned();
            $table->string('name');
            $table->tinyInteger('level')->nullable();
            $table->string('race')->nullable();
            $table->string('class')->nullable();
            $table->string('spec')->nullable();
            $table->string('rank')->nullable();
            $table->string('rank_goal')->nullable();
            $table->string('public_note')->nullable();
            $table->string('personal_note', 2200)->nullable();
            $table->string('officer_note')->nullable();
            $table->bigInteger('raid_id')->unsigned()->nullable();
            $table->integer('order')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('raid_id')->references('id')->on('raids');
        });

        Schema::create('character_items', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('item_id')->unsigned()->index()->foreign()->references('id')->on('items')->onDelete('cascade');
            $table->bigInteger('character_id')->unsigned()->index()->foreign()->references('id')->on('characters')->onDelete('cascade');
            $table->bigInteger('added_by')->unsigned();
            $table->string('type', 20)->nullable();
            $table->integer('order')->unsigned();

            $table->timestamps();

            $table->foreign('added_by')->references('id')->on('members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('characters');
        Schema::dropIfExists('character_items');
    }
}
