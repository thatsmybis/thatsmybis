<?php

namespace App\Http\Controllers;

// use App\{};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RosterController extends Controller
{
    /**
     * Show the roster page.
     *
     * @return \Illuminate\Http\Response
     */
    public function roster($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['allRaidGroups', 'raidGroups.role']);

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        $showPrios = false;
        if (!$guild->is_prio_private || $currentMember->hasPermission('view.prios')) {
            $showPrios = true;
        }

        $showWishlist = false;
        if (!$guild->is_wishlist_private || $currentMember->hasPermission('view.wishlists')) {
            $showWishlist = true;
        }

        $cacheKey = 'roster:guild:' . $guild->id . ':showOfficerNote:' . $showOfficerNote . ':showPrios:' . $showPrios . ':showWishlist:' . $showWishlist . ':attendance:' . $guild->is_attendance_hidden;

        if (request()->get('bustCache')) {
            Cache::forget($cacheKey);
        }

        $characters = Cache::remember($cacheKey, env('CACHE_ROSTER_SECONDS', 5), function () use ($guild, $showOfficerNote, $showPrios, $showWishlist) {
            return $guild->getCharactersWithItemsAndPermissions($showOfficerNote, $showPrios, $showWishlist, false);
        });

        $showEdit = false;
        if ($currentMember->hasPermission('edit.characters')) {
            $showEdit = true;
        }

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
