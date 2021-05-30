@extends('layouts.app')
@section('title', (!$character ? "Create" : "Edit") . " Character - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row mb-3">
                @if ($character)
                    <div class="col-12 pt-2 bg-lightest rounded">
                        @include('character/partials/header', ['headerSize' => 1, 'showEdit' => false, 'titlePrefix' => ($character ? 'Edit ' : 'Create ')])

                        <div class="mb-3">
                            <a class="" href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}" target="_blank">
                                <span class="fas fa-fw fa-sack text-success"></span> Edit wishlist & loot
                            </a>
                        </div>
                    </div>
                @else
                    <div class="col-12 pt-2 mb-2">
                        <h1 class="font-weight-medium ">Create a Character</h1>
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
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="row mb-4">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-user"></span>
                                        Character Name
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
                                        <label for="member_id" class="font-weight-bold">

                                            Guild Member
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
                                        Class
                                    </label>
                                    <div class="form-group">
                                        <select name="class" class="form-control dark">
                                            <option value="">
                                                —
                                            </option>

                                            @foreach (App\Character::classes($guild->expansion_id) as $class)
                                                <option value="{{ $class }}" class="text-{{ strtolower($class) }}-important"
                                                    {{ old('class') ? (old('class') == $class ? 'selected' : '') : ($character && $character->class == $class ? 'selected' : '') }}>
                                                    {{ $class }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="spec" class="font-weight-bold">
                                        Spec
                                    </label>
                                    <input name="spec"
                                        maxlength="50"
                                        type="text"
                                        class="form-control dark"
                                        placeholder="eg. Fury Prot"
                                        value="{{ old('spec') ? old('spec') : ($character ? $character->spec : '') }}" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="race" class="font-weight-bold">
                                        Race
                                    </label>
                                    <div class="form-group">
                                        <select name="race" class="form-control dark">
                                            <option value="" selected>
                                                —
                                            </option>

                                            @foreach (App\Character::races($guild->expansion_id) as $race)
                                                <option value="{{ $race }}"
                                                    {{ old('race') ? (old('race') == $race ? 'selected' : '') : ($character && $character->race == $race ? 'selected' : '') }}>
                                                    {{ $race }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3 col-6">
                                <div class="form-group">
                                    <label for="level" class="font-weight-bold">
                                        Level
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

                <div class="row mb-3 pb-1 pt-2 bg-light rounded">
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label for="raid_group_id" class="font-weight-bold">
                                <span class="fas fa-fw fa-helmet-battle text-gold"></span>
                                Main Raid Group
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
                                    <span class="text-muted">locked by the man</span>
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
                                General Raid Groups
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
                                        <span class="text-muted">locked by the man</span>
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

                <div class="row mb-3 pb-1 pt-2 bg-light rounded">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="class" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-flower-daffodil"></span>
                                        Profession 1
                                    </label>
                                    <div class="form-group">
                                        <select name="profession_1" class="form-control dark">
                                            <option value="" selected>
                                                —
                                            </option>

                                            @foreach (App\Character::professions($guild->expansion_id) as $profession)
                                                <option value="{{ $profession }}"
                                                    {{ old('profession_1') ? (old('profession_1') == $profession ? 'selected' : '') : ($character && $character->profession_1 == $profession ? 'selected' : '') }}>
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
                                        Profession 2
                                    </label>
                                    <div class="form-group">
                                        <select name="profession_2" class="form-control dark">
                                            <option value="" selected>
                                                —
                                            </option>

                                            @foreach (App\Character::professions($guild->expansion_id) as $profession)
                                                <option value="{{ $profession }}"
                                                    {{ old('profession_2') ? (old('profession_2') == $profession ? 'selected' : '') : ($character && $character->profession_2 == $profession ? 'selected' : '') }}>
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
                                            PvP Rank
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
                                            PvP Rank Goal
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

                <div class="row mb-3 pb-1 pt-2 bg-light rounded">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="public_note" class="font-weight-bold">
                                <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
                                Public Note
                                <small class="text-muted">anyone in the guild can see this</small>
                            </label>
                            <textarea maxlength="140" data-max-length="140" name="public_note" rows="2" placeholder="anyone in the guild can see this" class="form-control dark">{{ old('public_note') ? old('public_note') : ($character ? $character->public_note : '') }}</textarea>
                        </div>
                    </div>

                    @if ($currentMember->hasPermission('edit.officer-notes'))
                        <div class="col-12 mt-4">
                            <div class="form-group">
                                <label for="officer_note" class="font-weight-bold">
                                    <span class="text-muted fas fa-fw fa-shield"></span>
                                    Officer Note
                                    <small class="text-muted">only officers can see this</small>
                                </label>
                                @if (isStreamerMode())
                                    Hidden in streamer mode
                                @else
                                    <textarea maxlength="140" data-max-length="140" name="officer_note" rows="2" placeholder="only officers can see this" class="form-control dark">{{ old('officer_note') ? old('officer_note') : ($character ? $character->officer_note : '') }}</textarea>
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
                                        Personal Note
                                        <small class="text-muted">only you can see this</small>
                                    </label>
                                    <textarea maxlength="2000" data-max-length="2000" name="personal_note" rows="2" placeholder="only you can see this" class="form-control dark">{{ old('personal_note') ? old('personal_note') : ($character ? $character->personal_note : '') }}</textarea>
                                </div>
                            </div>
                        @endif
                    --}}
                </div>
                <div class="row mb-3 pt-2 pb-1 bg-light rounded">
                    @if ($character && ($currentMember->hasPermission('inactive.characters') || $currentMember->id == $character->member_id))
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="inactive_at" value="1" class="" autocomplete="off"
                                            {{ old('inactive_at') && old('inactive_at') == 1 ? 'checked' : ($character->inactive_at ? 'checked' : '') }}>
                                            Archive <small class="text-muted">no longer visible</small>
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
                                            Alt Character <small class="text-muted">will be tagged as an alt</small>
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
                                            Alt Character <small class="text-muted">will be tagged as an alt</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(() => warnBeforeLeaving("#editForm"));
</script>
@endsection
