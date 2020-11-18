<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAuditLogTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('type', 9)->nullable()->after('description')->change();
        });

        DB::update('UPDATE `audit_logs` SET `type` = "assign"    WHERE `description` LIKE "%assigned item to character%";');

        DB::update('UPDATE `audit_logs` SET `type` = "wishlist"  WHERE `description` = "System removed 1 wishlist item after character was assigned item";');
        DB::update('UPDATE `audit_logs` SET `type` = "wishlist"  WHERE `description` = "System flagged 1 wishlist item as received after character was assigned item";');

        DB::update('UPDATE `audit_logs` SET `type` = "prio"      WHERE `description` = "System removed 1 prio  after character was assigned item";');
        DB::update('UPDATE `audit_logs` SET `type` = "prio"      WHERE `description` = "System flagged 1 prio as received after character was assigned item";');

        DB::update('UPDATE `audit_logs` SET `type` = "item_note" WHERE `description` LIKE "%item note/priority";');
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
