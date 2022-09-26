<?php

namespace App\Http\Controllers;

use App\{Character, Instance};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RosterController extends Controller
{
    private const BREAKDOWN = 'breakdown';
    private const ROSTER = 'roster';
    private const STATS = 'stats';

    /**
     * Show the roster page.
     *
     * @return \Illuminate\Http\Response
     */
    public function roster($guildId, $guildSlug, $page = self::ROSTER) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        $viewPrioPermission = $currentMember->hasPermission('view.prios');
        $showPrios = false;
        if (!$guild->is_prio_disabled && (!$guild->is_prio_private || $viewPrioPermission)) {
            $showPrios = true;
        }

        $showWishlist = false;
        if (!$guild->is_wishlist_disabled && (!$guild->is_wishlist_private || $currentMember->hasPermission('view.wishlists'))) {
            $showWishlist = true;
        }

        $cacheKey = 'roster:guild:' . $guild->id . ':showOfficerNote:' . $showOfficerNote . ':showPrios:' . $showPrios . ':viewPrioPermission:' . $viewPrioPermission . ':showWishlist:' . $showWishlist . ':attendance:' . $guild->is_attendance_hidden;

        if (request()->get('bustCache')) {
            Cache::forget($cacheKey);
        }

        $characters = Cache::remember($cacheKey, env('CACHE_ROSTER_SECONDS', 5), function () use ($guild, $showOfficerNote, $showPrios, $showWishlist, $viewPrioPermission) {
            return $guild->getCharactersWithItemsAndPermissions($showOfficerNote, $showPrios, $showWishlist, $viewPrioPermission, false, true);
        });

        $showEdit = false;
        if ($currentMember->hasPermission('edit.characters')) {
            $showEdit = true;
        }

        if ($page === self::BREAKDOWN) {
            $character = $characters['characters']->where('name', 'Coop')->first();
            // dd(
            //     array_merge([$character->raid_group_id], $character->secondaryRaidGroups->map(function ($raidGroup) { return $raidGroup->id; })->all())
            // );
            return view('rosterBreakdown', [
                'characters'    => $characters['characters'],
                'currentMember' => $currentMember,
                'guild'         => $guild,
                'raidGroups'    => $guild->allRaidGroups,

                'archetypes'  => Character::archetypes(),
                'classes'     => Character::classes($guild->expansion_id),
                'professions' => Character::professions($guild->expansion_id),
                'races'       => Character::races($guild->expansion_id),
                'specs'       => Character::specs($guild->expansion_id),
            ]);
        } else if ($page === self::STATS) {
            return view('rosterStats', [
                'characters'      => $characters['characters'],
                'currentMember'   => $currentMember,
                'guild'           => $guild,
                'raidGroups'      => $guild->allRaidGroups,
                'showEdit'        => $showEdit,
                'showOfficerNote' => $characters['showOfficerNote'],
                'showPrios'       => $characters['showPrios'],
                'showWishlist'    => $characters['showWishlist'],
            ]);
        } else {
            return view('roster', [
                'characters'      => $characters['characters'],
                'currentMember'   => $currentMember,
                'guild'           => $guild,
                'raidGroups'      => $guild->allRaidGroups,
                'showEdit'        => $showEdit,
                'showOfficerNote' => $characters['showOfficerNote'],
                'showPrios'       => $characters['showPrios'],
                'showWishlist'    => $characters['showWishlist'],
            ]);
        }
    }

    /**
     * Show the roster breakdown page.
     *
     * @return \Illuminate\Http\Response
     */
    public function rosterBreakdown($guildId, $guildSlug) {
        return $this->roster($guildId, $guildSlug, self::BREAKDOWN);
    }

    /**
     * Show the roster stats page.
     *
     * @return \Illuminate\Http\Response
     */
    public function rosterStats($guildId, $guildSlug) {
        return $this->roster($guildId, $guildSlug, self::STATS);
    }
}
