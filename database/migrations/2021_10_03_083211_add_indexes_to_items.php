<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->index(['expansion_id', 'name']);
            $table->index(['expansion_id', 'name_cn']);
            $table->index(['expansion_id', 'name_de']);
            $table->index(['expansion_id', 'name_es']);
            $table->index(['expansion_id', 'name_fr']);
            $table->index(['expansion_id', 'name_it']);
            $table->index(['expansion_id', 'name_ko']);
            $table->index(['expansion_id', 'name_pt']);
            $table->index(['expansion_id', 'name_ru']);
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
