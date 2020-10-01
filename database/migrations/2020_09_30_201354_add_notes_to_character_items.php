<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesToCharacterItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_items', function (Blueprint $table) {
            $table->string('officer_note', 160)->nullable()->after('order');
            $table->string('note',         160)->nullable()->after('order');
            $table->boolean('is_offspec')->default(0)->after('officer_note');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_items', function (Blueprint $table) {
            //
        });
    }
}
