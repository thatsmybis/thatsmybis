<?php

namespace App\Http\Controllers;

use App\{Character, Content, Guild, Raid, Role, User};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RestCord\DiscordClient;

class MemberController extends Controller
{
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
     * Show a member for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($guildSlug, $username)
    {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) use($username) {
                    return $query->where('members.username', $username)
                        ->orWhere('members.user_id', Auth::id());
                        // Not grabbing member.user and member.user.roles here because the code is messier than just doing it in a separate call
                },
            ])->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();
        if (!$currentMember) {
            abort(404, 'Not a member of that guild.');
        }

        // TODO: Validate user can view this character in this guild

        $member = $guild->members->first();

        $user = User::where('id', $member->user_id)->with([
            'roles' => function ($query) use($guild) {
                return $query->where('guild_id', $guild->id);
            },
            ])->first();

        // TODO: Validate user can edit this character in this guild

        return view('member.edit', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'member'        => $member,
            'user'          => $user,
        ]);
    }

    /**
     * Show a member
     *
     * @return \Illuminate\Http\Response
     */
    public function show($guildSlug, $username)
    {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) use($username) {
                    return $query->where('members.username', $username)
                        ->orWhere('members.user_id', Auth::id())
                        ->with([
                        'characters',
                        'characters.recipes',
                        // Not grabbing member.user and member.user.roles here because the code is messier than just doing it in a separate call
                    ]);
                },
            ])->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();
        if (!$currentMember) {
            abort(404, 'Not a member of that guild.');
        }

        // TODO: Validate user can view this character in this guild

        $member = $guild->members->first();

        $user = User::where('id', $member->user_id)->with([
            'roles' => function ($query) use($guild) {
                return $query->where('guild_id', $guild->id);
            },
            ])->first();

        $recipes = collect();
        foreach ($member->characters as $character) {
            foreach ($character->recipes as $recipe) {
                $recipes->add($recipe);
            }
        }

        return view('member.show', [
            'characters'    => $member->characters,
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'member'        => $member,
            'recipes'       => $recipes,
            'user'          => $user,
        ]);
    }

    /**
     * Update a member
     * @return
     */
    public function update($guildSlug) {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                    return $query->where('members.user_id', Auth::id())
                        ->orWhere('members.id', request()->input('id'))
                        ->orWhere('members.username', request()->input('username'));
                },
            ])->firstOrFail();

        $currentMember  = $guild->members->where('user_id', Auth::id())->first();
        $selectedMember = $guild->members->where('id', request()->input('id'))->first();
        $sameNameMember = $guild->members->where('username', request()->input('username'))->first();

        if (!$currentMember) {
            abort(404, 'Not a member of that guild.');
        }

        if (!$selectedMember) {
            abort(404, 'Guild member not found.');
        }

        // Can't create a duplicate name
        if ($sameNameMember && ($selectedMember->id != $sameNameMember->id)) {
            abort(403, 'Name taken.');
        }

        // TODO: Validate user has permissions to update this member in this guild

        $validationRules = [
            'id'            => 'required|integer|exists:members,id',
            'username'      => 'nullable|string|min:2|max:32',
            'public_note'   => 'nullable|string|max:1000',
            'officer_note'  => 'nullable|string|max:1000',
            'personal_note' => 'nullable|string|max:2000',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $updateValues = [];

        // TODO: Permissions for officer note
        if (true) {
            $updateValues['officer_note'] = request()->input('officer_note');
        }

        // TODO: Permissions for editing someone else
        if ($currentMember->id != $selectedMember->id && false) {
            abort(403, "You do not have permission to edit someone else.");
        }

        $updateValues['username']    = request()->input('username');
        $updateValues['public_note'] = request()->input('public_note');

        // User is editing their own character
        if ($currentMember->id == $selectedMember->id) {
            $updateValues['personal_note'] = request()->input('personal_note');
        }

        $selectedMember->update($updateValues);

        request()->session()->flash('status', 'Successfully updated profile.');
        return redirect()->route('member.show', ['guildSlug' => $guild->slug, 'username' => $selectedMember->username]);
    }

    /**
     * Remove a character
     * @return
     */
    public function remove($guildSlug) {
        // $guild = Guild::where('slug', $guildSlug)->firstOrFail();

        // // TODO: Validate user can update this character in this guild

        // $validationRules = [
        //     'id' => 'required|integer|exists:raids,id',
        // ];

        // $validationMessages = [];

        // $this->validate(request(), $validationRules, $validationMessages);

        // $guild = Guild::where('slug', $guildSlug)->firstOrFail();

        // $raid = Raid::where(['id' => request()->input('id'), 'guild_id' => $guild->id])->firstOrFail();

        // $raid->delete();

        // request()->session()->flash('status', 'Successfully removed raid.');
        // return redirect()->back();
    }
}
