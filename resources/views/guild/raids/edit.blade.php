@extends('layouts.app')
@section('title', (!$character ? "Create" : "Edit") . " Raid - " . config('app.name')

)@php
    $maxDate = (new \DateTime())->modify('+1 day')->format('Y-m-d');

    // Iterating over 100+ characters 100+ items results in TENS OF THOUSANDS OF ITERATIONS.
    // So we're iterating over the characters only one time, saving the results, and printing them.
    $characterSelectOptions = (string)View::make('partials.characterOptions', ['characters' => $guild->characters]);
@endphp

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row mb-3">
                @if ($raid)
                    <div class="col-12 pt-2 bg-lightest rounded">
                        {{ $raid->name }}
                    </div>
                @else
                    <div class="col-12 pt-2 mb-2">
                        <h1 class="font-weight-medium ">Create a Raid</h1>
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
            <form id="editForm" class="form-horizontal" role="form" method="POST" action="{{ route(($raid ? 'guild.raids.update' : 'guild.raids.create'), ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="id" value="{{ $raid ? $raid->id : '' }}" />

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="row mb-4">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-helmet-battle"></span>
                                        Raid Name
                                    </label>
                                    <input name="name"
                                        maxlength="40"
                                        type="text"
                                        class="form-control dark"
                                        placeholder="eg. Gurgthock"
                                        value="{{ old('name') ? old('name') : ($character ? $character->name : '') }}" />
                                </div>
                            </div>

                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="date" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-calendar"></span>
                                        Date
                                    </label>
                                    <input name="date"
                                        type="date"
                                        class="form-control dark"
                                        value="{{ old('date') ? old('date') : ($raid ? $raid->date : '') }}" />
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="logs" class="font-weight-bold">
                                        Link to Logs
                                    </label>
                                    <input name="logs"
                                        maxlength="255"
                                        type="text"
                                        class="form-control dark"
                                        placeholder="a Warcraft Logs link perhaps?"
                                        value="{{ old('logs') ? old('logs') : ($raid ? $raid->logs : '') }}" />
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
TODO beyond this point
                <div class="row mb-3 pb-1 pt-2 bg-light rounded">
                    @for ($i = 0; $i++; $i < $maxRaids)
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label for="raid_group_id[{{ $i }}]" class="font-weight-bold">
                                <span class="fas fa-fw fa-users text-dk"></span>
                                Raid Group {{ $i }}
                            </label>
                            <div class="form-group">
                                <select name="raid_group_id{{ $i }}" class="form-control dark">
                                    <option value="" selected>
                                        —
                                    </option>

                                    @foreach ($raidGroups as $raidGroup)
                                        <option value="{{ $raidGroup->id }}"
                                            style="color:{{ $raidGroup->getColor() }};"
                                            {{ old('raid_group_id') TODO ? (old('raid_group_id') TODO == $raidGroup->id ? 'selected' : '') : (TODO ? 'selected' : '') }}>
                                            {{ $raidGroup->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3 pb-1 pt-2 bg-light rounded">
                    @for ($i = 0; $i++; $i < $maxInstances)
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label for="instance_id[{{ $i }}]" class="font-weight-bold">
                                <span class="fas fa-fw fa-users text-dk"></span>
                                Dungeon {{ $i }}
                            </label>
                            <div class="form-group">
                                <select name="instance_id{{ $i }}" class="form-control dark">
                                    <option value="" selected>
                                        —
                                    </option>

                                    @foreach ($instances as $instance)
                                        <option value="{{ $instance->id }}"
                                            style="color:{{ $instance->getColor() }};"
                                            {{ old('instance_id') TODO ? (old('instance_id') TODO == $instance->id ? 'selected' : '') : (TODO ? 'selected' : '') }}>
                                            {{ $instance->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
Not touched beyond this point
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
                                            Inactive <small class="text-muted">no longer visible</small>
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
