<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, Guild, Raid, RaidGroup, User};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Kodeine\Acl\Models\Eloquent\Permission;

class RaidGroupController extends Controller
{
    const RAIDS_PER_PAGE = 10;

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
     * Show a raid group's attendance
     *
     * @return \Illuminate\Http\Response
     */
    public function attendance($guildId, $guildSlug, $id)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        $type = null;
        if (request()->input('type')) {
            $type = request()->input('type');
        }

        $guild->load([
            'allRaidGroups' => function ($query) use ($id, &$type) {
                $query = $query->where('id', $id)->with(['role']);

                if ($type === 'secondary') {
                    $query = $query->with('secondaryCharacters');
                } else if ($type === 'all') {
                    $query = $query->with(['characters', 'secondaryCharacters']);
                } else {
                    $type = 'main';
                    $query = $query->with('characters');
                }

                return $query;
            },
        ]);

        $raidGroup = $guild->allRaidGroups->where('id', $id)->first();

        if (!$raidGroup) {
            request()->session()->flash('status', 'Raid group not found');
            return redirect()->route('guild.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]);
        }

        if ($type == 'secondary') {
            $raidGroup->setRelation('characters', $raidGroup->secondaryCharacters->values());
        } else if ($type === 'all' && $raidGroup->relationLoaded('secondaryCharacters')) {
            // Merge secondary characters in with main characters
            $raidGroup->setRelation(
                'characters',
                $raidGroup->characters
                    ->merge($raidGroup->secondaryCharacters)
                    ->sortBy('name')
                    ->values()
            );
        }

        // To get the paginator to work, raids is its own query and not a with() on the raidGroup.
        $raids = Raid::select('raids.*')
            ->join('raid_raid_groups', 'raid_raid_groups.raid_id', 'raids.id')
            ->where('raid_raid_groups.raid_group_id', $id)
            ->whereNull('cancelled_at')
            ->whereNull('archived_at')
            ->with([
                'items',
                'instances',
            ])
            ->orderBy('raids.date', 'desc')
            ->paginate(self::RAIDS_PER_PAGE);

        return view('guild.raidGroups.attendance', [
            'currentMember'   => $currentMember,
            'guild'           => $guild,
            'raidGroup'       => $raidGroup,
            'raids'           => $raids,
            'remarks'         => Raid::remarks(),
            'showOfficerNote' => $showOfficerNote,
            'type'            => $type,
        ]);
    }

