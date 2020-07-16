<?php

namespace App\Http\Controllers;

use App\{Content, Guild, Raid, Role, User};
use Auth;
use Illuminate\Http\Request;
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
            'spec'          => 'nullable|string|50',
            'profession_1'  => ['nullable', 'string', 'different:profession_2', Rule::in(Character::professions())],
            'profession_2'  => ['nullable', 'string', 'different:profession_1', Rule::in(Character::professions())],
            'rank'          => 'nullable|integer|min:1|max:14',
            'rank_goal'     => 'nullable|integer|min:1|max:14',
            'raid_id'       => 'nullable|integer|exists:raids,id',
            'public_note'   => 'nullable|string|1000',
            'officer_note'  => 'nullable|string|1000',
            'personal_note' => 'nullable|string|2000',
            'position'      => 'nullable|integer|min:0|max:50',
        ];
    }

    /**
     * Show the roster page.
     *
     * @return \Illuminate\Http\Response
     */
    public function roster($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with('roles')->firstOrFail();

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
            ->with(['member', 'raid', 'recipes', 'received', 'wishlist'])
            ->orderBy('name')
            ->get();

        return view('roster', [
            'guild'      => $guild,
            'characters' => $characters,
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
            $character = Character::where(['id' => $id], ['guild_id' => $guild->id])->firstOrFail();
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
                    return $query->where('members.user_id', Auth::id());
                },
            'characters' => function ($query) {
                    return $query->where(['characters.guild_id' => $guild->id, 'name' => request()->input('name')]);
                },
            'raids',
            ])->firstOrFail();

        // TODO: Validate user has permissions to create a character in this guild

        $validationRules = $this->getValidationRules();

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        if ($guild->characters->count() > 0) {
            abort(403, 'Name taken.');
        }

        $createValues = [];

        // TODO: If has permissions, allow to create characters tied to other members
        if (false) {
            // TODO: Validate member is of this guild
            $createValues['member_id'] = request()->input('member_id');
        } else {
        // Assign to current user's member object for this guild
            $createValues['member_id'] = $guild->members->first()->id;
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
        $createValues['public_note']   = request()->input('public_note');
        $createValues['officer_note']  = request()->input('officer_note');
        $createValues['personal_note'] = request()->input('personal_note');
        $createValues['position']      = request()->input('position');
        $createValues['guild_id']      = $guild->id;

        Raid::create($createValues);

        request()->session()->flash('status', 'Successfully created raid.');
        return redirect()->route('guild.raids', ['guildSlug' => $guild->slug]);
    }

    /**
     * Update a character
     * @return
     */
    public function update($guildSlug) {
        $guild = Guild::where('slug', $guildSlug)->with(['raids'])->firstOrFail();

        // TODO: Validate user can update this character in this guild

        $validationRules =  [
            'id'      => 'required|integer|exists:raids,id',
            'name'    => 'string|max:255',
            'role_id' => 'nullable|integer|exists:roles,id',
        ];

        $this->validate(request(), $validationRules);

        $raid = $guild->raids->where('id', request()->input('id'))->first();

        if (!$raid) {
            abort(404);
        }

        $updateValues = [];

        $updateValues['name']    = request()->input('name');
        $updateValues['slug']    = slug(request()->input('name'));
        $updateValues['role_id'] = request()->input('role_id');

        $raid->update($updateValues);

        request()->session()->flash('status', 'Successfully updated raid.');
        return redirect()->route('guild.raids', ['guildSlug' => $guild->slug]);


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
