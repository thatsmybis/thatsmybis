<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('description', 500);
            $table->bigInteger('character_id')->unsigned()->nullable();
            $table->bigInteger('guild_id')->unsigned()->nullable();
            $table->bigInteger('instance_id')->unsigned()->nullable();
            $table->mediumInteger('item_id')->unsigned()->nullable();
            $table->bigInteger('item_source_id')->unsigned()->nullable();
            $table->bigInteger('member_id')->unsigned()->nullable();
            $table->bigInteger('raid_id')->unsigned()->nullable();
            $table->integer('role_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('character_id')->references('id')->on('characters');
            $table->foreign('guild_id')->references('id')->on('guilds');
            $table->foreign('instance_id')->references('id')->on('instances');
            $table->foreign('item_id')->references('item_id')->on('items');
            $table->foreign('item_source_id')->references('id')->on('item_sources');
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('raid_id')->references('id')->on('raids');
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_log');
    }
}
