<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateContentTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('content', function (Blueprint $table) {
            $table->bigInteger('member_id')->unsigned()->nullable()->after('content');
            $table->bigInteger('guild_id')->unsigned()->nullable()->after('content');

            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('guild_id')->references('id')->on('guilds');

            $table->dropForeign('content_last_edited_by_foreign');
            $table->foreign('last_edited_by')->references('id')->on('members');

            $table->dropForeign('content_user_id_foreign');
            $table->dropColumn('user_id');
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
