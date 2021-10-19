@extends('layouts.app')
@section('title', ($raid ? ($copy ? __("Copy") : __("Edit")) : __("Create")) . " " . __("Raid") . " - " . config('app.name'))

@php
    $date = null;
    if (old('date')) {
        $date = old('date');
    } else if ($raid && $raid->date) {
        $date = $raid->date;
    }

    $maxDate = (new \DateTime())->modify('+2 year')->format('Y-m-d');

    // Iterating over 100+ characters 100+ items results in TENS OF THOUSANDS OF ITERATIONS.
    // So we're iterating over the characters only one time, saving the results, and printing them.

    $characterSelectOptions = (string)View::make('partials.characterOptions', [
        'characters'     => $selectableCharacters,
        'guild'          => $guild,
        'raidGroups'     => $guild->raidGroups,
        'showAttendance' => true,
    ]);

    $remarkSelectOptions = (string)View::make('partials.remarkOptions');
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-10 offset-xl-1 col-12">
            <div class="row mb-3">
                <div class="col-12 pt-2 bg-lightest rounded">
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <h1 class="font-weight-medium ">
                                @if ($raid)
                                    @if ($copy)
                                        <span class="text-muted fas fa-fw fa-copy"></span>
                                        {{ __("Copying") }}
                                    @else
                                        <span class="text-muted fas fa-fw fa-pencil"></span>
                                        {{ __("Editing") }}
                                    @endif
                                    <a href="{{ route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => ($copy ? $originalRaid->id : $raid->id), 'raidSlug' => ($copy ? $originalRaid->slug : $raid->slug)]) }}"
                                        class="text-white">
                                        {{ $copy ? $originalRaid->name : $raid->name }}
                                    </a>
                                @else
                                    <span class="text-muted fas fa-fw fa-plus"></span>
                                    {{ __("Create a Raid") }}
                                @endif
                            </h1>
                        </li>

                            @if ($raid && !$copy)
                                <li class="list-inline-item">
                                    <a class="text-success" href="{{ route('item.assignLoot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $raid->id]) }}">
                                        <span class="fa-fw fas fa-sack"></span>
                                        {{ __("Assign Loot") }}
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.raids.copy', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id]) }}">
                                        <span class="fas fa-fw fa-copy"></span> {{ __("copy") }}
                                    </a>
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $raid->id]) }}">
                                        <span class="fas fa-clipboard-list-check"></span>
                                        {{ __("history") }}
                                    </a>
                                </li>
                            @endif
                    </ul>
                </div>
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

            @include('partials/loadingBars')

            <form id="editForm"
                style="display:none;"
                class="form-horizontal"
                role="form"
                method="POST"
                action="{{ route(($raid && !$copy ? 'guild.raids.update' : 'guild.raids.create'), ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="id" value="{{ $raid ? $raid->id : '' }}" />

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded {{ $errors->has('raid.date') ? 'text-danger font-weight-bold' : '' }}">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="date" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-calendar"></span>
                                        {{ __("Date") }} <span class="small text-muted">{{ __("your local time") }}</span>
                                    </label>
                                    <div>
                                        <input class="js-date" type="text" name="date" hidden value="{{ $date }}">
                                        <input
                                            class="js-date-input"
                                            required
                                            type="text"
                                            min="2004-09-22"
                                            max="{{ $maxDate }}"
                                            value="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-12 {{ $errors->has('raid.name') ? 'text-danger font-weight-bold' : '' }}">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">
                                        <span class="text-dk fas fa-fw fa-helmet-battle"></span>
                                        {{ __("Raid Name") }}
                                    </label>
                                    <input name="name"
                                        required
                                        autocomplete="off"
                                        maxlength="75"
                                        type="text"
                                        class="form-control dark"
                                        placeholder="{{ __('eg. MC Binding Run 42') }}"
                                        value="{{ old('name') ? old('name') : ($raid ? $raid->name : '') }}" />
                                </div>
                            </div>

                            @if ($raid && !$copy)
                                <div class="col-12 {{ $errors->has('raid.is_cancelled') ? 'text-danger font-weight-bold' : '' }}">
                                    <div class="form-group mt-2">
                                        <div class="checkbox text-warning">
                                            <label>
                                                <input type="checkbox" name="is_cancelled" value="1" class="" autocomplete="off"
                                                    {{ old('is_cancelled') && old('is_cancelled') == 1 ? 'checked' : ($raid && $raid->cancelled_at ? 'checked' : '') }}>
                                                    {{ __("Cancelled") }} <small class="text-muted">{{ __("attendance will not count") }}</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 {{ $errors->has('raid.is_archived') ? 'text-danger font-weight-bold' : '' }}">
                                    <div class="form-group">
                                        <div class="checkbox text-danger">
                                            <label>
                                                <input type="checkbox" name="is_archived" value="1" class="" autocomplete="off"
                                                    {{ old('is_archived') && old('is_archived') == 1 ? 'checked' : ($raid && $raid->archived_at ? 'checked' : '') }}>
                                                    {{ __("Archived") }} <small class="text-muted">{{ __("raid will be hidden") }}</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-12 {{ $errors->has('raid.public_note') ? 'text-danger font-weight-bold' : '' }}">
                                <div class="form-group">
                                    <label for="public_note" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
                                        {{ __("Public Note") }}
                                        <small class="text-muted">{{ __("anyone in the guild can see this") }}</small>
                                    </label>
                                    <textarea autocomplete="off"  maxlength="250" data-max-length="250" name="public_note" rows="2" placeholder="{{ __('anyone in the guild can see this') }}" class="form-control dark">{{ old('public_note') ? old('public_note') : ($raid ? $raid->public_note : '') }}</textarea>
                                </div>
                            </div>

                            @if ($currentMember->hasPermission('edit.officer-notes'))
                                <div class="col-lg-6 col-12 {{ $errors->has('raid.officer_note') ? 'text-danger font-weight-bold' : '' }}">
                                    <div class="form-group">
                                        <label for="officer_note" class="font-weight-bold">
                                            <span class="text-muted fas fa-fw fa-shield"></span>
                                            {{ __("Officer Note") }}
                                            <small class="text-muted">{{ __("only officers can see this") }}</small>
                                        </label>
                                        @if (isStreamerMode())
                                        <div class="mt-1">
                                            {{ __("Officer note is hidden in streamer mode") }}
                                        </div>
                                        @else
                                            <textarea autocomplete="off" maxlength="250" data-max-length="250" name="officer_note" rows="2" placeholder="{{ __('only officers can see this') }}" class="form-control dark">{{ old('officer_note') ? old('officer_note') : ($raid ? $raid->officer_note : '') }}</textarea>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-12 {{ $errors->has('raid.logs') ? 'text-danger font-weight-bold' : '' }}">
                                <div class="form-group">
                                    <label for="logs" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-link"></span>
                                        {{ __("Link to Raid Logs") }} <span class="text-muted small">{{ __("max") }} {{ $maxLogs }}</span>
                                    </label>

                                    @if ($raid && $raid->logs_deprecated)
                                        <!-- DEPRECATED -->
                                        <div class="text-warning">
                                            {{ __("This raid is using the old raid logs feature (only supports one log). To use the new raid logs feature, remove this log and save the raid. You'll then be able to come back and use the new logs feature on this raid.") }}
                                        </div>
                                        <input name="logs_deprecated"
                                            autocomplete="off"
                                            maxlength="250"
                                            type="text"
                                            class="form-control dark"
                                            placeholder="{{ __('eg. https://classic.warcraftlogs.com/reports/AbCdE3FgHiJkLmNo') }}"
                                            value="{{ old('logs_deprecated') ? old('logs_deprecated') : ($raid ? $raid->logs_deprecated : '') }}"
                                            style="" />
                                    @else
                                        @for ($i = 0; $i < $maxLogs; $i++)
                                            @php
                                                $oldLog = old('logs.' . $i . '.name') ? old('logs.' . $i . '.name') : ($raid && $raid->logs->slice($i, 1)->count() ? $raid->logs->slice($i, 1)->first()->name : null);
                                            @endphp
                                            <div class="form-group {{ $i > 0 ? 'js-hide-empty' : '' }}" style="{{ !$oldLog && $i > 0 ? 'display:none;' : '' }}">
                                                <input name="logs[{{ $i }}][name]"
                                                    autocomplete="off"
                                                    maxlength="250"
                                                    type="text"
                                                    class="js-show-next form-control dark"
                                                    placeholder="{{ __('eg. https://classic.warcraftlogs.com/reports/AbCdE3FgHiJkLmNo') }}"
                                                    value="{{ $oldLog }}"
                                                    style="" />
                                            </div>
                                        @endfor
                                        @if ($guild->warcraftlogs_token)
                                            <span id="addWarcraftlogsAttendees" class="btn btn-success">
                                                <span class="fas fa-fw fa-file-import"></span>
                                                {{ __("Import Characters from Warcraft Logs") }}
                                            </span>
                                            <div class="js-warcraftlogs-attendees-message text-warning mt-2" style="display:none;">
                                            </div>
                                            @include('partials/loadingBars', ['loadingBarId' => 'warcraftlogsLoadingbar'])
                                        @else
                                            <span class="disabled btn btn-success">
                                                <span class="fas fa-fw fa-file-import"></span>
                                                {{ __("Import Attendees from Warcraft Logs") }}
                                            </span>
                                            <br>
                                            <span class="small text-warning font-weight-normal">
                                                {!! __('<a href=":link" target="_blank">Connect</a> a Warcraft Logs account', ['link' => route('warcraftlogsAuth', ['guildId' => $guild->id, 'guildSlug' => $guild->slug])]) !!}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3 mb-3 pb-1 pt-2 bg-light rounded">
                    <div class="col-xl-4 col-sm-6 col-12 {{ $errors->has('raid.instance_id.*') ? 'text-danger font-weight-bold' : '' }}">
                        <label for="instance_id[0]" class="font-weight-bold">
                            <span class="fas fa-fw fa-dungeon text-muted"></span>
                            {{ __("Dungeon(s)") }}
                            <span class="text-muted small">{{ __("max") }} {{ $maxInstances }}</span>
                        </label>
                        @for ($i = 0; $i < $maxInstances; $i++)
                            @php
                                $oldInstanceId = old('instance_id.' . $i) ? old('instance_id.' . $i) : ($raid && $raid->instances->slice($i, 1)->count() ? $raid->instances->slice($i, 1)->first()->id : null);
                            @endphp
                            <div class="form-group {{ $i > 0 ? 'js-hide-empty' : '' }}" style="{{ !$oldInstanceId && $i > 0 ? 'display:none;' : '' }}">
                                <select name="instance_id[]" class="js-show-next form-control dark" autocomplete="off">
                                    <option value="" selected>
                                        —
                                    </option>

                                    @foreach ($instances as $instance)
                                        <option value="{{ $instance->id }}"
                                            {{ $oldInstanceId == $instance->id ? 'selected' : '' }}>
                                            {{ $instance->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endfor
                    </div>

                    <div class="offset-xl-2 col-xl-4 col-sm-6 col-12 {{ $errors->has('raid.raid_group_id.*') ? 'text-danger font-weight-bold' : '' }}">
                        <label for="raid_group_id[0]" class="font-weight-bold">
                            <span class="fas fa-fw fa-users text-muted"></span>
                            {{ __("Raid Group(s)") }} <span class="text-muted small">{{ __("max") }} {{ $maxRaidGroups }}</span>
                        </label>
                        @for ($i = 0; $i < $maxRaidGroups; $i++)
                            <div class="form-group {{ $i > 0 ? 'js-hide-empty' : '' }}" style="{{ $i > 0 ? 'display:none;' : '' }}">
                                <select name="raid_group_id[]" class="form-control dark {{ $i >= 0 ? 'js-show-next' : '' }}" autocomplete="off">
                                    <option value="" selected>
                                        —
                                    </option>

                                    @foreach ($guild->raidGroups as $raidGroup)
                                        <option value="{{ $raidGroup->id }}"
                                            style="color:{{ $raidGroup->getColor() }};"
                                            {{ old('raid_group_id.' . $i) && old('raid_group_id.' . $i) == $raidGroup->id ? 'selected' : ($raid && $raid->raidGroups->slice($i, 1)->count() && $raid->raidGroups->slice($i, 1)->first()->id == $raidGroup->id ? 'selected' : '') }}>
                                            {{ $raidGroup->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endfor
                        <div class="form-group mb-0">
                            <div class="checkbox">
                                <label>
                                    <input checked type="checkbox" name="add_raiders" value="1" class="" autocomplete="off">
                                        <small class="text-muted">{{ __("auto-add raiders") }}</small>
                                </label>
                            </div>
                        </div>
                        <div class="js-raid-group-message text-warning" style="display:none;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2">
                        <ul class="list-inline">
                            <li class="list-inline-item">
                                <h2 class="font-weight-medium ">
                                    <span class="fas fa-fw fa-users text-muted"></span>
                                    {{ __("Attendees") }}
                                </h2>
                            </li>
                            <li class="list-inline-item">
                                <span class="js-clear-attendees text-link cursor-pointer"><span class="fas fa-fw fa-trash"></span> {{ __("clear attendees") }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mt-3 mb-3 bg-light rounded numbered-start">
                        @include('partials/loadingBars', ['loadingBarId' => 'attendeesLoadingbar'])
                        <fieldset>
                            @for ($i = 0; $i < $maxCharacters; $i++)
                                @php
                                    $characterId = 'characters.' . $i . '.id';
                                    $character = $raid && $raid->characters->slice($i, 1)->count() ? $raid->characters->slice($i, 1)->first() : null;

                                    $hide = false;

                                    if ($i > 2) {
                                        if (old($characterId) && old($characterId) == null) {
                                            $hide = true;
                                        } else if (!old($characterId) && !$character) {
                                            $hide = true;
                                        }
                                    }
                                @endphp

                                <div class="js-row row striped-light pb-0 {{ $i == 0 ? 'pt-3' : 'pt-0' }} rounded {{ $i > 2 ? 'js-hide-empty' : '' }}" style="{{ $hide ? 'display:none;' : '' }}">

                                    <!-- Exempt -->
                                    <div class="col-lg-1 col-2 {{ $errors->has('characters.' . $i . '.is_exempt') ? 'text-danger font-weight-bold' : '' }}">
                                        <div class="form-group mb-1 text-center">
                                            <label for="characters[{{ $i }}][is_exempt]">
                                                @if ($i == 0)
                                                    <span class="fas fa-fw fa-times text-muted"></span>
                                                    <span class="font-weight-bold">
                                                        {{ __("Excused") }}
                                                    </span>
                                                @else
                                                    <span class="small text-muted">
                                                        {{ __("excused") }}
                                                    </span>
                                                @endif
                                            </label>
                                            <div class="checkbox numbered text-muted">
                                                <label title="skip this character's attendance check">
                                                    <input data-index="{{ $i }}" class="js-attendance-skip" type="checkbox" name="characters[{{ $i }}][is_exempt]" value="1" autocomplete="off"
                                                        {{ old('characters.' . $i . '.is_exempt') && old('characters.' . $i . '.is_exempt') == 1  ? 'checked' : (!old('characters.' . $i . '.is_exempt') && $character && $character->pivot->is_exempt ? 'checked' : '') }}>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-11 col-10">
                                        <div class="row">
                                            <!-- Character dropdown -->
                                            <div class="col-xl-5 col-lg-5 col-sm-6 col-12">
                                                <div class="form-group mb-1 {{ $errors->has('characters.' . $i . '.character_id') ? 'text-danger font-weight-bold' : '' }}">

                                                    <label for="characters[{{ $i }}][character_id]" class="font-weight-bold">
                                                        @if ($i == 0)
                                                            <span class="fas fa-fw fa-user text-muted"></span>
                                                            {{ __("Character") }}
                                                        @else
                                                            <span class="sr-only">
                                                                {{ __("Character") }}
                                                            </span>
                                                        @endif
                                                    </label>

                                                    @php
                                                        $oldCharacterId = old('characters.' . $i . '.character_id') ? old('characters.' . $i . '.character_id') : (!old('characters.' . $i . '.character_id') && $character ? $character->pivot->character_id : '');
                                                        if ($oldCharacterId) {
                                                            // Select the correct option
                                                            $options = str_replace('hack="' . $oldCharacterId . '"', 'selected', $characterSelectOptions);
                                                        } else {
                                                            $options = $characterSelectOptions;
                                                        }
                                                     @endphp

                                                    <select name="characters[{{ $i }}][character_id]" class="js-show-next-character form-control dark
                                                        {{ $errors->has('characters.' . $i . '.character_id') ? 'form-danger' : '' }}
                                                        {{ $hide ? '' : 'selectpicker' }}"
                                                        data-live-search="true"
                                                        autocomplete="off">
                                                        <option value="">
                                                            —
                                                        </option>

                                                        {{-- See the notes at the top for why the options look like this --}}
                                                        @if ($oldCharacterId)
                                                             {!! $options !!}
                                                        @else
                                                            {!! $characterSelectOptions !!}
                                                        @endif
                                                    </select>

                                                    @if ($errors->has('characters.' . $i))
                                                        <div class="'text-danger font-weight-bold'">
                                                            {{ $errors->first('characters.' . $i) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Remarks dropdown -->
                                            <div class="col-lg-4 col-sm-6 col-12">
                                                <div class="form-group mb-1 {{ $errors->has('characters.' . $i . '.remark_id') ? 'text-danger font-weight-bold' : '' }}">

                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <label for="characters[{{ $i }}][remark_id]" class="font-weight-bold full-width">
                                                                @if ($i == 0)
                                                                    <span class="fas fa-fw fa-quote-left text-muted"></span>
                                                                    {{ __("Note") }}
                                                                @else
                                                                    <span class="sr-only">
                                                                        {{ __("Note") }}
                                                                    </span>
                                                                @endif
                                                            </label>
                                                        </div>
                                                        <div>
                                                            <span data-index="{{ $i }}" class="js-show-notes text-link cursor-pointer font-weight-light">+ {{ __("custom note") }}</span>
                                                        </div>
                                                    </div>

                                                    <select name="characters[{{ $i }}][remark_id]" class="form-control dark {{ $errors->has('characters.' . $i . '.remark_id') ? 'form-danger' : '' }}" data-live-search="true" autocomplete="off">
                                                        <option value="">
                                                            —
                                                        </option>

                                                        {{-- See the notes at the top for why the options look like this --}}
                                                        @if (old('characters.' . $i . '.remark_id') || $character)
                                                            @php
                                                                $oldRemark = old('characters.' . $i . '.remark_id') ? old('characters.' . $i . '.remark_id') : (!old('characters.' . $i . '.remark_id') && $character ? $character->pivot->remark_id : '');
                                                                if ($oldRemark) {
                                                                    // Select the correct option
                                                                    $options = str_replace('hack="' . $oldRemark . '"', 'selected', $remarkSelectOptions);
                                                                } else {
                                                                    $options = $remarkSelectOptions;
                                                                }
                                                             @endphp
                                                             {!! $options !!}
                                                        @else
                                                            {!! $remarkSelectOptions !!}
                                                        @endif
                                                    </select>

                                                    @if ($errors->has('characters.' . $i))
                                                        <div class="'text-danger font-weight-bold'">
                                                            {{ $errors->first('characters.' . $i) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Credit slider -->
                                            <div class="col-lg-3 col-sm-6 col-12">
                                                <div class="form-group mb-1 text-center {{ $errors->has('characters.' . $i . '.credit') ? 'text-danger font-weight-bold' : '' }}">

                                                    <label for="characters[{{ $i }}][credit]" class="font-weight-bold">
                                                        @if ($i == 0)
                                                            <span class="fas fa-fw fa-user-chart text-muted"></span>
                                                            {{ __("Attendance Credit") }}
                                                        @else
                                                            <span class="sr-only">
                                                                {{ __("Attendance Credit") }}
                                                            </span>
                                                        @endif
                                                    </label>

                                                    <div data-attendance-input="{{ $i }}" class="small text-muted {{ $errors->has('characters.' . $i . '.credit') ? 'form-danger' : '' }}">
                                                        <input type="text"
                                                            name="characters[{{ $i }}][credit]"
                                                            autocomplete="off"
                                                            data-provide="slider"
                                                            data-slider-ticks="[0.0, 0.25, 0.5, 0.75, 1.0]"
                                                            data-slider-ticks-labels='["0%", "25%", "50%", "75%", "100%"]'
                                                            data-slider-min="0"
                                                            data-slider-max="1"
                                                            data-slider-step="0.25"
                                                            data-slider-value="{{ old('characters.' . $i . '.credit') ? old('characters.' . $i . '.credit') : (! old('characters.' . $i . '.credit') && $character ? $character->pivot->credit : 1) }}"
                                                            data-slider-tooltip="hide" />
                                                    </div>
                                                    <div data-attendance-skip-note="{{ $i }}" class="text-warning" style="display:none;">
                                                        {{ __("Attendance skipped - this won't count against their overall attendance") }}
                                                    </div>

                                                    @if ($errors->has('characters.' . $i))
                                                        <div class="'text-danger font-weight-bold'">
                                                            {{ $errors->first('characters.' . $i) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div data-index="{{ $i }}" class="js-notes col-12 mb-3"
                                                style="{{ old('characters.' . $i . '.public_note') || old('characters.' . $i . '.officer_note') || ($character && ($character->pivot->public_note || $character->pivot->officer_note)) ? '' : 'display:none' }};">
                                                <div class="row">
                                                    <!-- Note -->
                                                    <div class="js-note col-lg-6 col-12">
                                                        <div class="form-group mb-0 {{ $errors->has('characters.' . $i . '.public_note') ? 'text-danger font-weight-bold' : '' }}">

                                                            <label for="characters[{{ $i }}][public_note]" class="font-weight-bold">
                                                                @if ($i == 0)
                                                                    <span class="fas fa-fw fa-comment-alt-lines text-muted"></span>
                                                                    {{ __("Custom Note") }}
                                                                @else
                                                                    &nbsp;
                                                                    <span class="sr-only">
                                                                        {{ __("Custom Note") }}
                                                                    </span>
                                                                @endif
                                                            </label>
                                                            <input name="characters[{{ $i }}][public_note]" maxlength="250" data-max-length="250" type="text" placeholder="{{ __('brief public note') }}"
                                                                class="form-control dark {{ $errors->has('characters.' . $i . '.public_note') ? 'form-danger' : '' }}" autocomplete="off"
                                                                value="{{ old('characters.' . $i . '.public_note') ? old('characters.' . $i . '.public_note') : (!old('characters.' . $i . '.public_note') && $character ? $character->pivot->public_note : '') }}">
                                                        </div>
                                                    </div>

                                                    <!-- Officer Note -->
                                                    <div class="js-note col-lg-6 col-12">
                                                        <div class="form-group mb-0 {{ $errors->has('characters.' . $i . '.officer_note') ? 'text-danger font-weight-bold' : '' }}">

                                                            <label for="characters[{{ $i }}][officer_note]" class="font-weight-bold">
                                                                @if ($i == 0)
                                                                    <span class="fas fa-fw fa-shield text-muted"></span>
                                                                    {{ __("Officer Note") }}
                                                                @else
                                                                    &nbsp;
                                                                    <span class="sr-only">
                                                                        {{ __("Optional Officer Note") }}
                                                                    </span>
                                                                @endif
                                                            </label>
                                                            @if (isStreamerMode())
                                                                <div class="mt-2">
                                                                    {{ __("Officer note is hidden in streamer mode") }}
                                                                </div>
                                                            @endif
                                                            <input name="characters[{{ $i }}][officer_note]" maxlength="250" data-max-length="250" type="text" placeholder="{{ __('officer note') }}"
                                                                class="form-control dark {{ $errors->has('characters.' . $i . '.officer_note') ? 'form-danger' : '' }}" autocomplete="off"
                                                                style="{{ isStreamerMode() ? 'display:none;' : '' }}"
                                                                value="{{ old('characters.' . $i . '.officer_note') ? old('characters.' . $i . '.officer_note') : (!old('characters.' . $i . '.officer_note') && $character ? $character->pivot->officer_note : '') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($i == $maxCharacters - 1)
                                                <div class="col-12 mt-3 text-danger font-weight-bold">
                                                    {{ __("Max characters added") }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </fieldset>
                    </div>
                </div>

                <div class="form-group">
                    <button id="submit" class="btn btn-success"><span class="fas fa-fw fa-save"></span> {{ $raid && !$copy ? __('Update') : __('Create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var characters = {!! $showOfficerNote ? $guild->characters->makeVisible('officer_note')->toJson() : $guild->characters->toJson() !!};
    var date  = '{{ $date }}';
    var guild = {!! $guild->toJson() !!};
</script>
<script src="{{ loadScript('warcraftlogs.js') }}"></script>
<script src="{{ loadScript('raidEdit.js') }}"></script>
@endsection
