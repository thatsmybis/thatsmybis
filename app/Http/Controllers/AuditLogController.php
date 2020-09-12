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
    public function index($guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

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
            ->leftJoin('raids', function ($join) use ($currentMember) {
                    $join->on('raids.id', '=', 'audit_logs.raid_id');
                    $join->whereIn('raids.id', $currentMember->raidsWithViewPermissions());
                })
            ->leftJoin('roles', function ($join) {
                    $join->on('roles.id', '=', 'audit_logs.role_id');
                });

        if (!empty($params['character_id'])) {
            $query = $query->where('characters.id', $params['character_id']);
        }

        if (!empty($params['item_id'])) {
            $query = $query->where('items.item_id', $params['item_id']);
        }

        if (!empty($params['member_id'])) {
            $query = $query->where('members.id', $params['member_id']);
        }

        if (!empty($params['raid_id'])) {
            $query = $query->where('raids.id', $params['raid_id']);
        }

        $logs = $query->where(['audit_logs.guild_id' => $guild->id])
            ->orderBy('audit_logs.created_at', 'desc')
            ->paginate(self::RESULTS_PER_PAGE);

        return view('auditLog', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'logs'          => $logs,
        ]);
    }
}
