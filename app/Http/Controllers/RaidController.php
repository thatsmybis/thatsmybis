<?php

namespace App\Http\Controllers;

use App\{AuditLog, Guild, Raid, User};
use Auth;
use Illuminate\Http\Request;
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
    public function edit($guildId, $guildSlug, $id = null)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('edit.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to view that page.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load([
            'allRaids' => function ($query) use ($id) {
                return $query->where('id', $id);
            },
            'allRaids.role']);

        $raid = null;

        if ($id) {
            $raid = $guild->allRaids->where('id', $id)->first();

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
    public function create($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('create.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to create raids.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load(['raids', 'roles']);

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

        $raid = Raid::create($createValues);

        AuditLog::create([
            'description' => $currentMember->username . ' created a raid',
            'member_id'   => $currentMember->id,
            'guild_id'    => $guild->id,
            'raid_id'     => $raid->id,
        ]);

        request()->session()->flash('status', 'Successfully created raid.');
        return redirect()->route('guild.raids', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]);
    }

    /**
     * Disable a raid
     * @return
     */
    public function toggleDisable($guildId, $guildSlug) {
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

        if (!$currentMember->hasPermission('disable.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to disable/enable raids.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $validationRules = [
            'id' => 'required|integer|exists:raids,id'
        ];
        $validationMessages = [];
        $this->validate(request(), $validationRules, $validationMessages);

        $disabledAt = (request()->input('disabled_at') == 1 ? getDateTime() : null);

        $updateValues['disabled_at']  = $disabledAt;

        $raid->update($updateValues);

        AuditLog::create([
            'description' => $currentMember->username . ($disabledAt ? ' disabled' : ' enabled') . ' a raid',
            'member_id'   => $currentMember->id,
            'guild_id'    => $guild->id,
            'raid_id'     => $raid->id,
        ]);

        request()->session()->flash('status', 'Successfully ' . ($disabledAt ? 'disabled' : 'enabled') . ' ' . $raid->name . '.');
        return redirect()->route('guild.raids', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]);
    }

    /**
     * Show the raids page.
     *
     * @return \Illuminate\Http\Response
     */
    public function raids($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('view.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to view that page.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load(['allRaids', 'allRaids.role']);

        return view('guild.raids.list', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
        ]);
    }

    /**
     * Update a raid
     * @return
     */
    public function update($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('edit.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit raids.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $validationRules =  [
            'id'      => 'required|integer|exists:raids,id',
            'name'    => 'string|max:255',
            'role_id' => 'nullable|integer|exists:roles,id',
        ];

        $this->validate(request(), $validationRules);

        $id = request()->input('id');

        $guild->load([
            'allRaids' => function ($query) use ($id) {
                return $query->where('id', $id);
            },
        ]);

        $raid = $guild->allRaids->where('id', $id)->first();
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

        $auditMessage = '';

        if ($updateValues['name'] != $raid->name) {
            $auditMessage .= ' (renamed to ' . $updateValues['name'] . ')';
        }

        $raid->update($updateValues);

        AuditLog::create([
            'description' => $currentMember->username . ' updated a raid' . $auditMessage,
            'member_id'   => $currentMember->id,
            'guild_id'    => $guild->id,
            'raid_id'     => $raid->id,
        ]);

        request()->session()->flash('status', 'Successfully updated ' . $raid->name . '.');
        return redirect()->route('guild.raids', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]);
    }
}
