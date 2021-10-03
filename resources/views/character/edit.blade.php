@extends('layouts.app')
@section('title', (!$character ? __("Create") : __("Edit")) . " " . __("Character") . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row mb-3">
                @if ($character)
                    <div class="col-12 pt-2 bg-lightest rounded">
                        @include('character/partials/header', ['headerSize' => 1, 'showEdit' => false, 'showLogs' => true, 'showEditLoot' => true, 'titlePrefix' => ($character ? __('Edit') . ' ' : __('Create') . ' ')])
                    </div>
                @else
                    <div class="col-12 pt-2 mb-2">
                        <h1 class="font-weight-medium text-{{ getExpansionColor($guild->expansion_id) }}">
                            <span class="text-muted fas fa-fw fa-user"></span>
                            {{ __("Create a Character") }}
                        </h1>
                    </div>
                @endif
            </div>

            @if (count($errors) > 0)
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            @endif
            <form id="editForm" class="form-horizontal" role="form" method="POST" action="{{ route(($character ? 'character.update' : 'character.create'), ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="id" value="{{ $character ? $character->id : '' }}" />
                <input hidden name="create_more" value="{{ $createMore ? 1 : 0 }}" />

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-4 bg-light rounded">
                        <div class="row mb-4">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-user"></span>
                                        {{ __("Character Name") }}
                                    </label>
                                    <input name="name"
                                        maxlength="40"
                                        type="text"
                                        class="form-control dark"
                                        placeholder="eg. Gurgthock"
                                        value="{{ old('name') ? old('name') : ($character ? $character->name : '') }}" />
                                </div>
                            </div>

                            @if ($currentMember->hasPermission('edit.characters'))
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="member_id" class="font-weight-normal">
                                            <span class="text-muted fas fa-fw fa-user"></span>
                                            {{ __("Guild Member") }}
                                        </label>
                                        <div class="form-group">
                                            <select name="member_id" class="form-control dark selectpicker" data-live-search="true">
                                                <option value="">
                                                    —
                                                </option>

                                                @foreach ($guild->members as $member)
                                                    <option value="{{ $member->id }}"
                                                        data-tokens="{{ $member->id }}"
                                                        {{ old('member_id') ? (old('member_id') == $member->id ? 'selected' : '') : ($character && $character->member_id == $member->id ? 'selected' : (isset($memberId) && $memberId == $member->id ? 'selected' : '')) }}>
                                                        {{ $member->username }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="class" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-swords"></span>
                                        {{ __("Class") }}
                                    </label>
                                    <div class="form-group">
                                        <select name="class" class="form-control dark">
                                            <option value="">
                                                —
                                            </option>

                                            @foreach (App\Character::classes($guild->expansion_id) as $key => $class)
                                                <option value="{{ $key }}" class="text-{{ strtolower($key) }}-important"
                                                    {{ old('class') ? (old('class') == $key ? 'selected' : '') : ($character && $character->class == $key ? 'selected' : '') }}>
                                                    {{ $class }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="archetype" class="font-weight-bold">
                                        {{ __("Role") }}
                                    </label>
                                    <div class="form-group">
                                        <select name="archetype" class="form-control dark">
                                            <option value="" selected>
                                                —
                                            </option>
                                            @foreach (App\Character::archetypes() as $key => $archetype)
                                                <option value="{{ $key }}" {{ (old('archetype') && old('archetype') == $key) || ($character && $character->archetype == $key) ? 'selected' : '' }}>
                                                    {{ $archetype }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="spec" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-hat-wizard"></span>
                                        {{ __("Spec") }}
                                    </label>
                                    <div class="form-group">
                                        @php
                                            // Support for custom specs
                                            $found = false;
                                            $oldSpec = old('spec') ? old('spec') : ($character ? $character->spec : null);
                                        @endphp
                                        <select name="spec" class="form-control dark" {{ !$oldSpec ? 'disabled' : '' }}>
                                            <option value="" selected>
                                                —
                                            </option>
                                            @foreach (App\Character::specs($guild->expansion_id) as $key => $spec)
                                                @php
                                                    $isOldSpec = $oldSpec == $key;
                                                    if ($isOldSpec) {
                                                        $found = true;
                                                    }
                                                @endphp
                                                <option value="{{ $key }}"
                                                    data-class="{{ $spec['class'] }}"
                                                    data-archetype="{{ $spec['archetype'] }}"
                                                    data-icon="{{ $spec['icon'] }}"
                                                    {{ $isOldSpec ? 'selected' : '' }}>
                                                    {{ $spec['name'] }}
                                                </option>
                                            @endforeach
                                            @if (!$found && $oldSpec)
                                                <option value="{{ $oldSpec }}" selected>
                                                    {{ $oldSpec }}
                                                </option>
                                            @endif
                                        </select>
                                        <input class="selectInput"" style="display:none;" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="spec_label" class="font-weight-normal text-muted">
                                        {{ __("Spec Label") }}
                                        <span class="text-muted small">{{ __("optional") }}</span>
                                    </label>
                                    @php
                                        $oldSpecLabel = old('spec_label') ? old('spec_label') : ($character ? $character->spec_label : '');
                                    @endphp
                                    <input name="spec_label"
                                        {{ !$oldSpecLabel && !$oldSpec ? 'disabled' : '' }}
                                        maxlength="50"
                                        type="text"
                                        class="form-control dark"
                                        placeholder="{{ __('eg. Boomkin') }}"
                                        value="{{ $oldSpecLabel }}" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="race" class="font-weight-normal">
                                        {{ __("Race") }}
                                    </label>
                                    <div class="form-group">
                                        <select name="race" class="form-control dark">
                                            <option value="" selected>
                                                —
                                            </option>

                                            @foreach (App\Character::races($guild->expansion_id) as $key => $race)
                                                <option value="{{ $key }}"
                                                    {{ old('race') ? (old('race') == $key ? 'selected' : '') : ($character && $character->race == $key ? 'selected' : '') }}>
                                                    {{ $race }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3 col-6">
                                <div class="form-group">
                                    <label for="level" class="text-muted font-weight-normal">
                                        {{ __("Level") }}
                                    </label>
                                    <input name="level"
                                        type="number"
                                        min="1"
                                        max="{{ $guild->getMaxLevel() }}"
                                        class="form-control dark"
                                        placeholder="0"
                                        value="{{ old('level') ? old('level') : ($character ? $character->level : $guild->getMaxLevel()) }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4 pb-1 pt-2 bg-light rounded">
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label for="raid_group_id" class="font-weight-bold">
                                <span class="fas fa-fw fa-helmet-battle text-gold"></span>
                                {{ __("Main Raid Group") }}
                            </label>
                            @if ($editRaidGroups)
                                <div class="form-group">
                                    <select name="raid_group_id" class="form-control dark">
                                        <option value="" selected>
                                            —
                                        </option>

                                        @foreach ($guild->raidGroups as $raidGroup)
                                            <option value="{{ $raidGroup->id }}"
                                                style="color:{{ $raidGroup->getColor() }};"
                                                {{ old('raid_group_id') ? (old('raid_group_id') == $raidGroup->id ? 'selected' : '') : ($character && $character->raidGroup && $character->raidGroup->id == $raidGroup->id ? 'selected' : '') }}>
                                                {{ $raidGroup->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div>
                                    <span class="text-muted">{{ __("locked by the man") }}</span>
                                </div>
                                <div>
                                    @if ($character && $character->raidGroup)
                                        @php
                                            $raidGroupColor = null;
                                            if ($character->raidGroup->relationLoaded('role')) {
                                                $raidGroupColor = $character->raidGroup->getColor();
                                            }
                                        @endphp
                                        @include('partials/raidGroup', ['raidGroup' => $character->raidGroup])
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label for="raid_groups" class="font-weight-bold">
                                <span class="fas fa-fw fa-helmet-battle text-muted"></span>
                                {{ __("General Raid Groups") }}
                            </label>
                            <div class="form-group">
                                @if ($editRaidGroups)
                                    <select name="raid_groups" class="js-input-select form-control dark selectpicker" autocomplete="off">
                                        <option value="" selected>
                                            —
                                        </option>

                                        @foreach ($guild->raidGroups as $raidGroup)
                                            <option value="{{ $raidGroup->id }}"
                                                data-tokens="{{ $raidGroup->id }}"
                                                style="color:{{ $raidGroup->getColor() }};">
                                                {{ $raidGroup->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <ol class="list-inline mt-3">
                                        @for ($i = 0; $i < $maxRaidGroups; $i++)
                                            <li class="list-unstyled pt-1 pb-2 pr-2 mt-1 bg-dark rounded"
                                                style="{{ old('raid_groups.' . $i) || ($character && $character->secondaryRaidGroups->get($i)) ? '' : 'display:none;' }}">
                                                <input type="checkbox" checked
                                                    name="raid_groups[{{ $i }}]"
                                                    value="{{ old('raid_groups.' . $i) ? old('raid_groups.' . $i) : ($character && $character->secondaryRaidGroups->get($i) ? $character->secondaryRaidGroups->get($i)->id : '') }}"
                                                    class="js-input-item"
                                                    style="display:none;">
                                                <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                                @php
                                                    $label = '';
                                                    $raidGroup = null;
                                                    if (old('raid_groups.' . $i)) {
                                                        $raidGroup = $guild->allRaidGroups->where('id', old('raid_groups.' . $i))->first();
                                                    } else if ($character && $character->secondaryRaidGroups->get($i)) {
                                                        $raidGroup = $guild->allRaidGroups->where('id',  $character->secondaryRaidGroups->get($i)->id)->first();
                                                    }

                                                    if ($raidGroup) {
                                                        $label = $raidGroup->name;
                                                    }
                                                @endphp
                                                <span class="js-input-label">
                                                    @if ($raidGroup)
                                                        @php
                                                            $raidGroupColor = null;
                                                            if ($raidGroup->relationLoaded('role')) {
                                                                $raidGroupColor = $raidGroup->getColor();
                                                            }
                                                        @endphp
                                                        @include('partials/raidGroup', ['text' => 'muted'])
                                                    @endif
                                                </span>
                                            </li>
                                        @endfor
                                    </ol>
                                @else
                                    <div>
                                        <span class="text-muted">{{ __("locked by the man") }}</span>
                                    </div>
                                    <div>
                                        @if ($character && $character->secondaryRaidGroups->count())
                                            <ul class="list-inline">
                                                @foreach ($character->secondaryRaidGroups as $raidGroup)
                                                    <li class="list-inline-item">
                                                        @php
                                                            $raidGroupColor = null;
                                                            if ($raidGroup->relationLoaded('role')) {
                                                                $raidGroupColor = $raidGroup->getColor();
                                                            }
                                                        @endphp
                                                        @include('partials/raidGroup')
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4 pb-1 pt-2 bg-light rounded">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="class" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-flower-daffodil"></span>
                                        {{ __("Profession 1") }}
                                    </label>
                                    <div class="form-group">
                                        <select name="profession_1" class="form-control dark">
                                            <option value="" selected>
                                                —
                                            </option>

                                            @foreach (App\Character::professions($guild->expansion_id) as $key => $profession)
                                                <option value="{{ $key }}"
                                                    {{ old('profession_1') ? (old('profession_1') == $key ? 'selected' : '') : ($character && $character->profession_1 == $key ? 'selected' : '') }}>
                                                    {{ $profession }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="class" class="font-weight-bold">
                                        {{ __("Profession 2") }}
                                    </label>
                                    <div class="form-group">
                                        <select name="profession_2" class="form-control dark">
                                            <option value="" selected>
                                                —
                                            </option>

                                            @foreach (App\Character::professions($guild->expansion_id) as $key => $profession)
                                                <option value="{{ $key }}"
                                                    {{ old('profession_2') ? (old('profession_2') == $key ? 'selected' : '') : ($character && $character->profession_2 == $key ? 'selected' : '') }}>
                                                    {{ $profession }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($guild->expansion_id === 1)
                            <div class="row mt-4">
                                <div class="col-sm-3 col-6">
                                    <div class="form-group">
                                        <label for="rank" class="font-weight-bold">
                                            <span class="text-muted fas fa-fw fa-swords"></span>
                                            {{ __("PvP Rank") }}
                                        </label>
                                        <input name="rank"
                                            type="number"
                                            min="1"
                                            max="14"
                                            class="form-control dark"
                                            placeholder="—"
                                            value="{{ old('rank') ? old('rank') : ($character ? $character->rank : '') }}" />
                                    </div>
                                </div>


                                <div class="col-sm-3 col-6">
                                    <div class="form-group">
                                        <label for="rank_goal" class="font-weight-bold">
                                            {{ __("PvP Rank Goal") }}
                                        </label>
                                        <input name="rank_goal"
                                            type="number"
                                            min="1"
                                            max="14"
                                            class="form-control dark"
                                            placeholder="—"
                                            value="{{ old('rank_goal') ? old('rank_goal') : ($character ? $character->rank_goal : '') }}" />
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row mb-4 pb-1 pt-2 bg-light rounded">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="public_note" class="font-weight-bold">
                                <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
                                {{ __("Public Note") }}
                                <small class="text-muted">
                                    {{ __("anyone in the guild can see this") }}
                                </small>
                            </label>
                            <textarea maxlength="140" data-max-length="140" name="public_note" rows="2" placeholder="{{ __('anyone in the guild can see this') }}" class="form-control dark">{{ old('public_note') ? old('public_note') : ($character ? $character->public_note : '') }}</textarea>
                        </div>
                    </div>

                    @if ($currentMember->hasPermission('edit.officer-notes'))
                        <div class="col-12 mt-4">
                            <div class="form-group">
                                <label for="officer_note" class="font-weight-bold">
                                    <span class="text-muted fas fa-fw fa-shield"></span>
                                    {{ __("Officer Note") }}
                                    <small class="text-muted">
                                        {{ __("only officers can see this") }}
                                    </small>
                                </label>
                                @if (isStreamerMode())
                                    {{ __("Hidden in streamer mode") }}
                                @else
                                    <textarea maxlength="140" data-max-length="140" name="officer_note" rows="2" placeholder="{{ __('only officers can see this') }}" class="form-control dark">{{ old('officer_note') ? old('officer_note') : ($character ? $character->officer_note : '') }}</textarea>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{--
                        @if ($currentMember->id == $character->member_id)
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="personal_note" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-eye-slash"></span>
                                        {{ __("Personal Note") }}
                                        <small class="text-muted">
                                            {{ __("only you can see this") }}
                                        </small>
                                    </label>
                                    <textarea maxlength="2000" data-max-length="2000" name="personal_note" rows="2" placeholder="{{ __('only you can see this') }}" class="form-control dark">{{ old('personal_note') ? old('personal_note') : ($character ? $character->personal_note : '') }}</textarea>
                                </div>
                            </div>
                        @endif
                    --}}
                </div>

                <div class="row mb-4 pt-2 pb-1 bg-light rounded">
                    @if ($character && $character->member)
                        <div class="col-12">
                            <div class="form-group">
                                <a href="{{ route('member.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $character->member->id, 'usernameSlug' => $character->member->slug]) }}">
                                    {{ __("Unlock member's wishlist or received loot list") }}
                                    <!-- TODO: Swap over to once translations are added: {{ __("Click here to unlock member's wishlist or received loot list") }} -->
                                </a>
                            </div>
                        </div>
                    @endif
                    @if ($character && ($currentMember->hasPermission('inactive.characters') || $currentMember->id == $character->member_id))
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="inactive_at" value="1" class="" autocomplete="off"
                                            {{ old('inactive_at') && old('inactive_at') == 1 ? 'checked' : ($character->inactive_at ? 'checked' : '') }}>
                                            {{ __("Archive") }} <small class="text-muted">
                                                {{ __("no longer visible") }}
                                            </small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_alt" value="1" class="" autocomplete="off"
                                            {{ old('is_alt') && old('is_alt') == 1 ? 'checked' : ($character->is_alt ? 'checked' : '') }}>
                                            {{ __("Alt Character") }} <small class="text-muted">
                                                {{ __("will be tagged as an alt") }}
                                            </small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_alt" value="1" class="" autocomplete="off">
                                            {{ __("Alt Character") }} <small class="text-muted">
                                                {{ __("will be tagged as an alt") }}
                                            </small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> {{ __("Save") }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        warnBeforeLeaving("#editForm")
    });
</script>
<script src="{{ loadScript('characterEdit.js') }}"></script>
@endsection
