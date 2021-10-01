<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, Content, Guild, Member, Raid, RaidGroup, Role, User};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

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
            'allMembers' => function ($query) use($memberId) {
                return $query->where('members.id', $memberId)
                ->with([
                    'roles',
                    'user',
                ]);
            },
        ]);

        $member = $guild->allMembers->first();

        if (!$member) {
            request()->session()->flash('status', __('Member not found.'));
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        if ($member->id != $currentMember->id && !$currentMember->hasPermission('edit.characters')) {
            request()->session()->flash('status', __('You don\'t have permissions to edit someone else.'));
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
            request()->session()->flash('status', __('Could not find member.'));
            return redirect()->route('home');
        }

        return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guildSlug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]);
    }

    /**
     * Set the user's locale
     * @return
     */
    public function setLocale() {
        $user = request()->get('currentUser');

        $validationRules = [
            'locale' => ['string', 'min:2', 'max:3', Rule::in(array_keys(getLocales()))],
        ];

        $validationMessages = ['locale' => 'Language must be in supported list of languages.'];

        $this->validate(request(), $validationRules, $validationMessages);

        $user->update(['locale' => request()->input('locale')]);

        request()->session()->flash('status', __("Language set to :locale. To help with translations in your language, reach out on TMB's Discord server.", ['locale' => request()->input('locale')]));
        return redirect()->back()->withInput(['b' => 1]);
    }

    /**
     * Set the member's raid filter
     * @return
     */
    public function setRaidGroupFilter() {
        $currentMember = request()->get('currentMember');

        $validationRules = [
            'raid_group_id' => [
                'nullable',
                'integer',
                Rule::exists('raid_groups', 'id')->where('raid_groups.guild_id', $currentMember->guild_id),
            ],
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $currentMember->update(['raid_group_id_filter' => request()->input('raid_group_id')]);

        request()->session()->flash('status', __("Raid Group Filter set."));
        return redirect()->back()->withInput(['b' => 1]);
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

        $cacheKey = 'member:' . $memberId . ':guild:' . $guild->id . ':attendance:' . $guild->is_attendance_hidden;

        if (request()->get('bustCache')) {
            Cache::forget($cacheKey);
        }

        $member = Cache::remember($cacheKey, env('CACHE_MEMBER_SECONDS', 5), function () use ($guild, $memberId) {
            $member = Member::where(['guild_id' => $guild->id, 'id' => $memberId])
                ->with([
                    'characters',
                    'characters.raidGroup',
                    'characters.raidGroup.role',
                    'characters.secondaryRaidGroups',
                    'characters.secondaryRaidGroups.role',
                    'characters.raids',
                    'characters.recipes',
                    'roles',
                ])
                ->first();

            // For optimization, fetch characters with their attendance here and then merge them into
            // the existing characters for prios and wishlists
            $charactersWithAttendance = Guild::getAllCharactersWithAttendanceCached($guild);
            $member->characters = Character::mergeAttendance($member->characters, $charactersWithAttendance);

            return $member;
        });

        if (!$member) {
            request()->session()->flash('status', __('Member not found.'));
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $user = User::where('id', $member->user_id)->first();

        // Merge all recipes from all characters into one list
        $recipes = collect();
        foreach ($member->characters as $character) {
            foreach ($character->recipes as $recipe) {
                $recipes->add($recipe);
            }
        }

        $unassignedCharacterCount = Character::where('guild_id', $guild->id)
            ->whereNull('member_id')
            ->count();

        $showEdit = false;
        if ($member->id == $currentMember->id || $currentMember->hasPermission('edit.characters')) {
            $showEdit = true;
        }

        $showEditLoot = false;
        if ($member->id == $currentMember->id || $currentMember->hasPermission('loot.characters')) {
            $showEditLoot = true;
        }

        $viewOfficerNotePermission = false;
        if ($currentMember->hasPermission('view.officer-notes')) {
            $viewOfficerNotePermission = true;
        }

        $editOfficerNotePermission = false;
        if ($currentMember->hasPermission('edit.officer-notes')) {
            $showOfficerNotePermission = true;
        }

        return view('member.show', [
            'characters'       => $member->characters,
            'currentMember'    => $currentMember,
            'guild'            => $guild,
            'member'           => $member,
            'recipes'          => $recipes,
            'remarks'          => Raid::remarks(),
            'showEdit'         => $showEdit,
            'showEditLoot'     => $showEditLoot,
            'showPersonalNote' => ($currentMember->id == $member->id),
            'user'             => $user,
            'editOfficerNotePermission' => $editOfficerNotePermission,
            'viewOfficerNotePermission' => $viewOfficerNotePermission,
            'unassignedCharacterCount' => $unassignedCharacterCount,
        ]);
    }

    /**
     * Show the page for a member to gquit
     *
     * @return \Illuminate\Http\Response
     */
    public function showGquit($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        return view('member.gquit', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
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

        $guild->load([
            'allMembers',
            'allMembers.characters',
            'allMembers.roles',
            'raidGroups',
            'raidGroups.role',
        ]);

        $charactersWithAttendance = Guild::getAllCharactersWithAttendanceCached($guild);

        foreach ($guild->allMembers as $member) {
            $member->characters = Character::mergeAttendance($member->characters, $charactersWithAttendance);
        }

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
     * A member gquits.
     *
     * @return
     */
    public function submitGquit($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if ($currentMember->user_id == $guild->user_id) {
            request()->session()->flash('status', __('You are the guild master. The guild master may not gquit.'));
            return redirect()->back();
        }

        $updateValues = [];
        $updateValues['inactive_at'] = getDateTime();

        $currentMember->load('characters');
        // Make characters inactive
        foreach ($currentMember->characters as $character) {
            if (!$character->inactive_at) {
                $character->inactive_at = getDateTime();
                $character->save();
            }
        }

        $currentMember->update($updateValues);

        AuditLog::create([
            'description'     => $currentMember->username . ' gquit. Their profile and characters were automatically flagged as archived and were hidden.',
            'member_id'       => $currentMember->id,
            'guild_id'        => $guild->id,
            'other_member_id' => null,
        ]);

        request()->session()->flash('status', __('Successfully gquit.'));
        return redirect()->route('home');
    }

    /**
     * Toggle streamer mode on and off
     * @return
     */
    public function toggleStreamerMode() {
        $user = request()->get('currentUser');

        $toggle = ($user->is_streamer_mode ? 0 : 1);

        $user->update(['is_streamer_mode' => $toggle]);

        request()->session()->flash('status', __('Streamer mode') . ' ' . ($toggle ? __('on') : __('off')) . '. ' . __('Officer notes') . ' ' . ($toggle ? __('hidden') : __('visible')) . '.');
        return redirect()->back()->withInput(['b' => 1]);;
    }

    /**
     * Update a member
     * @return
     */
    public function update($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'allMembers' => function ($query) {
                return $query->where('members.id', request()->input('id'))
                    ->orWhere('members.username', request()->input('username'));
            },
        ]);

        $member = $guild->allMembers->where('id', request()->input('id'))->first();
        $sameNameMember = $guild->members->where('username', request()->input('username'))->first();

        if (!$member) {
            dd('test');
            abort(404, __('Guild member not found.'));
        }

        // Can't create a duplicate name
        if ($sameNameMember && ($member->id != $sameNameMember->id)) {
            abort(403, __('Name taken.'));
            request()->session()->flash('status', __('Name taken.'));
            return redirect()->back();
        }

        $validationRules = [
            'id'                   => 'required|integer|exists:members,id',
            'username'             => 'required|string|min:2|max:32',
            'public_note'          => 'nullable|string|max:140',
            'officer_note'         => 'nullable|string|max:140',
            'personal_note'        => 'nullable|string|max:2000',
            'is_wishlist_unlocked' => 'nullable|boolean',
            'is_received_unlocked' => 'nullable|boolean',
            'inactive_at'          => 'nullable|boolean',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $updateValues = [];

        if ($currentMember->hasPermission('edit.officer-notes') || $currentMember->id == $guild->user_id) {
            $updateValues['officer_note'] = request()->input('officer_note');
        }

        if ($currentMember->hasPermission('edit.characters') || $currentMember->id == $guild->user_id) {
            $updateValues['is_wishlist_unlocked'] = request()->input('is_wishlist_unlocked') == 1 ? 1 : 0;
            $updateValues['is_received_unlocked'] = request()->input('is_received_unlocked') == 1 ? 1 : 0;
        }

        if ($currentMember->id != $member->id && !$currentMember->hasPermission('edit.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit that member.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]);
        }

        $updateValues['username']    = request()->input('username');
        $updateValues['slug']        = slug(request()->input('username'));
        $updateValues['public_note'] = request()->input('public_note');

        // Member cannot make themselves inactive.
        if ($currentMember->hasPermission('inactive.characters') && $currentMember->id != $member->id &&
            ((request()->input('inactive_at') == 1 && !$member->inactive_at) || (!request()->input('inactive_at') && $member->inactive_at))
        ) {
            $updateValues['inactive_at'] = (request()->input('inactive_at') == 1 ? getDateTime() : null);
        }

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

        if (array_key_exists('officer_note', $updateValues) && ($updateValues['officer_note'] != $member->officer_note)) {
            $auditMessage .= ' (officer note)';
        }

        if (array_key_exists('is_wishlist_unlocked', $updateValues) && $updateValues['is_wishlist_unlocked'] != $member->is_wishlist_unlocked) {
            $auditMessage .= ' (wishlist ' . ($updateValues['is_wishlist_unlocked'] ? 'unlocked' : 'lock changed to use guild setting') . ')';
        }

        if (array_key_exists('is_received_unlocked', $updateValues) && $updateValues['is_received_unlocked'] != $member->is_received_unlocked) {
            $auditMessage .= ' (received loot list ' . ($updateValues['is_received_unlocked'] ? 'unlocked' : 'lock changed to use guild setting') . ')';
        }

        if (array_key_exists('inactive_at', $updateValues)) {
            $member->load('characters');
            if ($updateValues['inactive_at']) {
                // Make characters inactive
                foreach ($member->characters as $character) {
                    if (!$character->inactive_at) {
                        $character->inactive_at = getDateTime();
                        $character->save();
                    }
                }

                $auditMessage .= ' (archived, including their characters)';
            } else {
                // Make characters active
                foreach ($member->characters as $character) {
                    if ($character->inactive_at) {
                        $character->inactive_at = null;
                        $character->save();
                    }
                }

                $auditMessage .= ' (unarchived, including their characters)';
            }
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

        request()->session()->flash('status', __('Successfully updated profile.'));
        return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug, 'b' => 1]);
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

        request()->session()->flash('status', __("Successfully updated") . " " . $member->username ."'s " . __("note") . ".");
        return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug, 'b' => 1]);
    }
}
