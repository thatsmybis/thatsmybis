<?php

namespace App\Http\Controllers;

use App\{AuditLog, Batch, Character, Guild, Instance, Item, ItemSource, Member, Raid, Role};
use Auth;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    const RESULTS_PER_PAGE = 50;

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

        $guild->load(['characters', 'members', 'raids']);

        $resource = null;
        $resourceName = null;

        $showPrios = false;
        if (!$guild->is_prio_private || $currentMember->hasPermission('view.prios')) {
            $showPrios = true;
        }

        $showWishlist = false;
        if (!$guild->is_wishlist_private  || $currentMember->hasPermission('view.wishlists')) {
            $showWishlist = true;
        }

        $select = 'audit_logs.*';

        $query = AuditLog::select([
                'audit_logs.*',
                'batches.name           AS batch_name',
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
            ->leftJoin('batches', function ($join) {
                    $join->on('batches.id', '=', 'audit_logs.batch_id');
                })
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
            $query = $query->where(function ($query) {
                return $query->where([
                        ['audit_logs.type', '!=', Item::TYPE_PRIO],
                        ['audit_logs.type', '!=', Item::TYPE_WISHLIST]
                    ])
                    ->orWhereNull('audit_logs.type');
            });
        } else if (!$showPrios) {
            $query = $query->where(function ($query) {
                return $query->where([['audit_logs.type', '!=', Item::TYPE_PRIO]])
                    ->orWhereNull('audit_logs.type');
            });
        } else if (!$showWishlist) {
            $query = $query->where(function ($query) {
                return $query->where([['audit_logs.type', '!=', Item::TYPE_WISHLIST]])
                    ->orWhereNull('audit_logs.type');
            });
        }

        if (!empty(request()->input('batch_id'))) {
            $query = $query->where('batches.id', request()->input('batch_id'));
            $resource = Batch::where([['guild_id', $guild->id], ['id', request()->input('batch_id')]])->with('member')->first();
            $resourceName = $resource ? ($resource->name ? $resource->name : 'Batch ' . $resource->id) : null;
        }

        if (!empty(request()->input('character_id'))) {
            $query = $query->where('characters.id', request()->input('character_id'));
            $resource = Character::where([['guild_id', $guild->id], ['id', request()->input('character_id')]])->with('member')->first();
            $resourceName = $resource ? $resource->name : null;
        }

        if (!empty(request()->input('item_id'))) {
            $query = $query->where('items.item_id', request()->input('item_id'));
            $resource = Item::find(request()->input('item_id'));
            $resourceName = $resource ? $resource->name : null;
        }

        if (!empty(request()->input('member_id'))) {
            $query = $query->where('members.id', request()->input('member_id'));
            $resource = Member::where([['guild_id', $guild->id], ['id', request()->input('member_id')]])->with('user')->first();
            $resourceName = $resource ? $resource->username : null;
        }

        if (!empty(request()->input('raid_id'))) {
            $query = $query->where('raids.id', request()->input('raid_id'));
            $resource = Raid::where([['guild_id', $guild->id], ['id', request()->input('raid_id')]])->with('role')->first();
            $resourceName = $resource ? $resource->name : null;
        }

        if (!empty(request()->input('type'))) {
            if (request()->input('type') == 'received_all') {
                $query = $query->whereIn('audit_logs.type', [AuditLog::TYPE_ASSIGN, Item::TYPE_RECEIVED]);
            } else {
                $query = $query->where('audit_logs.type', request()->input('type'));
            }
        }

        $logs = $query->where(['audit_logs.guild_id' => $guild->id])
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
