<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCharacterRaidGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_raid_groups', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('character_id') ->unsigned();
            $table->bigInteger('raid_group_id')->unsigned();
            $table->boolean('is_primary')      ->default(0);
            $table->timestamps();

            $table->foreign('raid_group_id')->references('id')->on('raid_groups');
            $table->foreign('character_id')->references('id')->on('characters');
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
