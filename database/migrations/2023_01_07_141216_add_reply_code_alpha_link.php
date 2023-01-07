<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

class EnableWotlk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //UPDATE parent ID linking for Reply Code Alpha - 25 man
        DB::update('UPDATE `items` SET `parent_id` = NULL, `parent_item_id` = NULL WHERE `item_id` = 46053 AND `expansion_id` = 3;');
        DB::update('UPDATE `items` SET `parent_id` = (SELECT * FROM (SELECT `id` FROM `items` WHERE `item_id` = 46053 LIMIT 1) as haxxorz), `parent_item_id` = 46053 WHERE `item_id` = 45588 AND `expansion_id` = 3;');
        DB::update('UPDATE `items` SET `parent_id` = (SELECT * FROM (SELECT `id` FROM `items` WHERE `item_id` = 46053 LIMIT 1) as haxxorz), `parent_item_id` = 46053 WHERE `item_id` = 45608 AND `expansion_id` = 3;'); 
        DB::update('UPDATE `items` SET `parent_id` = (SELECT * FROM (SELECT `id` FROM `items` WHERE `item_id` = 46053 LIMIT 1) as haxxorz), `parent_item_id` = 46053 WHERE `item_id` = 45618 AND `expansion_id` = 3;');
        DB::update('UPDATE `items` SET `parent_id` = (SELECT * FROM (SELECT `id` FROM `items` WHERE `item_id` = 46053 LIMIT 1) as haxxorz), `parent_item_id` = 46053 WHERE `item_id` = 45614 AND `expansion_id` = 3;');

        //UPDATE parent ID linking for Reply Code Alpha - 10 man
        DB::update('UPDATE `items` SET `parent_id` = NULL, `parent_item_id` = NULL WHERE `item_id` = 46052 AND `expansion_id` = 3;');
        DB::update('UPDATE `items` SET `parent_id` = (SELECT * FROM (SELECT `id` FROM `items` WHERE `item_id` = 46052 LIMIT 1) as haxxorz), `parent_item_id` = 46052 WHERE `item_id` = 46320 AND `expansion_id` = 3;');
        DB::update('UPDATE `items` SET `parent_id` = (SELECT * FROM (SELECT `id` FROM `items` WHERE `item_id` = 46052 LIMIT 1) as haxxorz), `parent_item_id` = 46052 WHERE `item_id` = 46322 AND `expansion_id` = 3;');
        DB::update('UPDATE `items` SET `parent_id` = (SELECT * FROM (SELECT `id` FROM `items` WHERE `item_id` = 46052 LIMIT 1) as haxxorz), `parent_item_id` = 46052 WHERE `item_id` = 46321 AND `expansion_id` = 3;');
        DB::update('UPDATE `items` SET `parent_id` = (SELECT * FROM (SELECT `id` FROM `items` WHERE `item_id` = 46052 LIMIT 1) as haxxorz), `parent_item_id` = 46052 WHERE `item_id` = 46323 AND `expansion_id` = 3;');
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