<?php

namespace App\Http\Controllers;

use App\{User};
use Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('sessionHasDiscordToken');
    }

    /**
     * Show the Dashboard page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard');
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
            $user = User::where('username', '=', strtolower(trim($username)))->first();

            if (!$user) {
                request()->session()->flash('status', 'Could not find user ' . $username . '.');
                return redirect()->route('home');
            }
        }

        if (request()->input('edit') && Auth::id() == $user->id) {
            return view('profile.edit', [
                'user' => $user,
                'showOfficerNote' => false,
            ]);
        } else {
            return view('profile.view', [
                'user' => $user,
                'canEdit' => Auth::id() == $user->id ? true : false,
                'showOfficerNote' => false,
            ]);
        }
    }

    /**
     * Update a profile
     *
     * @param string $username The name of the user to update.
     *
     * @return \Illuminate\Http\Response
     */
    public function submit($id = null)
    {
        $validationRules =  [
            'username'      => 'string|max:255',
            'spec'          => 'nullable|string|max:50',
            'class'         => 'nullable|string|max:20',
            'professions'   => 'nullable|string|max:1000',
            'recipes'       => 'nullable|string|max:1000',
            'alts'          => 'nullable|string|max:300',
            'rank'          => 'nullable|string|max:50',
            'rank_goal'     => 'nullable|string|max:50',
            'wishlist'      => 'nullable|string|max:1000',
            'loot_received' => 'nullable|string|max:1000',
            'note'          => 'nullable|string|max:1000',
            'officer_note'  => 'nullable|string|max:1000',
        ];

        $this->validate(request(), $validationRules);

        $user = User::findOrFail($id);
        $authUser = Auth::user();

        if ($user->id == $authUser->id) { // TODO: Add permissions check
            $updateValues['username']      = request()->input('username');
            $updateValues['spec']          = request()->input('spec');
            $updateValues['class']         = request()->input('class');
            $updateValues['professions']   = request()->input('professions');
            $updateValues['recipes']       = request()->input('recipes');
            $updateValues['alts']          = request()->input('alts');
            $updateValues['rank']          = request()->input('rank');
            $updateValues['rank_goal']     = request()->input('rank_goal');
            $updateValues['wishlist']      = request()->input('wishlist');
            $updateValues['loot_received'] = request()->input('loot_received');
            $updateValues['note']          = request()->input('note');

            if ($isOfficer) {
                $updateValues['officer_note']  = request()->input('officer_note');
            }

            $user->update($updateValues);
        }

        return redirect()->route('showUser', ['id' => $user->id, 'username' => $user->username]);
    }
}
