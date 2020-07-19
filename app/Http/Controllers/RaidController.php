<?php

namespace App\Http\Controllers;

use App\{Guild, Raid, User};
use Auth;
use Illuminate\Http\Request;
use RestCord\DiscordClient;
use Kodeine\Acl\Models\Eloquent\Permission;

class RaidController extends Controller
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
     * Show a raid for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($guildSlug, $id = null)
    {
        $guild = Guild::where('slug', $guildSlug)->with(['raids', 'raids.role'])->firstOrFail();

        // TODO: Validate user can view/edit this raid

        $raid = null;

        if ($id) {
            $raid = $guild->raids->where('id', $id)->first();

            if (!$raid) {
                abort(404);
            }
        }

        return view('guild.raids.edit', [
            'guild' => $guild,
            'raid'  => $raid,
        ]);
    }

    /**
     * Create a raid
     * @return
     */
    public function create($guildSlug) {
        $guild = Guild::where('slug', $guildSlug)->with('raids')->firstOrFail();

        // TODO: Validate user can create a raid in this guild

        $validationRules = [
            'name'    => 'string|max:255',
            'role_id' => 'nullable|integer|exists:roles,id',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        if ($guild->raids->contains('name', request()->input('name'))) {
            abort(403, 'Name already exists.');
        }

        $createValues = [];

        $createValues['name']     = request()->input('name');
        $createValues['slug']     = slug(request()->input('name'));
        $createValues['role_id']  = request()->input('role_id');
        $createValues['guild_id'] = $guild->id;

        Raid::create($createValues);

        request()->session()->flash('status', 'Successfully created raid.');
        return redirect()->route('guild.raids', ['guildSlug' => $guild->slug]);
    }

    /**
     * Show the raids page.
     *
     * @return \Illuminate\Http\Response
     */
    public function raids($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with(['raids', 'raids.role'])->firstOrFail();

        // TODO: Validate user can view this guild's raids

        return view('guild.raids.list', [
            'guild' => $guild,
        ]);
    }

    /**
     * Update a raid
     * @return
     */
    public function update($guildSlug) {
        $guild = Guild::where('slug', $guildSlug)->with(['raids'])->firstOrFail();

        // TODO: Validate a user can update a raid in this guild

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
     * Remove a raid
     * @return
     */
    public function remove($guildSlug) {
        $guild = Guild::where('slug', $guildSlug)->firstOrFail();
// NOT IMPLEMENTED!!!!!!!! PLACEHOLDER CODE!!!!
        // TODO: Validate user can remove raids in this guild

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
