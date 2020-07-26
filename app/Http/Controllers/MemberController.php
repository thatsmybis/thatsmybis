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
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'members' => function ($query) use($username) {
                return $query->where('members.username', $username)
                ->with([
                    'roles',
                    'user',
                ]);

            },
        ]);

        // TODO: Validate user can edit this character

        $member = $guild->members->first();

        return view('member.edit', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'member'        => $member,
        ]);
    }

    /**
     * Show a member
     *
     * @return \Illuminate\Http\Response
     */
    public function show($guildSlug, $username)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'members' => function ($query) use($username) {
                return $query->where('members.username', $username)
                    ->with([
                        'characters',
                        'characters.raid',
                        'characters.raid.role',
                        'characters.recipes',
                        'roles',
                        // Not grabbing member.user and member.user.roles here because the code is messier than just doing it in a separate call
                    ]);
            },
        ]);

        $member = $guild->members->where('username', $username)->first();

        if (!$member) {
            abort(404, 'Member not found.');
        }

        $user = User::where('id', $member->user_id)->first();

        $recipes = collect();
        foreach ($member->characters as $character) {
            foreach ($character->recipes as $recipe) {
                $recipes->add($recipe);
            }
        }

        return view('member.show', [
            'characters'       => $member->characters,
            'currentMember'    => $currentMember,
            'guild'            => $guild,
            'member'           => $member,
            'recipes'          => $recipes,
            'showOfficerNote'  => true, // TODO permissions for showing officer note
            'showPersonalNote' => ($currentMember->id == $member->id),
            'user'             => $user,
        ]);
    }

    /**
     * Update a member
     * @return
     */
    public function update($guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'members' => function ($query) {
                return $query->where('members.id', request()->input('id'))
                    ->orWhere('members.username', request()->input('username'));
            },
        ]);

        $selectedMember = $guild->members->where('id', request()->input('id'))->first();
        $sameNameMember = $guild->members->where('username', request()->input('username'))->first();

        if (!$selectedMember) {
            abort(404, 'Guild member not found.');
        }

        // Can't create a duplicate name
        if ($sameNameMember && ($selectedMember->id != $sameNameMember->id)) {
            abort(403, 'Name taken.');
        }

        $validationRules = [
            'id'            => 'required|integer|exists:members,id',
            'username'      => 'nullable|string|min:2|max:32',
            'public_note'   => 'nullable|string|max:144',
            'officer_note'  => 'nullable|string|max:144',
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
     * Update a character's note(s) only
     * @return
     */
    public function updateNote($guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'members' => function ($query) {
                return $query->where('members.user_id', request()->input('id'));
            },
        ]);

        $validationRules = [
            'id'            => 'required|integer|exists:members,id',
            'officer_note'  => 'nullable|string|max:144',
            'personal_note' => 'nullable|string|max:2000',
            'public_note'   => 'nullable|string|max:144',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $member = $guild->members->where('id', request()->input('id'))->first();

        if (!$member) {
            abort(404, "Member not found.");
        }

        $updateValues = [];

        // TODO: If has permissions, allow to change who owns character or modify someone else's character
        // TODO: Also if they can edit officer note
        if (true) {
            $updateValues['officer_note'] = request()->input('officer_note');
        } else if ($currentMember->id != $member->id) {
            abort(403, "You do not have permission to edit someone else.");
        }

        $updateValues['public_note'] = request()->input('public_note');

        // User is editing their own member
        if ($currentMember->id == $member->id) {
            $updateValues['personal_note'] = request()->input('personal_note');
        }

        $member->update($updateValues);

        request()->session()->flash('status', "Successfully updated " . $member->username ."'s note.");
        return redirect()->route('member.show', ['guildSlug' => $guild->slug, 'username' => $member->username]);
    }
}
