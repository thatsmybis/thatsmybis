<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlagsToCharacters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->bigInteger('member_id')->unsigned()->nullable()->change();
            $table->string('profession_2')->nullable()->change();
            $table->string('profession_1')->nullable()->change();

            $table->date('removed_at')->nullable()->after('order');
            $table->date('hidden_at')->nullable()->after('order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            //
        });
    }
}
