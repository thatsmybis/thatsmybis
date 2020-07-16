<?php

namespace App\Http\Controllers;

use App\{Character, Content, Guild, Raid, Role, User};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RestCord\DiscordClient;

class CharacterController extends Controller
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

    private function getValidationRules() {
        return [
            'member_id'     => 'nullable|integer|exists:members,id',
            'name'          => 'nullable|string|min:2|max:40',
            'level'         => 'nullable|integer|min:1|max:60',
            'race'          => ['nullable', 'string', Rule::in(Character::races())],
            'class'         => ['nullable', 'string', Rule::in(Character::classes())],
            'spec'          => 'nullable|string|max:50',
            'profession_1'  => ['nullable', 'string', 'different:profession_2', Rule::in(Character::professions())],
            'profession_2'  => ['nullable', 'string', 'different:profession_1', Rule::in(Character::professions())],
            'rank'          => 'nullable|integer|min:1|max:14',
            'rank_goal'     => 'nullable|integer|min:1|max:14',
            'raid_id'       => 'nullable|integer|exists:raids,id',
            'public_note'   => 'nullable|string|max:1000',
            'officer_note'  => 'nullable|string|max:1000',
            'personal_note' => 'nullable|string|max:2000',
            'order'         => 'nullable|integer|min:0|max:50',
        ];
    }

    /**
     * Show the roster page.
     *
     * @return \Illuminate\Http\Response
     */
    public function roster($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with(['raids'])->firstOrFail();

        // TODO: Validate user can view this roster

        $characterFields = [
            'member_id',
            'guild_id',
            'name',
            'level',
            'race',
            'class',
            'spec',
            'profession_1',
            'profession_2',
            'rank',
            'rank_goal',
            'raid_id',
            'public_note',
            'hidden_at',
            'removed_at',
        ];

        // if (Auth::user()->hasRole(env('PERMISSION_RAID_LEADER'))) {
        //     $characterFields[] = 'officer_note';
        // }

        $characters = Character::select($characterFields)
            ->where('guild_id', $guild->id)
            ->whereNull('hidden_at')
            ->with(['member', 'member.user.roles', 'raid', 'recipes', 'received', 'wishlist'])
            ->orderBy('name')
            ->get();

        return view('roster', [
            'characters' => $characters,
            'guild'      => $guild,
            'raids'      => $guild->raids,
        ]);
    }

    /**
     * Show a character for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($guildSlug, $id = null)
    {
        $guild = Guild::where('slug', $guildSlug)->with('members')->firstOrFail();

        // TODO: Validate user can edit this character

        $character = null;

        if ($id) {
            $character = Character::where(['id' => $id], ['guild_id' => $guild->id])->with('member')->firstOrFail();
        }

        return view('characters.edit', [
            'guild'     => $guild,
            'character' => $character,
        ]);
    }

    /**
     * Create a character
     * @return
     */
    public function create($guildSlug) {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                    return $query->where('members.user_id', Auth::id())
                        ->orWhere('members.id', request()->input('member_id'));
                },
            'characters' => function ($query) {
                    return $query->where('name', request()->input('name'));
                },
            'raids',
            ])->firstOrFail();

        $currentMember  = $guild->members->where('user_id', Auth::id())->first();
        $selectedMember = $guild->members->where('id', request()->input('member_id'))->first();

        if (!$selectedMember) {
            abort(403, 'Chosen player not found.');
        }

        if ($guild->characters->count() > 0) {
            abort(403, 'Name taken.');
        }

        // TODO: Validate user has permissions to create a character in this guild

        $validationRules = $this->getValidationRules();

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $createValues = [];

        // TODO: If has permissions, allow to create characters tied to other members
        if (true) {
            $createValues['member_id']    = $selectedMember->id;
            $createValues['officer_note'] = request()->input('officer_note');
        } else {
        // Assign to current user's member object for this guild
            $createValues['member_id'] = $currentMember->id;
        }

        // User is editing their own character
        if ($createValues['member_id'] == $currentMember->id) {
            $createValues['personal_note'] = request()->input('personal_note');
            $createValues['order']         = request()->input('order');
        }

        $createValues['name']          = request()->input('name');
        $createValues['level']         = request()->input('level');
        $createValues['race']          = request()->input('race');
        $createValues['class']         = request()->input('class');
        $createValues['spec']          = request()->input('spec');
        $createValues['profession_1']  = request()->input('profession_1');
        $createValues['profession_2']  = request()->input('profession_2');
        $createValues['rank']          = request()->input('rank');
        $createValues['rank_goal']     = request()->input('rank_goal');
        $createValues['raid_id']       = request()->input('raid_id');

        // TODO: Permissions for who can edit public note
        if (true) {
            $createValues['public_note']   = request()->input('public_note');
        }

        $createValues['guild_id']      = $guild->id;

        Character::create($createValues);

        request()->session()->flash('status', 'Successfully created ' . $createValues['name'] . ', ' . (request()->input('level') ? 'level ' . request()->input('level') : '') . ' ' . request()->input('race') . ' ' . request()->input('class'));
        return redirect()->route('guild.news', ['guildSlug' => $guild->slug]);
    }

    /**
     * Update a character
     * @return
     */
    public function update($guildSlug) {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                    return $query->where('members.user_id', Auth::id())
                        ->orWhere('members.id', request()->input('member_id'));
                },
            'characters' => function ($query) {
                    return $query->where('name', request()->input('name'))
                        ->orWhere('id', request()->input('id'));
                },
            'raids',
            ])->firstOrFail();

        $currentMember  = $guild->members->where('user_id', Auth::id())->first();
        $selectedMember = $guild->members->where('id', request()->input('member_id'))->first();

        $currentCharacter  = $guild->characters->where('id', request()->input('id'))->first();
        $sameNameCharacter = $guild->characters->where('name', request()->input('name'))->first();

        // Can't create a duplicate name
        if ($sameNameCharacter && ($currentCharacter->id != $sameNameCharacter->id)) {
            abort(403, 'Name taken.');
        }

        // TODO: Validate user has permissions to update this character in this guild

        $validationRules = $this->getValidationRules();

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $updateValues = [];

        // TODO: If has permissions, allow to change who owns character or modify someone else's character
        if (true) {
            $updateValues['member_id']    = ($selectedMember ? $selectedMember->id : null);
            $updateValues['officer_note'] = request()->input('officer_note');
        } else if (!$selectedMember) {
            abort(403, "You do not have permissions to create a character that isn't assigned to a player.");
        } else if ($currentCharacter->member_id != $currentMember->id) {
            abort(403, "You do not have permission to edit someone else's character.");
        } else if ($selectedMember->id != $currentMember->id) {
            abort(403, 'You do not have permission to change who owns this character.');
        }

        // User is editing their own character
        if ($currentCharacter->member_id == $currentMember->id) {
            $updateValues['personal_note'] = request()->input('personal_note');
            $updateValues['order']         = request()->input('order');
        }

        $updateValues['name']          = request()->input('name');
        $updateValues['level']         = request()->input('level');
        $updateValues['race']          = request()->input('race');
        $updateValues['class']         = request()->input('class');
        $updateValues['spec']          = request()->input('spec');
        $updateValues['profession_1']  = request()->input('profession_1');
        $updateValues['profession_2']  = request()->input('profession_2');
        $updateValues['rank']          = request()->input('rank');
        $updateValues['rank_goal']     = request()->input('rank_goal');
        $updateValues['raid_id']       = request()->input('raid_id');

        // TODO: Permissions for who can edit public note
        if (true) {
            $updateValues['public_note']   = request()->input('public_note');
        }

        $currentCharacter->update($updateValues);

        request()->session()->flash('status', 'Successfully updated ' . $updateValues['name'] . ', ' . (request()->input('level') ? 'level ' . request()->input('level') : '') . ' ' . request()->input('race') . ' ' . request()->input('class'));
        return redirect()->route('guild.news', ['guildSlug' => $guild->slug]);
    }

    /**
     * Remove a character
     * @return
     */
    public function remove($guildSlug) {
        $guild = Guild::where('slug', $guildSlug)->firstOrFail();

        // TODO: Validate user can update this character in this guild

        $validationRules = [
            'id' => 'required|integer|exists:raids,id',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $guild = Guild::where('slug', $guildSlug)->firstOrFail();

        $raid = Raid::where(['id' => request()->input('id'), 'guild_id' => $guild->id])->firstOrFail();

        $raid->delete();

        request()->session()->flash('status', 'Successfully removed raid.');
        return redirect()->back();
    }
}
