<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('items', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('name')->unique();
        //     $table->integer('item_id')->nullable()->unique();
        //     $table->string('slot')->nullable();
        //     $table->string('class')->nullable();
        //     $table->string('tier')->nullable();
        //     $table->string('type')->nullable();
        //     $table->string('source')->nullable();
        //     $table->string('profession')->nullable();
        //     $table->string('rarity')->nullable();
        //     $table->timestamps();
        // });

        Schema::create('user_items', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('item_id')->unsigned()->index()->foreign()->references('id')->on('items')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->index()->foreign()->references('id')->on('users')->onDelete('cascade');
            $table->string('type', 20)->nullable();
            $table->integer('order')->unsigned();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
