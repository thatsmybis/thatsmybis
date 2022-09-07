<?php

namespace App\Http\Controllers;

use App\{AuditLog, Batch, Character, Guild, Instance, Item, ItemSource, Member, Raid, RaidGroup, Role};
use App\Http\Controllers\ExportController;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuditLogController extends Controller
{
    const DEFAULT_RESULTS_PER_PAGE = 50;
    const MAX_RESULTS_PER_PAGE = 500;

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
     * Passing the parameter 'index' in the URL params will produce a CSV of the results.
     * Passing 'rows' will specify the number of rows to fetch.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['characters', 'members', 'raidGroups']);

        $instances = Cache::remember('instances:expansion:' . $guild->expansion_id,
            env('CACHE_INSTANCES_SECONDS', 600),
            function () use ($guild) {
                return Instance::where('expansion_id', $guild->expansion_id)->get();
        });

        $resources = [];

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
                'raids.slug             AS raid_slug',
                'raids.date             AS raid_date',
                'raid_groups.name       AS raid_group_name',
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
            ->leftJoin('raid_groups', function ($join) {
                    $join->on('raid_groups.id', '=', 'audit_logs.raid_group_id');
                })
            ->leftJoin('roles', function ($join) {
                    $join->on('roles.id', '=', 'audit_logs.role_id');
                })
            ->groupBy('audit_logs.id');

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

        // Require item to exist
        if (!empty(request()->input('item_instance_id'))) {
            $query = $query
                ->join('items', 'items.item_id', 'audit_logs.item_id')
                ->join('item_item_sources AS item_item_sources2', 'item_item_sources2.item_id', 'items.item_id')
                ->join('item_sources AS item_sources2', 'item_sources2.id', 'item_item_sources2.item_source_id')
                ->where('item_sources2.instance_id', request()->input('item_instance_id'));
        } else { // Okay if no item present
            $query = $query->leftJoin('items', function ($join) {
                    $join->on('items.item_id', '=', 'audit_logs.item_id');
                });
        }

        if (!empty(request()->input('min_date'))) {
            $query = $query->where('audit_logs.created_at', '>',  request()->input('min_date'));
        }
        if (!empty(request()->input('max_date'))) {
            $query = $query->where('audit_logs.created_at', '<',  request()->input('max_date'));
        }

        if (!empty(request()->input('character_class'))) {
            $query = $query->where('characters.class', request()->input('character_class'));
        }

        if (!empty(request()->input('character_id'))) {
            $query = $query->where('characters.id', request()->input('character_id'));
            $resources[] = Character::where([['guild_id', $guild->id], ['id', request()->input('character_id')]])->with('member')->first();
        }

        if (!empty(request()->input('member_id'))) {
            $query = $query->where('members.id', request()->input('member_id'));
            $resources[] = Member::where([['guild_id', $guild->id], ['id', request()->input('member_id')]])->with('user')->first();
        }

        if (!empty(request()->input('batch_id'))) {
            $query = $query->where('batches.id', request()->input('batch_id'));
            $resources[] = Batch::where([['guild_id', $guild->id], ['id', request()->input('batch_id')]])->with('member')->first();
        }

        if (!empty(request()->input('raid_id'))) {
            $query = $query->where('raids.id', request()->input('raid_id'));
            $resources[] = Raid::where([['guild_id', $guild->id], ['id', request()->input('raid_id')]])->first();
        }

        if (!empty(request()->input('raid_group_id'))) {
            $query = $query->where('raid_groups.id', request()->input('raid_group_id'));
            $resources[] = RaidGroup::where([['guild_id', $guild->id], ['id', request()->input('raid_group_id')]])->with('role')->first();
        }

        if (!empty(request()->input('item_id'))) {
            $query = $query->where('items.item_id', request()->input('item_id'));
            $resources[] = Item::find(request()->input('item_id'));
        }

        if (!empty(request()->input('type'))) {
            if (request()->input('type') == 'received_all') {
                $query = $query->whereIn('audit_logs.type', [AuditLog::TYPE_ASSIGN, Item::TYPE_RECEIVED]);
            } else {
                $query = $query->where('audit_logs.type', request()->input('type'));
            }
        }

        $resultsPerPage = self::DEFAULT_RESULTS_PER_PAGE;
        if (request()->input('rows')) {
            // use the user input, but limit it between 1 and MAX_RESULTS_PER_PAGE
            $resultsPerPage = max(1, min(self::MAX_RESULTS_PER_PAGE, (int)request()->input('rows')));
        }

        $logs = $query->where(['audit_logs.guild_id' => $guild->id])
            ->orderBy('audit_logs.created_at', 'desc')
            ->paginate($resultsPerPage);

        if (request()->input('export')) {
            $simpleFormatLogs = [];
            $i = 0;
            foreach ($logs->items() as $auditLogItem) {
                $simpleFormatLogs[$i] = (object)$auditLogItem->toArray();
                $i++;
            }

            $headers = [
                "id",
                "description",
                "type",
                "character_id",
                "guild_id",
                "batch_id",
                "instance_id",
                "item_id",
                "item_source_id",
                "member_id",
                "other_member_id",
                "raid_id",
                "raid_group_id",
                "role_id",
                "created_at",
                "updated_at",
                "batch_name",
                "character_name",
                "character_slug",
                "character_class",
                "instance_name",
                "instance_slug",
                "item_name",
                "item_source_name",
                "member_username",
                "member_slug",
                "other_member_username",
                "other_member_slug",
                "raid_name",
                "raid_slug",
                "raid_date",
                "raid_group_name",
                "role_name",
            ];

            $csv = ExportController::createCsv($simpleFormatLogs, $headers);
            return ExportController::GetExport($csv, $guild->name . ' Audit Logs @' . time(), ExportController::CSV);
        }

        return view('auditLog', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'instances'     => $instances,
            'logs'          => $logs,
            'resources'     => $resources,
            'showPrios'     => $showPrios,
            'showWishlist'  => $showWishlist,
        ]);
    }
}
