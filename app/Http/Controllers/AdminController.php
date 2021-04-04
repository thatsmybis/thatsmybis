<?php

namespace App\Http\Controllers;

use App\{Guild, Item, User};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    const GUILDS_PER_PAGE = 20;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'seeUser', 'checkAdmin']);
    }

    /**
     * Show the default guild page
     *
     * @return \Illuminate\Http\Response
     */
    public function showGuilds()
    {
        $currentMember = request()->get('currentMember');

        $guilds = Guild::
            select([
                'guilds.*',
                'owner.username',
                'owner.discord_username',
                'owner_member.id AS member_id',
                'owner_member.username AS member_username',
                // DB::raw("(SELECT count(`id`) FROM `batches` WHERE `batches`.`guild_id` = `guilds`.`id`)       AS `batch_count`"),
                // DB::raw("(SELECT count(`id`) FROM `characters` WHERE `characters`.`guild_id` = `guilds`.`id`) AS `character_count`"),
                // DB::raw("(SELECT count(`id`) FROM `members` WHERE `members`.`guild_id` = `guilds`.`id`)       AS `member_count`"),
                // DB::raw("(SELECT count(`id`) FROM `raids` WHERE `raids`.`guild_id` = `guilds`.`id`)           AS `raid_count`"),
                // DB::raw('COUNT(DISTINCT `batches`.`id`)    AS `batch_count`'),
                // DB::raw('COUNT(DISTINCT `characters`.`id`) AS `char_count`'),
                DB::raw('COUNT(DISTINCT `members`.`id`)    AS `member_count`'),
                // DB::raw('COUNT(DISTINCT `raids`.`id`)      AS `raid_count`'),
                // Joins running too slow; need to optimize before using
                // DB::raw("(SELECT count(`id`) FROM `character_items` AS `wishlist_items` WHERE `raids`.`guild_id` = `guilds`.`id`)           AS `raid_count`"),
                // DB::raw('COUNT(DISTINCT `wishlist_items`.`id`) AS `wishlist_item_count`'),
                // DB::raw('COUNT(DISTINCT `prio_items`.`id`)     AS `prio_item_count`'),
                // DB::raw('COUNT(DISTINCT `received_items`.`id`) AS `received_item_count`'),
                // DB::raw('COUNT(DISTINCT `batch_items`.`id`)    AS `batch_item_count`'),
            ])
            // ->leftJoin('batches',                 'batches.guild_id',             '=', 'guilds.id')
            // ->leftJoin('characters',              'characters.guild_id',          '=', 'guilds.id')
            // ->leftJoin('character_items AS wishlist_items', function ($join) {
            //     $join->on('wishlist_items.character_id', 'characters.id')
            //         ->where('wishlist_items.type', Item::TYPE_WISHLIST);
            // })
            // ->leftJoin('character_items AS prio_items', function ($join) {
            //     $join->on('prio_items.character_id', 'characters.id')
            //         ->where('prio_items.type', Item::TYPE_PRIO);
            // })
            // ->leftJoin('character_items AS received_items', function ($join) {
            //     $join->on('received_items.character_id', 'characters.id')
            //         ->where('received_items.type', Item::TYPE_RECEIVED);
            // })
            // ->leftJoin('character_items AS batch_items', function ($join) {
            //     $join->on('batch_items.character_id', 'characters.id')
            //         ->whereNotNull('batch_items.batch_id');
            // })
            ->leftJoin('users AS owner',          'owner.id',                     '=', 'guilds.user_id')
            ->leftJoin('members',                 'members.guild_id',             '=', 'guilds.id')
            ->leftJoin('members AS owner_member', 'owner_member.user_id',         '=', 'owner.id')
            ->leftJoin('raids',                   'raids.guild_id',               '=', 'guilds.id')
            ->orderBy('guilds.name', 'asc')
            ->groupBy('guilds.id')
            ->paginate(self::GUILDS_PER_PAGE);

        return view('admin.guilds', [
            'currentMember' => $currentMember,
            'guilds'        => $guilds,
        ]);
    }

    // showCharacters

    // batch stats

    // item stats (which items are the most wished for, etc.

    // extrapolate wishlist items by class/faction
}
