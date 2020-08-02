<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherMemberToAuditLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->bigInteger('other_member_id')->unsigned()->nullable()->after('member_id');

            $table->foreign('other_member_id')->references('id')->on('members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            //
        });
    }
}
