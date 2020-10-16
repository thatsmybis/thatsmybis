<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImportIdToCharacterItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_items', function (Blueprint $table) {
            $table->string('import_id', 20)->nullable()->after('received_at');

            // Contemplated putting a composite unique key into the DB, but decided to let server validation handle it instead
            // $table->unique(['guild_id', 'import_id']);
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
