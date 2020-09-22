<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReceivedToWishlists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_items', function (Blueprint $table) {
            $table->boolean('is_received')->default(0)->after('order')->index();
            $table->timestamp('received_at')->nullable()->after('is_received');
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