    /**
     * Show a raid group's characters for editing
     *
     * @return \Illuminate\Http\Response
     */
    private function characters($guildId, $guildSlug, $id, $isSecondary = false)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('edit.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to view that page.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load([
            'allRaidGroups' => function ($query) use ($id, $isSecondary) {
                return $query->where('id', $id)
                    ->with([
                        ($isSecondary ? 'secondaryCharacters' : 'characters') => function ($query) {
                            return $query->with(['raidGroup', 'raidGroup.role']);
                        },
                        'role'
                    ])
                    ->withCount(['characters', 'secondaryCharacters']);
            },
            'characters',
            'characters.raidGroup',
            'characters.raidGroup.role',
        ]);

        $raidGroup = $guild->allRaidGroups->where('id', $id)->first();

        if (!$raidGroup) {
            request()->session()->flash('status', 'Raid group not found');
            return redirect()->route('guild.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]);
        }

        if ($isSecondary) {
            // Filter out selected characters so that they don't show up in the 'Available characters' menu
            $guild->setRelation('characters', $guild->characters->diff($raidGroup->secondaryCharacters));
            $selectedCharacters = $raidGroup->secondaryCharacters;
        } else {
            $guild->setRelation('characters', $guild->characters->diff($raidGroup->characters));
            $selectedCharacters = $raidGroup->characters;
        }

        return view('guild.raidGroups.characters', [
            'selectedCharacters' => $selectedCharacters,
            'currentMember'      => $currentMember,
            'guild'              => $guild,
            'raidGroup'          => $raidGroup,
            'isSecondary'        => $isSecondary,
        ]);
    }

    /**
     * Show a raid group's main characters for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function mainCharacters($guildId, $guildSlug, $id)
    {
        return $this->characters($guildId, $guildSlug, $id, false);
    }

    /**
     * Show a raid group's secondary characters for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function secondaryCharacters($guildId, $guildSlug, $id)
    {
        return $this->characters($guildId, $guildSlug, $id, true);
    }

    /**
     * Show a raid group for editing
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

        $raidGroup = null;

        if ($id) {
            $raidGroup = RaidGroup::where([
                'guild_id' => $guild->id,
                'id' => $id,
            ])->first();

            if (!$raidGroup) {
                abort(404, 'Raid Group not found.');
            }
        }

        return view('guild.raidGroups.edit', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'raidGroup'     => $raidGroup,
        ]);
    }

    /**
     * Create a raid group
     * @return
     */
    public function create($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('create.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to create Raid Groups.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load(['allRaidGroups', 'roles']);

        $validationRules = [
            'name'    => 'string|max:255',
            'role_id' => 'nullable|integer|exists:roles,id',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        if ($guild->raidGroups->contains('name', request()->input('name'))) {
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

        $raidGroup = RaidGroup::create($createValues);

        AuditLog::create([
            'description'   => $currentMember->username . ' created a Raid Group',
            'member_id'     => $currentMember->id,
            'guild_id'      => $guild->id,
            'raid_group_id' => $raidGroup->id,
        ]);

        request()->session()->flash('status', 'Successfully created Raid Group.');
        return redirect()->route('guild.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'b' => 1]);
    }

    /**
     * Disable a raid group
     * @return
     */
    public function toggleDisable($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'allRaidGroups' => function ($query) {
                return $query->where('id', request()->input('id'));
            }
        ]);

        $raidGroup = $guild->allRaidGroups->first();

        if (!$raidGroup) {
            abort(404, 'Raid Group not found.');
        }

        if (!$currentMember->hasPermission('disable.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to disable/enable Raid Groups.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $validationRules = [
            'id' => 'required|integer|exists:raid_groups,id'
        ];
        $validationMessages = [];
        $this->validate(request(), $validationRules, $validationMessages);

        $disabledAt = (request()->input('disabled_at') == 1 ? getDateTime() : null);

        $updateValues['disabled_at']  = $disabledAt;

        $raidGroup->update($updateValues);

        AuditLog::create([
            'description'   => $currentMember->username . ($disabledAt ? ' disabled' : ' enabled') . ' a Raid Group group',
            'member_id'     => $currentMember->id,
            'guild_id'      => $guild->id,
            'raid_group_id' => $raidGroup->id,
        ]);

        request()->session()->flash('status', 'Successfully ' . ($disabledAt ? 'disabled' : 'enabled') . ' ' . $raidGroup->name . '.');
        return redirect()->route('guild.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'b' => 1]);
    }

    /**
     * Show the raid groups page.
     *
     * @return \Illuminate\Http\Response
     */
    public function raidGroups($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $showEdit = false;

        if ($currentMember->hasPermission('edit.raids')) {
            $showEdit = true;
        }

        $guild->load([
            'allRaidGroups' => function ($query) {
                return $query->withCount(['characters', 'secondaryCharacters']);
            },
            'allRaidGroups.role',
        ]);

        return view('guild.raidGroups.list', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'showEdit'      => $showEdit,
        ]);
    }

    /**
     * Update a raid group
     * @return
     */
    public function update($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('edit.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit Raid Groups.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $validationRules =  [
            'id'      => 'required|integer|exists:raid_groups,id',
            'name'    => 'string|max:255',
            'role_id' => 'nullable|integer|exists:roles,id',
        ];

        $this->validate(request(), $validationRules);

        $id = request()->input('id');

        $guild->load([
            'allRaidGroups' => function ($query) use ($id) {
                return $query->where('id', $id);
            },
        ]);

        $raidGroup = $guild->allRaidGroups->where('id', $id)->first();
        if (!$raidGroup) {
            abort(404, 'Raid Group not found.');
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

        if ($updateValues['name'] != $raidGroup->name) {
            $auditMessage .= ' (renamed to ' . $updateValues['name'] . ')';
        }

        $raidGroup->update($updateValues);

        AuditLog::create([
            'description'   => $currentMember->username . ' updated a Raid Group' . $auditMessage,
            'member_id'     => $currentMember->id,
            'guild_id'      => $guild->id,
            'raid_group_id' => $raidGroup->id,
        ]);

        request()->session()->flash('status', 'Successfully updated ' . $raidGroup->name . '.');
        return redirect()->route('guild.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]);
    }

    /**
     * Update a raid group's characters
     * @return
     */
    private function updateCharacters($isSecondary = false) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('edit.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit Raid Groups.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $validationRules =  [
            'raid_group_id' => [
                'integer',
                Rule::exists('raid_groups', 'id')->where('raid_groups.guild_id', $guild->id),
            ],
            'characters.*.character_id' => [
                'integer',
                Rule::exists('characters', 'id')->where('characters.guild_id', $guild->id),
            ],
        ];

        $this->validate(request(), $validationRules);

        $characters = [];
        if (request()->input('characters')) {
            foreach (request()->input('characters') as $character) {
                // This has the added effect of filtering out duplicates
                $characters[$character['character_id']] = $character;
            }
        }

        $raidGroup = $guild->raidGroups()
            ->where('id', request()->input('raid_group_id'))
            ->with($isSecondary ? 'secondaryCharacters' : 'characters')
            ->firstOrFail();

        if ($isSecondary) {
            $oldCount = $raidGroup->characters->count();
        } else {
            $oldCount = $raidGroup->secondaryCharacters->count();
        }


        if ($isSecondary) {
            $raidGroup->secondaryCharacters()->sync($characters);
        } else {
            // Drop the old main characters
            $guild->allCharacters()->where('raid_group_id', $raidGroup->id)->update(['raid_group_id' => null]);
            // Add the new/updated ones
            Character::where('guild_id', $guild->id)->whereIn('id', array_keys($characters))->update(['raid_group_id' => $raidGroup->id]);
        }

        AuditLog::create([
            'description'   => "{$currentMember->username} updated the " . ($isSecondary ? 'other' : 'main') . " characters in a Raid Group ({$oldCount} -> " . count($characters) . " characters)",
            'member_id'     => $currentMember->id,
            'guild_id'      => $guild->id,
            'raid_group_id' => $raidGroup->id,
        ]);

        request()->session()->flash('status', 'Successfully updated ' . $raidGroup->name . '\'s ' . ($isSecondary ? 'other' : 'main') . ' characters.');
        return redirect()->route('guild.raidGroup.' . ($isSecondary ? 'secondaryCharacters' : 'mainCharacters'), ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id]);
    }

    public function updateMainCharacters() {
        return $this->updateCharacters(false);
    }

    public function updateSecondaryCharacters() {
        return $this->updateCharacters(true);
    }
}
