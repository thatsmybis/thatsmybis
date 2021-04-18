<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewRaidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raids', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)         ->index();
            $table->string('slug', 80)         ->index();
            $table->bigInteger('guild_id')     ->unsigned();
            $table->bigInteger('member_id')    ->unsigned();
            $table->timestamp('date')          ->nullable()->index();
            $table->boolean('is_cancelled')    ->default(0);
            $table->string('public_note', 255) ->nullable();
            $table->string('officer_note', 255)->nullable();
            $table->string('logs', 255)        ->nullable();
            $table->timestamps();

            $table->foreign('guild_id')->references('id')->on('guilds');
            $table->foreign('member_id')->references('id')->on('members');
        });

        Schema::create('raid_raid_groups', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('raid_id')->unsigned();
            $table->bigInteger('raid_group_id')->unsigned();
            $table->timestamps();

            $table->foreign('raid_id')->references('id')->on('raids');
            $table->foreign('raid_group_id')->references('id')->on('raid_groups');
        });

        Schema::create('raid_instances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('raid_id')->unsigned();
            $table->bigInteger('instance_id')->unsigned();
            $table->timestamps();

            $table->foreign('raid_id')->references('id')->on('raids');
            $table->foreign('instance_id')->references('id')->on('instances');
        });

        Schema::create('raid_characters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('raid_id')      ->unsigned();
            $table->bigInteger('character_id') ->unsigned();
            $table->boolean('is_exempt')       ->default(0);
            $table->float('credit', 3, 2)      ->nullable();
            $table->tinyInteger('remark_id')   ->nullable()->index();
            $table->string('public_note', 255) ->nullable();
            $table->string('officer_note', 255)->nullable();
            $table->timestamps();

            $table->foreign('raid_id')->references('id')->on('raids');
            $table->foreign('character_id')->references('id')->on('characters');
        });

        Schema::table('character_items', function (Blueprint $table) {
            $table->bigInteger('raid_id')->unsigned()->nullable()->after('character_id');
            $table->foreign('raid_id')->references('id')->on('raids');
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->bigInteger('raid_id')->unsigned()->nullable()->after('member_id');
            $table->foreign('raid_id')->references('id')->on('raids');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->bigInteger('raid_id')->unsigned()->nullable()->after('other_member_id');
            $table->foreign('raid_id')->references('id')->on('raids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_raids');
    }
}
