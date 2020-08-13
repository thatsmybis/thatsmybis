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
        //     $table->mediumint('id');
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

        DB::statement("
            CREATE TABLE `items` (
                `item_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
                `name` varchar(255) NOT NULL DEFAULT '',
                `slot` varchar(255) DEFAULT NULL,
                `class` varchar(10) DEFAULT NULL,
                `weight` float(3,2) unsigned DEFAULT NULL,
                `tier` tinyint(3) DEFAULT NULL,
                `type` varchar(10) DEFAULT NULL,
                `profession` varchar(20) DEFAULT NULL,
                `note` varchar(500) DEFAULT NULL,
                `quality` tinyint(3) unsigned NOT NULL DEFAULT '0',
                `description` varchar(255) NOT NULL DEFAULT '',
                `display_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
                `inventory_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
                `allowable_class` mediumint(9) NOT NULL DEFAULT '-1',
                `item_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
                `required_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
                `required_honor_rank` mediumint(8) unsigned NOT NULL DEFAULT '0',
                `set_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`item_id`),
                KEY `items_id` (`item_id`),
                KEY `items_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Item System';
        ");

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
