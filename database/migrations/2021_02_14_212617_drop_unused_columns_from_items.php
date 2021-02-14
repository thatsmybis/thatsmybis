<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUnusedColumnsFromItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('slot');
            $table->dropColumn('class');
            $table->dropColumn('tier');
            $table->dropColumn('type');
            $table->dropColumn('profession');
            $table->dropColumn('note');
            $table->dropColumn('description');
            $table->dropColumn('required_honor_rank');
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
