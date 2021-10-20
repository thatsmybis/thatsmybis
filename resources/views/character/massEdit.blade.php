@extends('layouts.app')
@section('title', __("Create Characters") . " - " . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row mb-3">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium text-{{ getExpansionColor($guild->expansion_id) }}">
                        <span class="text-muted fas fa-fw fa-user"></span>
                        {{ __("Create Characters") }}
                    </h1>
                    <p>
                        {{ __("Maximum :count characters at once. You can paste the same log between multiple submissions.", ['count' => $maxCharacters]) }}
                    </p>
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
        </div>
    </div>

    <div class="row mb-3 pt-3 pb-3 bg-light rounded">
        <div class="col-md-6 col-12">
            <label class="text-muted">
                <span class="fas fa-fw fa-paste"></span>
                <span class="small">{{ __("optional") }}</span> {!! __("paste a Warcraft Logs report <strong>OR</strong> a list of characters") !!}
                <br>
                <span class="small">{!! __("list can be separated by spaces or commas or whatever you want; but names should match ingame names EXACTLY") !!}</span>
            </label>
            <div class="form-group">
                <input name="logs"
                    autocomplete="off"
                    maxlength="2000"
                    type="text"
                    class="form-control dark"
                    placeholder="{{ __('eg. https://classic.warcraftlogs.com/reports/AbCdE3FgHiJkLmNo') }}"
                    style="" />
            </div>
            @if ($guild->warcraftlogs_token)
                <span id="addWarcraftlogsCharacters" class="btn btn-success">
                    <span class="fas fa-fw fa-file-import"></span>
                    {{ __("Load Characters") }}
                </span>
                <div class="js-warcraftlogs-attendees-message text-warning mt-2" style="display:none;">
                </div>
                @include('partials/loadingBars', ['hide' => true, 'loadingBarId' => 'warcraftlogsLoadingbar'])
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
        </div>
    </div>

    <form id="editForm" class="form-horizontal" role="form" method="POST" action="{{ route('character.submitCreateMany', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
        {{ csrf_field() }}

        @for ($i = 0; $i < $maxCharacters; $i++)
            @php
                // If this page makes it into an EDIT mode, put character here ok? Ok. Glad we cleared that up.
                $character = null;

                // Support for custom specs
                $found = false;
                $oldSpec = old('spec') ? old('spec') : ($character ? $character->spec : null);
            @endphp
            <div class="js-character" style="{{ !$character && $i > 0 ? 'display:none;' : '' }}">
                <div class="row">
                    <div class="col-12 font-weight-bold text-warning">
                        <span class="fas fa-fw fa-user text-muted"></span>
                        {{ __("Character") }} {{ $i + 1 }}
                    </div>
                </div>

                <input hidden name="characters[{{ $i }}][id]" value="{{ $character ? $character->id : '' }}" />

                <div class="row mt-3 mb-3 pt-3 bg-light rounded">
                    <div class="col-md-3 col-sm-6 col-12">
                        <!-- Name -->
                        <div class="form-group">
                            @include('character/partials/nameInput', [
                                'character' => $character,
                                'oldValue'  => (old('character.' . $i . '.name') ? old('character.' . $i . '.name') : null),
                                'hideLabel' => __("Name"),
                                'name'      => "characters[{$i}][name]",
                            ])
                        </div>

                        <!-- Class -->
                        <div class="form-group">
                            @include('character/partials/classInput', [
                                'character' => $character,
                                'oldValue'  => (old('character.' . $i . '.class') ? old('character.' . $i . '.class') : null),
                                'hideLabel' => __("Class"),
                                'name'      => "characters[{$i}][class]",
                                'index'     => $i,
                            ])
                        </div>

                        <!-- Spec -->
                        <div class="form-group">
                            @include('character/partials/specInput', [
                                'character' => $character,
                                'hideLabel' => __("Spec"),
                                'name'      => "characters[{$i}][spec]",
                                'index'     => $i,
                            ])
                        </div>

                        <!-- Member -->
                        <div class="form-group">
                            @include('character/partials/memberInput', [
                                'character' => $character,
                                'oldValue'  => (old('character.' . $i . '.member_id') ? old('character.' . $i . '.member_id') : null),
                                'hideLabel' => __("Guild Member"),
                                'name'      => "characters[{$i}][member_id]",
                            ])
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-12">
                        <!-- Is Alt -->
                        <div class="form-group">
                            @include('character/partials/altInput', [
                                'character' => $character,
                                'dummySpacing' => ($i === 0 ? true : false),
                                'oldValue'  => (old('character.' . $i . '.is_alt') ? old('character.' . $i . '.is_alt') : null),
                                'hideLabel' => __("Alt Character"),
                                'name'      => "characters[{$i}][is_alt]",
                            ])
                        </div>

                        <!-- Race -->
                        <div class="form-group">
                            @include('character/partials/raceInput', [
                                'character' => $character,
                                'oldValue'  => (old('character.' . $i . '.race') ? old('character.' . $i . '.race') : null),
                                'hideLabel' => __("Race"),
                                'name'      => "characters[{$i}][race]",
                            ])
                        </div>

                        <!-- Archetype -->
                        <div class="form-group">
                            @include('character/partials/archetypeInput', [
                                'character' => $character,
                                'oldValue'  => (old('character.' . $i . '.archetype') ? old('character.' . $i . '.archetype') : null),
                                'hideLabel' => __("Role"),
                                'name'      => "characters[{$i}][archetype]",
                                'index'     => $i,
                            ])
                        </div>

                        <!-- Spec Label -->
                        <div class="form-group">
                            @include('character/partials/specLabelInput', [
                                'character' => $character,
                                'oldValue'  => (old('character.' . $i . '.spec_label') ? old('character.' . $i . '.spec_label') : null),
                                'hideLabel' => __("Spec Label"),
                                'name'      => "characters[{$i}][spec_label]",
                                'index'     => $i,
                            ])
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-12">
                        <!-- Main Raid Group -->
                        <div class="form-group">
                            @include('character/partials/mainRaidGroupInput', [
                                'character' => $character,
                                'oldValue'  => (old('character.' . $i . '.raid_group_id') ? old('character.' . $i . '.raid_group_id') : null),
                                'hideLabel' => __("Main Raid Group"),
                                'name'      => "characters[{$i}][raid_group_id]",
                            ])
                        </div>

                        <!-- General Raid Group -->
                        <div class="form-group">
                            @include('character/partials/generalRaidGroupInput', [
                                'character' => $character,
                                'oldPrefix' => 'character.' . $i . '.',
                                'hideLabel' => __("General Raid Groups"),
                                'name'      => "characters[{$i}][raid_groups]",
                            ])
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-12">
                        <!-- Profession 1 -->
                        <div class="form-group">
                            @include('character/partials/profession1Input', [
                                'character' => $character,
                                'oldValue'  => (old('character.' . $i . '.profession_1') ? old('character.' . $i . '.profession_1') : null),
                                'hideLabel' => __("Profession 1"),
                                'name'      => "characters[{$i}][profession_1]",
                            ])
                        </div>

                        <!-- Profession 2 -->
                        <div class="form-group">
                            @include('character/partials/profession2Input', [
                                'character' => $character,
                                'oldValue'  => (old('character.' . $i . '.profession_2') ? old('character.' . $i . '.profession_2') : null),
                                'hideLabel' => __("Profession 2"),
                                'name'      => "characters[{$i}][profession_2]",
                            ])
                        </div>

                        <!-- Level -->
                        <div class="form-group">
                            @include('character/partials/levelInput', [
                                'character' => $character,
                                'oldValue'  => (old('character.' . $i . '.level') ? old('character.' . $i . '.level') : null),
                                'hideLabel' => __("Level"),
                                'name'      => "characters[{$i}][level]",
                            ])
                        </div>

                        @if ($character)
                            <!-- Archive -->
                            <div class="form-group">
                                @include('character/partials/archiveInput', [
                                    'character' => $character,
                                    'oldValue'  => (old('character.' . $i . '.inactive_at') ? old('character.' . $i . '.inactive_at') : null),
                                    'hideLabel' => __("Archive"),
                                    'name'      => "characters[{$i}][inactive_at]",
                                ])
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6 col-12">
                        @if ($guild->expansion_id === 1)
                            <!-- Rank -->
                            <div class="form-group">
                                @include('character/partials/rankInput', [
                                    'character' => $character,
                                    'oldValue'  => (old('character.' . $i . '.rank') ? old('character.' . $i . '.rank') : null),
                                    'hideLabel' => __("PvP Rank"),
                                    'name'      => "characters[{$i}][rank]",
                                ])
                            </div>
                        @endif

                        <!-- Public Note -->
                        <div class="form-group">
                            @include('character/partials/publicNoteInput', [
                                'character' => $character,
                                'oldValue'  => (old('character.' . $i . '.public_note') ? old('character.' . $i . '.public_note') : null),
                                'hideLabel' => __("Public Note"),
                                'name'      => "characters[{$i}][public_note]",
                            ])
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        @if ($guild->expansion_id === 1)
                            <!-- Rank Goal -->
                            <div class="form-group">
                                @include('character/partials/rankGoalInput', [
                                    'character' => $character,
                                    'oldValue'  => (old('character.' . $i . '.rank_goal') ? old('character.' . $i . '.rank_goal') : null),
                                    'hideLabel' => __("PvP Rank Goal"),
                                    'name'      => "characters[{$i}][rank_goal]",
                                ])
                            </div>
                        @endif

                        <!-- Officer Note -->
                        <div class="form-group">
                            @include('character/partials/officerNoteInput', [
                                'character' => $character,
                                'oldValue'  => (old('character.' . $i . '.officer_note') ? old('character.' . $i . '.officer_note') : null),
                                'hideLabel' => __("Officer Note"),
                                'name'      => "characters[{$i}][officer_note]",
                            ])
                        </div>
                    </div>
                </div>
            </div>
        @endfor

        <div class="form-group">
            <button id="submit" class="btn btn-success"><span class="fas fa-fw fa-save"></span> {{ __('Create') }}</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    var characters = {!! $guild->characters->toJson() !!};
    var guild = {!! $guild->toJson() !!};
    $(document).ready(function() {
        warnBeforeLeaving("#editForm")
    });
</script>
<script src="{{ loadScript('warcraftlogs.js') }}"></script>
<script src="{{ loadScript('characterEdit.js') }}"></script>
@endsection
