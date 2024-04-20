<?php

namespace App\Http\Controllers;

use App\{Character, Instance};
use App\Http\Controllers\ItemController;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        $dateCacheKey = ItemController::getLootDateCacheKey($guild, $currentMember);;
        $minReceivedLootDate = null;
        // Check cache for old input, otherwise use default
        $minReceivedLootDate = Cache::get($dateCacheKey);
        if (!$minReceivedLootDate) {
            $minReceivedLootDate = Carbon::now()->subMonths(env('DEFAULT_RECEIVED_LOOT_MONTHS_CUTOFF', 6))->format('Y-m-d');
        }

        $cacheKey = 'roster:guild:' . $guild->id
            . ':showOfficerNote:' . $showOfficerNote
            . ':showPrios:' . $showPrios
            . ':viewPrioPermission:' . $viewPrioPermission
            . ':showWishlist:' . $showWishlist
            . ':attendance:' . $guild->is_attendance_hidden
            . ':minLootDate:' . $minReceivedLootDate;

        if (request()->get('bustCache')) {
            Cache::forget($cacheKey);
        }

        $data = Cache::remember($cacheKey, env('CACHE_ROSTER_SECONDS', 5), function () use ($guild, $showOfficerNote, $showPrios, $showWishlist, $viewPrioPermission, $minReceivedLootDate) {
            $data = $guild->getCharactersWithItemsAndPermissions($showOfficerNote, $showPrios, $showWishlist, $viewPrioPermission, false, true, $minReceivedLootDate);
            // VASTLY reduce memory usage by encoding to JSON before saving to cache.
            // On a large guild in late WoTLK:
            // - 91mb memory usage after running query above
            // - 156mb memory usage after Cache::remember WITHOUT early JSON encoding.
            // - 34mb memory usage after Cache::remember WITH early JSON encoding.
            $data['characterJson'] = $data['characters']->makeVisible('officer_note')->toJson();
            // For Roster Breakdown page which uses the model. Other pages just pass the JSON to the client.
            $data['characters'] = $data['characters']->transform(function($character, $key) {
                unset($character->received);
                unset($character->prios);
                unset($character->allWishlists);
                unset($character->wishlist);
                return $character;
            })->makeVisible('officer_note');
            return $data;
        });
        $showEdit = false;
        if ($currentMember->hasPermission('edit.characters')) {
            $showEdit = true;
        }

        if ($page === self::BREAKDOWN) {
            return view('rosterBreakdown', [
                'characters'    => $data['characters'],
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
                'characters'      => $data['characterJson'],
                'currentMember'   => $currentMember,
                'guild'           => $guild,
                'minReceivedLootDate' => $minReceivedLootDate,
                'raidGroups'      => $guild->allRaidGroups,
                'showEdit'        => $showEdit,
                'showOfficerNote' => $data['showOfficerNote'],
                'showPrios'       => $data['showPrios'],
                'showWishlist'    => $data['showWishlist'],
            ]);
        } else {
            return view('roster', [
                'characters'      => $data['characterJson'],
                'currentMember'   => $currentMember,
                'guild'           => $guild,
                'minReceivedLootDate' => $minReceivedLootDate,
                'raidGroups'      => $guild->allRaidGroups,
                'showEdit'        => $showEdit,
                'showOfficerNote' => $data['showOfficerNote'],
                'showPrios'       => $data['showPrios'],
                'showWishlist'    => $data['showWishlist'],
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
