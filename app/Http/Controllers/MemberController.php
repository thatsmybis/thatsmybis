<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, Content, Guild, Member, Raid, Role, User};
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
    public function edit($guildId, $guildSlug, $memberId, $usernameSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'members' => function ($query) use($memberId) {
                return $query->where('members.id', $memberId)
                ->with([
                    'roles',
                    'user',
                ]);

            },
        ]);

        $member = $guild->members->first();

        if (!$member) {
            request()->session()->flash('status', 'Member not found.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        if ($member->id != $currentMember->id && !$currentMember->hasPermission('edit.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit someone else.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        return view('member.edit', [
            'currentMember'   => $currentMember,
            'guild'           => $guild,
            'member'          => $member,
        ]);
    }

    /**
     * Find a member by ID.
     *
     * @param int $id The ID of the guild to find.
     *
     * @return \Illuminate\Http\Response
     */
    public function find($guildId, $guildSlug, $usernameSlug)
    {
        $guild  = request()->get('guild');

        $member = Member::select(['id', 'slug', 'guild_id'])->where(['slug' => $usernameSlug, 'guild_id' => $guild->id])->first();

        if (!$member) {
            request()->session()->flash('status', 'Could not find member.');
            return redirect()->route('home');
        }

        return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guildSlug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]);
    }

    /**
     * Show a member
     *
     * @return \Illuminate\Http\Response
     */
    public function show($guildId, $guildSlug, $memberId, $usernameSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $member = Member::where(['guild_id' => $guild->id, 'id' => $memberId])
            ->with([
                'characters',
                'characters.raid',
                'characters.raid.role',
                'characters.recipes',
                'roles',
            ])
            ->first();

        if (!$member) {
            request()->session()->flash('status', 'Member not found.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $user = User::where('id', $member->user_id)->first();

        $recipes = collect();
        foreach ($member->characters as $character) {
            foreach ($character->recipes as $recipe) {
                $recipes->add($recipe);
            }
        }

        $showEdit = false;
        if ($member->id == $currentMember->id || $currentMember->hasPermission('edit.characters')) {
            $showEdit = true;
        }

        $showEditLoot = false;
        if ($member->id == $currentMember->id || $currentMember->hasPermission('loot.characters')) {
            $showEditLoot = true;
        }

        return view('member.show', [
            'characters'       => $member->characters,
            'currentMember'    => $currentMember,
            'guild'            => $guild,
            'member'           => $member,
            'recipes'          => $recipes,
            'showEdit'         => $showEdit,
            'showEditLoot'     => $showEditLoot,
            'showPersonalNote' => ($currentMember->id == $member->id),
            'user'             => $user,
        ]);
    }

    /**
     * Show a list of members for a guild
     *
     * @return \Illuminate\Http\Response
     */
    public function showList($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['members', 'members.characters', 'members.roles', 'raids', 'raids.role']);

        $unassignedCharacters = Character::where([
                ['guild_id', $guild->id],
            ])
        ->whereNull('member_id')
        ->get();

        $showEdit = false;
        if ($currentMember->hasPermission('edit.characters')) {
            $showEdit = true;
        }

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        return view('member.list', [
            'currentMember'        => $currentMember,
            'guild'                => $guild,
            'showEdit'             => $showEdit,
            'showOfficerNote'      => $showOfficerNote,
            'unassignedCharacters' => $unassignedCharacters,
        ]);
    }

    /**
     * Toggle streamer mode on and off
     * @return
     */
    public function toggleStreamerMode() {
        $user = request()->get('currentUser');

        $toggle = ($user->is_streamer_mode ? 0 : 1);

        $user->update(['is_streamer_mode' => $toggle]);

        request()->session()->flash('status', 'Streamer mode ' . ($toggle ? 'on' : 'off') . '. Officer notes ' . ($toggle ? 'hidden' : 'visible') . '.');
        return redirect()->route('home');
    }

    /**
     * Update a member
     * @return
     */
    public function update($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'members' => function ($query) {
                return $query->where('members.id', request()->input('id'))
                    ->orWhere('members.username', request()->input('username'));
            },
        ]);

        $member = $guild->members->where('id', request()->input('id'))->first();
        $sameNameMember = $guild->members->where('username', request()->input('username'))->first();

        if (!$member) {
            abort(404, 'Guild member not found.');
        }

        // Can't create a duplicate name
        if ($sameNameMember && ($member->id != $sameNameMember->id)) {
            abort(403, 'Name taken.');
            request()->session()->flash('status', 'Name taken.');
            return redirect()->back();
        }

        $validationRules = [
            'id'            => 'required|integer|exists:members,id',
            'username'      => 'nullable|string|min:2|max:32',
            'public_note'   => 'nullable|string|max:140',
            'officer_note'  => 'nullable|string|max:140',
            'personal_note' => 'nullable|string|max:2000',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $updateValues = [];

        if ($currentMember->hasPermission('edit.officer-notes')) {
            $updateValues['officer_note'] = request()->input('officer_note');
        }

        if ($currentMember->id != $member->id && !$currentMember->hasPermission('edit.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit that member.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]);
        }

        $updateValues['username']    = request()->input('username');
        $updateValues['slug']    = slug(request()->input('username'));
        $updateValues['public_note'] = request()->input('public_note');

        // User is editing their own member
        if ($currentMember->id == $member->id) {
            $updateValues['personal_note'] = request()->input('personal_note');
        }

        $auditMessage = '';

        if ($updateValues['username'] != $member->username) {
            $auditMessage .= ' (renamed from ' . $member->username . ' to ' . $updateValues['username'] . ')';
        }

        if ($updateValues['public_note'] != $member->public_note) {
            $auditMessage .= ' (public note)';
        }

        if (isset($updateValues['officer_note']) && ($updateValues['officer_note'] != $member->officer_note)) {
            $auditMessage .= ' (officer note)';
        }

        $member->update($updateValues);

        if ($auditMessage && $currentMember->id == $member->id) {
            AuditLog::create([
                'description'     => $currentMember->username . ' updated their own profile' . ($auditMessage ? $auditMessage : ''),
                'member_id'       => $currentMember->id,
                'guild_id'        => $guild->id,
                'other_member_id' => null,
            ]);
        } else if ($auditMessage && $currentMember->id != $member->id) {
            AuditLog::create([
                'description'     => $currentMember->username . ' updated member ' . ($auditMessage ? $auditMessage : ''),
                'member_id'       => $currentMember->id,
                'guild_id'        => $guild->id,
                'other_member_id' => $member->id,
            ]);
        }

        request()->session()->flash('status', 'Successfully updated profile.');
        return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]);
    }

    /**
     * Update a character's note(s) only
     * @return
     */
    public function updateNote($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'members' => function ($query) {
                return $query->where('members.id', request()->input('id'));
            },
        ]);

        $validationRules = [
            'id'            => 'required|integer|exists:members,id',
            'officer_note'  => 'nullable|string|max:140',
            'personal_note' => 'nullable|string|max:2000',
            'public_note'   => 'nullable|string|max:140',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $member = $guild->members->where('id', request()->input('id'))->first();

        if (!$member) {
            request()->session()->flash('status', 'Member not found.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $updateValues = [];

        if ($currentMember->hasPermission('edit.officer-notes')) {
            $updateValues['officer_note'] = request()->input('officer_note');
        } else if ($currentMember->id != $member->id && !$currentMember->hasPermission('edit.character')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit that member.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]);
        }

        $updateValues['public_note'] = request()->input('public_note');

        // User is editing their own member
        if ($currentMember->id == $member->id) {
            $updateValues['personal_note'] = request()->input('personal_note');
        }

        $auditMessage = '';

        if ($updateValues['public_note'] != $member->public_note) {
            $auditMessage .= ' (public note)';
        }

        if (isset($updateValues['officer_note']) && ($updateValues['officer_note'] != $member->officer_note)) {
            $auditMessage .= ' (officer note)';
        }

        $member->update($updateValues);

        if ($auditMessage && $currentMember->id == $member->id) {
            AuditLog::create([
                'description'     => $currentMember->username . ' updated their own notes' . ($auditMessage ? $auditMessage : ''),
                'member_id'       => $currentMember->id,
                'guild_id'        => $guild->id,
                'other_member_id' => null,
            ]);
        } else if ($auditMessage && $currentMember->id != $member->id) {
            AuditLog::create([
                'description'     => $currentMember->username . ' updated  notes' . ($auditMessage ? $auditMessage : ''),
                'member_id'       => $currentMember->id,
                'guild_id'        => $guild->id,
                'other_member_id' => $member->id,
            ]);
        }

        request()->session()->flash('status', "Successfully updated " . $member->username ."'s note.");
        return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]);
    }
}
