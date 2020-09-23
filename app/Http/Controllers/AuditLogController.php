<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, Guild, Instance, Item, ItemSource, Member, Raid, Role};
use Auth;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    const RESULTS_PER_PAGE = 70;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'seeUser']);
    }

    /**
     * Show the index page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $resource = null;
        $resourceName = null;

        $showPrios = false;
        if (!$guild->is_prio_private || $currentMember->hasPermission('view.prios')) {
            $showPrios = true;
        }

        $showWishlist = false;
        if (!$guild->is_wishlist_private  || $currentMember->hasPermission('view.wishlist')) {
            $showWishlist = true;
        }

        $select = 'audit_logs.*';

        $query = AuditLog::select([
                'audit_logs.*',
                'characters.name        AS character_name',
                'characters.slug        AS character_slug',
                'characters.class       AS character_class',
                'instances.name         AS instance_name',
                'instances.slug         AS instance_slug',
                'items.name             AS item_name',
                'item_sources.name      AS item_source_name',
                'members.username       AS member_username',
                'members.slug           AS member_slug',
                'other_members.username AS other_member_username',
                'other_members.slug     AS other_member_slug',
                'raids.name             AS raid_name',
                'roles.name             AS role_name',
            ])
            ->leftJoin('characters', function ($join) {
                    $join->on('characters.id', '=', 'audit_logs.character_id');
                })
            // ->join('guilds', function ($join) {
            //         $join->on('guilds.id', '=', 'audit_logs.guild_id');
            //     })
            ->leftJoin('instances', function ($join) {
                    $join->on('instances.id', '=', 'audit_logs.instance_id');
                })
            ->leftJoin('items', function ($join) {
                    $join->on('items.item_id', '=', 'audit_logs.item_id');
                })
            ->leftJoin('item_sources', function ($join) {
                    $join->on('item_sources.id', '=', 'audit_logs.item_source_id');
                })
            ->leftJoin('members', function ($join) {
                    $join->on('members.id', '=', 'audit_logs.member_id');
                })

            ->leftJoin('members as other_members', function ($join) {
                    $join->on('other_members.id', '=', 'audit_logs.other_member_id');
                })
            ->leftJoin('raids', function ($join) {
                    $join->on('raids.id', '=', 'audit_logs.raid_id');
                })
            ->leftJoin('roles', function ($join) {
                    $join->on('roles.id', '=', 'audit_logs.role_id');
                });

        if (!$showPrios && !$showWishlist) {
            $query = $query->where([['audit_logs.type', '!=', Item::TYPE_PRIO], ['audit_logs.type', '!=', Item::TYPE_WISHLIST]]);
        } else if (!$showPrios) {
            $query = $query->where('audit_logs.type', '!=', Item::TYPE_PRIO);
        } else if (!$showWishlist) {
            $query = $query->where('audit_logs.type', '!=', Item::TYPE_WISHLIST);
        }

        if (!empty(request()->input('character_id'))) {
            $query = $query->where('characters.id', request()->input('character_id'));
            $resource = Character::where([['guild_id', $guild->id], ['id', request()->input('character_id')]])->with('member')->first();
            $resourceName = $resource->name;
        }

        if (!empty(request()->input('item_id'))) {
            $query = $query->where('items.item_id', request()->input('item_id'));
            $resource = Item::find(request()->input('item_id'));
            $resourceName = $resource->name;
        }

        if (!empty(request()->input('member_id'))) {
            $query = $query->where('members.id', request()->input('member_id'));
            $resource = Member::where([['guild_id', $guild->id], ['id', request()->input('member_id')]])->with('user')->first();
            $resourceName = $resource->name;
        }

        if (!empty(request()->input('raid_id'))) {
            $query = $query->where('raids.id', request()->input('raid_id'));
            $resource = Raid::where([['guild_id', $guild->id], ['id', request()->input('raid_id')]])->with('role')->first();
            $resourceName = $resource->name;
        }

        $logs = $query->where(['audit_logs.guild_id' => $guild->id])
            ->orWhereNull('audit_logs.type')->where(['audit_logs.guild_id' => $guild->id])
            ->orderBy('audit_logs.created_at', 'desc')
            ->paginate(self::RESULTS_PER_PAGE);

        return view('auditLog', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'logs'          => $logs,
            'resource'      => $resource,
            'resourceName'  => $resourceName,
            'showPrios'     => $showPrios,
            'showWishlist'  => $showWishlist,
        ]);
    }
}
