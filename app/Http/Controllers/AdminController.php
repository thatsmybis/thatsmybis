<?php

namespace App\Http\Controllers;

use App\{Guild, Item, User};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    const GUILDS_PER_PAGE = 20;
    const USERS_PER_PAGE  = 20;

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

        $query = Guild::
            select([
                'guilds.*',
                'owner.username',
                'owner.discord_username',
                'owner_member.id AS member_id',
                'owner_member.username AS member_username',
                // DB::raw("(SELECT count(`id`) FROM `batches` WHERE `batches`.`guild_id` = `guilds`.`id`)       AS `batch_count`"),
                // DB::raw("(SELECT count(`id`) FROM `characters` WHERE `characters`.`guild_id` = `guilds`.`id`) AS `character_count`"),
                // DB::raw("(SELECT count(`id`) FROM `members` WHERE `members`.`guild_id` = `guilds`.`id`)       AS `member_count`"),
                // DB::raw("(SELECT count(`id`) FROM `raid_groups` WHERE `raid_groups`.`guild_id` = `guilds`.`id`) AS `raid_group_count`"),
                // DB::raw('COUNT(DISTINCT `batches`.`id`)    AS `batch_count`'),
                // DB::raw('COUNT(DISTINCT `characters`.`id`) AS `char_count`'),
                DB::raw('COUNT(DISTINCT `members`.`id`)    AS `member_count`'),
                // DB::raw('COUNT(DISTINCT `raid_groups`.`id`)      AS `raid_group_count`'),
                // Joins running too slow; need to optimize before using
                // DB::raw("(SELECT count(`id`) FROM `character_items` AS `wishlist_items` WHERE `raid_groups`.`guild_id` = `guilds`.`id`) AS `raid_group_count`"),
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
            ->leftJoin('raid_groups',             'raid_groups.guild_id',         '=', 'guilds.id')
            ->groupBy('guilds.id');

        // These both require joining on members
        if (!empty(request()->input('member_name')) || (!empty(request()->input('discord_username')))) {
            $query = $query->join('members AS req_members', 'req_members.guild_id', 'guilds.id');
        }

        if (!empty(request()->input('character_name'))) {
            $query = $query->join('characters', 'characters.guild_id', 'guilds.id')
                ->where('characters.name', 'like', '%' . request()->input('character_name') . '%');
        }

        if (!empty(request()->input('guild_name'))) {
            $query = $query->where('guilds.name', 'like', '%' . request()->input('guild_name') . '%');
        }

        if (!empty(request()->input('discord_username'))) {
            $query = $query->join('users', 'users.id', 'req_members.user_id')
                ->where('users.discord_username', 'like', '%' . request()->input('discord_username') . '%');
        }

        if (!empty(request()->input('member_name'))) {
            $query = $query->where('req_members.username', 'like', '%' . request()->input('member_name') . '%');
        }

        if (!empty(request()->input('order_by'))) {
            $query = $query->orderBy(request()->input('order_by'), 'desc');
        } else {
            $query = $query->orderBy('guilds.name', 'asc');
        }

        $guilds = $query->paginate(self::GUILDS_PER_PAGE);

        return view('admin.guilds', [
            'currentMember' => $currentMember,
            'guilds'        => $guilds,
        ]);
    }

    // showCharacters

    // batch stats

    // item stats (which items are the most wished for, etc.

    // extrapolate wishlist items by class/faction

    /**
     * Show a user for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function showEditUser($id)
    {
        $currentMember = request()->get('currentMember');

        $user = User::select('*')->findOrFail($id);
        $user->load(['members', 'members.guild']);

        return view('admin.users.edit', [
            'currentMember' => $currentMember,
            'user'          => $user,
        ]);
    }

    /**
     * Show all users, paginated
     *
     * @return \Illuminate\Http\Response
     */
    public function showUsers()
    {
        $currentMember = request()->get('currentMember');

        $query = DB::table('users');

        if (!empty(request()->input('discord_username'))) {
            $query = $query->where('users.discord_username', 'like', '%' . request()->input('discord_username') . '%');
        }

        if (!empty(request()->input('order_by'))) {
            $query = $query->orderBy(request()->input('order_by'), 'desc');
        } else {
            $query = $query->orderBy('users.discord_username', 'asc');
        }

        $users = $query->paginate(self::USERS_PER_PAGE);

        return view('admin.users.list', [
            'currentMember' => $currentMember,
            'users'         => $users,
        ]);
    }

    /**
     * List items, get the translations from wowhead
     *
     * @return \Illuminate\Http\Response
     */
    public function showTranslateItems()
    {
        $expansion = request()->input('expansion') ? request()->input('expansion') : 'classic';
        $lang = request()->input('lang') ? request()->input('lang') : 'en';
        $maxId = request()->input('maxId') ? request()->input('maxId') : 100;
        $minId = request()->input('minId') ? request()->input('minId') : 1;

        $expansionId = 1;
        if ($expansion === 'wotlk') {
            $expansionId = 3;
        } else if ($expansion === 'tbc') {
            $expansionId = 2;
        }

        $items = Item::where([['item_id', '>=', $minId], ['item_id', '<=', $maxId], ['expansion_id', $expansionId]])->orderBy('item_id')->get();

        return view('admin.translateItems', [
            'expansion' => $expansion,
            'expansionId' => $expansionId,
            'items'     => $items,
            'lang'      => $lang,
            'maxId'     => $maxId,
            'minId'     => $minId,
        ]);
    }

    /**
     * Edit a user
     *
     * @return \Illuminate\Http\Response
     */
    public function submitEditUser($id)
    {
        $currentMember = request()->get('currentMember');

        $now = Carbon::now()->toDateTimeString();

        $user = User::findOrFail(request()->input('id'));

        $user->ads_disabled_at = request()->input('ads_disabled_at') ? $now : null;
        $user->banned_at       = request()->input('banned_at') ? $now : null;

        $user->save();

        return view('admin.users.edit', [
            'currentMember' => $currentMember,
            'user'          => $user,
        ]);
    }
}
