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
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['raids', 'raids.role']);

        // TODO: Validate can edit this raid

        $raid = null;

        if ($id) {
            $raid = $guild->raids->where('id', $id)->first();

            if (!$raid) {
                abort(404, 'Raid not found.');
            }
        }

        return view('guild.raids.edit', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'raid'          => $raid,
        ]);
    }

    /**
     * Create a raid
     * @return
     */
    public function create($guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['raids', 'roles']);

        // TODO: Validate user can create a raid

        $validationRules = [
            'name'    => 'string|max:255',
            'role_id' => 'nullable|integer|exists:roles,id',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        if ($guild->raids->contains('name', request()->input('name'))) {
            abort(403, 'Name already exists.');
        }

        $role = null;

        if (request()->input('role_id')) {
            $role = $guild->roles->where('id', request()->input('role_id'));
            if (!$role) {
                abort(404, 'Role not found.');
            }
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
     * Disable a raid
     * @return
     */
    public function toggleDisable($guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'allRaids' => function ($query) {
                return $query->where('id', request()->input('id'));
            }
        ]);

        $raid = $guild->allRaids->first();

        if (!$raid) {
            abort(404, 'Raid not found.');
        }

        // TODO: Validate has permissions to disable raid

        $validationRules = [
            'id' => 'required|integer|exists:raids,id'
        ];
        $validationMessages = [];
        $this->validate(request(), $validationRules, $validationMessages);

        $disabledAt = (request()->input('disabled_at') == 1 ? getDateTime() : null);

        $updateValues['disabled_at']  = $disabledAt;

        $raid->update($updateValues);

        request()->session()->flash('status', 'Successfully ' . ($disabledAt ? 'disabled' : 'enabled') . ' ' . $raid->name . '.');
        return redirect()->route('guild.raids', ['guildSlug' => $guild->slug]);
    }

    /**
     * Show the raids page.
     *
     * @return \Illuminate\Http\Response
     */
    public function raids($guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['allRaids', 'allRaids.role']);

        // TODO: Validate can view raids

        return view('guild.raids.list', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
        ]);
    }

    /**
     * Update a raid
     * @return
     */
    public function update($guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['raids']);

        // TODO: Validate can update a raid

        $validationRules =  [
            'id'      => 'required|integer|exists:raids,id',
            'name'    => 'string|max:255',
            'role_id' => 'nullable|integer|exists:roles,id',
        ];

        $this->validate(request(), $validationRules);

        $raid = $guild->raids->where('id', request()->input('id'))->first();
        if (!$raid) {
            abort(404, 'Raid not found.');
        }

        $role = null;
        if (request()->input('role_id')) {
            $role = $guild->roles->where('id', request()->input('role_id'));
            if (!$role) {
                abort(404, 'Role not found.');
            }
        }

        $updateValues = [];

        $updateValues['name']    = request()->input('name');
        $updateValues['slug']    = slug(request()->input('name'));
        $updateValues['role_id'] = request()->input('role_id');

        $raid->update($updateValues);

        request()->session()->flash('status', 'Successfully updated ' . $raid->name . '.');
        return redirect()->route('guild.raids', ['guildSlug' => $guild->slug]);
    }
}
