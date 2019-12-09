<?php

namespace App\Http\Controllers;

use App\{User};
use Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    const MAX_RECEIVED_ITEMS=100;
    const MAX_RECIPES=50;
    const MAX_WISHLIST_ITEMS=10;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'seeUser']);
    }

    public function ban($id = null) {
        $user = User::find($id);

        if (!$user) {
            abort(404, 'User not found.');
        }

        if ($user->banned_at) {
            $user->banned_at = null;
            request()->session()->flash('status', 'User ' . $user->username . ' unbanned.');
        } else {
            $user->banned_at = getDateTime();
            request()->session()->flash('status-danger', 'User ' . $user->username . ' banned.');
        }
        $user->save();

        return redirect()->route('showUser', [$user->id, $user->username]);
    }

    /**
     * Find a user by ID.
     *
     * @param int $id The ID of the user to find.
     *
     * @return \Illuminate\Http\Response
     */
    public function findById($id = null)
    {
        $user = User::select([
                'username',
            ])
            ->where('id', '=', $id)
            ->first();

        if ($id == null) {
            $user = Auth::user();
            if ($user) {
                return redirect()->route('showUser', [$user->id, $user->username]);
            } else {
                request()->session()->flash('status', 'Could not find user.');
                return redirect()->route('home');
            }
        } else if (!$user) {
            request()->session()->flash('status', 'Could not find user ' . $id . '.');
            return redirect()->route('home');
        }

        return redirect()->route('showUser', [$id, $user->username]);
    }

    /**
     * Find a user by username.
     *
     * @param string $username The name of the user to find.
     *
     * @return \Illuminate\Http\Response
     */
    public function findByUsername($username = null)
    {
        $user = null;

        if ($username == null) {
            $user = Auth::user();
            if ($user) {
                return redirect()->route('showUser', [$user->id, $user->username]);
            } else {
                request()->session()->flash('status', 'Could not find user.');
                return redirect()->route('home');
            }
        } else {
            $user = User::select([
                    'id',
                    'username',
                ])
                ->where('username', '=', strtolower(trim($username)))
                ->first();

            if (!$user) {
                request()->session()->flash('status', 'Could not find user ' . $username . '.');
                return redirect()->route('home');
            }
        }
        return redirect()->route('showUser', [$user->id, $user->username]);
    }

    /**
     * Find a user by username.
     *
     * @param string $username The name of the user to find.
     *
     * @return \Illuminate\Http\Response
     */
    public function showUser($id, $username = null)
    {
        $user = null;

        if ($username == null) {
            $user = Auth::user();
            if ($user) {
                return redirect()->route('showUser', [$user->id, $user->username]);
            } else {
                request()->session()->flash('status', 'Could not find user.');
                return redirect()->route('home');
            }
        } else {
            $user = User::where('username', '=', strtolower(trim($username)))->with(['roles', 'wishlist', 'received'])->first();

            if (!$user) {
                request()->session()->flash('status', 'Could not find user ' . $username . '.');
                return redirect()->route('home');
            }
        }

        $canEdit = false;
        $showPersonalNote = false;
        $showOfficerNote = false;

        if (Auth::id() == $user->id) {
            $canEdit = true;
            // $showPersonalNote = true;
        }

        if (Auth::user()->hasRole(env('PERMISSION_RAID_LEADER'))) {
            $canEdit = true;
            $showOfficerNote = true;
        }

        if (request()->input('edit') && $canEdit) {
            return view('profile.edit', [
                'maxReceivedItems' => self::MAX_RECEIVED_ITEMS,
                'maxRecipes'       => self::MAX_RECIPES,
                'maxWishlistItems' => self::MAX_WISHLIST_ITEMS,
                'user'             => $user,
                'showPersonalNote' => $showPersonalNote,
                'showOfficerNote'  => $showOfficerNote,
            ]);
        } else {
            return view('profile.show', [
                'user'             => $user,
                'canEdit'          => $canEdit,
                'showPersonalNote' => $showPersonalNote,
                'showOfficerNote'  => $showOfficerNote,
            ]);
        }
    }

    /**
     * Update a profile
     *
     * @param int $id The id of the user to update.
     *
     * @return \Illuminate\Http\Response
     */
    public function submit($id = null)
    {
        $validationRules =  [
            'username'     => 'string|max:255',
            'spec'         => 'nullable|string|max:50',
            'alts.*'       => 'nullable|string|max:50',
            'rank'         => 'nullable|string|max:50',
            'rank_goal'    => 'nullable|string|max:50',
            'wishlist.*'   => 'nullable|integer|exists:items,item_id',
            'received.*'   => 'nullable|integer|exists:items,item_id',
            'recipes.*'    => 'nullable|integer|exists:items,item_id',
            'note'         => 'nullable|string|max:1000',
            'officer_note' => 'nullable|string|max:1000',
        ];

        $this->validate(request(), $validationRules);

        $user = User::with(['wishlist', 'recipes', 'received'])->findOrFail($id);
        $authUser = Auth::user();

        $canEdit = false;
        $canEditOfficerNote = false;

        if (Auth::id() == $user->id) {
            $canEdit = true;
        }

        if (Auth::user()->hasRole(env('PERMISSION_RAID_LEADER'))) {
            $canEdit = true;
            $canEditOfficerNote = true;
        }

        if ($canEdit) {
            $updateValues['username']  = request()->input('username');
            $updateValues['spec']      = request()->input('spec');
            $updateValues['alts']      = implode(array_filter(request()->input('alts')), "\n");
            $updateValues['rank']      = request()->input('rank');
            $updateValues['rank_goal'] = request()->input('rank_goal');
            $updateValues['note']      = request()->input('note');

            if (request()->input('wishlist')) {
                $items = [];
                $existingItems = $user->wishlist->keyBy('item_id')->keys()->toArray();

                $i = 0;
                foreach (request()->input('wishlist') as $id) {
                    if($id) {
                        $i++;
                        $items[$id] = [
                            'order' => $i,
                            'type'  => 'wishlist',
                            ];
                    }
                }
                // Gets items which need to be dropped...
                $toDrop = array_diff($existingItems, array_keys($items));
                // Drops them...
                $user->wishlist()->detach($toDrop);
                // Adds any new items
                $user->wishlist()->syncWithoutDetaching($items);
            } else {
                $user->wishlist()->detach();
            }

            if (request()->input('recipes')) {
                $items = [];
                $existingItems = $user->recipes->keyBy('item_id')->keys()->toArray();

                $i = 0;
                foreach (request()->input('recipes') as $id) {
                    if($id) {
                        $i++;
                        $items[$id] = [
                            'order' => $i,
                            'type'  => 'recipe',
                            ];
                    }
                }
                // Gets items which need to be dropped...
                $toDrop = array_diff($existingItems, array_keys($items));
                // Drops them...
                $user->recipes()->detach($toDrop);
                // Adds any new items
                $user->recipes()->syncWithoutDetaching($items);
            } else {
                $user->recipes()->detach();
            }

            if (request()->input('received')) {
                $items = [];
                $existingItems = $user->received->keyBy('item_id')->keys()->toArray();

                $i = 0;
                foreach (request()->input('received') as $id) {
                    if($id) {
                        $i++;
                        $items[$id] = [
                            'order' => $i,
                            'type'  => 'received',
                            ];
                    }
                }
                // Gets items which need to be dropped...
                $toDrop = array_diff($existingItems, array_keys($items));
                // Drops them...
                $user->received()->detach($toDrop);
                // Adds any new items
                $user->received()->syncWithoutDetaching($items);
            } else {
                $user->received()->detach();
            }

            if ($canEditOfficerNote) {
                $updateValues['officer_note']  = request()->input('officer_note');
            }

            $user->update($updateValues);
        } else {
            abort(403);
        }

        return redirect()->route('showUser', ['id' => $user->id, 'username' => $user->username]);
    }

    /**
     * Update a user's personal note
     *
     * @param int $id The id of the user to update.
     *
     * @return \Illuminate\Http\Response
     */
    public function submitPersonalNote($id = null)
    {
        $validationRules =  [
            'personal_note'  => 'nullable|string|max:5000',
        ];

        $this->validate(request(), $validationRules);

        $user = User::findOrFail($id);

        if ($user->id == Auth::id()) {
            $updateValues['personal_note'] = request()->input('personal_note');

            $user->update($updateValues);
        }

        return redirect()->route('showUser', ['id' => $user->id, 'username' => $user->username]);
    }
}
