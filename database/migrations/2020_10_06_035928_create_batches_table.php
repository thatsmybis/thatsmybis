<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->string('note', 140)->nullable();
            $table->string('type', 8)->nullable();
            $table->bigInteger('guild_id')->unsigned()->nullable();
            $table->bigInteger('member_id')->unsigned()->nullable();
            $table->bigInteger('raid_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('guild_id')->references('id')->on('guilds')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('raid_id')->references('id')->on('raids');
            $table->foreign('user_id')->references('id')->on('users');
        });
        Schema::table('character_items', function (Blueprint $table) {
            $table->bigInteger('batch_id')->nullable()->unsigned()->after('received_at');
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('cascade');
        });
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->bigInteger('batch_id')->nullable()->unsigned()->after('guild_id');
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('cascade');
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
