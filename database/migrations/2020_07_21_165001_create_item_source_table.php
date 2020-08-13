<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemSourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->bigInteger('instance_id')->unsigned()->nullable();
            $table->mediumInteger('npc_id')->unsigned()->nullable();
            $table->mediumInteger('object_id')->unsigned()->nullable();
            $table->integer('order')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('instance_id')->references('id')->on('instances');
        });

        Schema::create('item_item_sources', function (Blueprint $table) {
            $table->id();
            $table->mediumInteger('item_id')->unsigned();
            $table->bigInteger('item_source_id')->unsigned();
            $table->timestamps();

            $table->foreign('item_id')->references('item_id')->on('items')->onDelete('cascade');
            $table->foreign('item_source_id')->references('id')->on('item_sources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_item_sources');
        Schema::dropIfExists('loot_source');
    }
}
