<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewLanguagesToItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('name_ru', 255)->default('')->after('set_id');
            $table->string('name_pt', 255)->default('')->after('set_id');
            $table->string('name_ko', 255)->default('')->after('set_id');
            $table->string('name_it', 255)->default('')->after('set_id');
            $table->string('name_fr', 255)->default('')->after('set_id');
            $table->string('name_es', 255)->default('')->after('set_id');
            $table->string('name_de', 255)->default('')->after('set_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            //
        });
    }
}
