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
                            <span class="text-muted fas fa-fw fa-user-plus"></span>
                            {{ __("Create a Character") }}
                        </h1>
                    </div>
                @endif
                @if (!$character && $hasEditCharactersPermission)
                    <div class="col-12 pt-2 mb-2">
                        <p class="font-weight-medium">
                            <span class="fas fa-fw fa-exclamation-triangle text-warning"></span>
                            {!! __("You can create many characters at once <a href=':link'>here</a>", ['link' => route('character.showCreateMany', ['guildId' => $guild->id, 'guildSlug' => $guild->slug])]) !!}
                        </p>
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
                                    @include('character/partials/nameInput', ['oldValue' => old('name')])
                                </div>
                            </div>

                            @if ($currentMember->hasPermission('edit.characters'))
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        @include('character/partials/memberInput', ['oldValue' => old('member_id')])
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    @include('character/partials/classInput', ['oldValue' => old('class'), 'index' => 1])
                                </div>
                            </div>

                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    @include('character/partials/archetypeInput', ['oldValue' => old('archetype'), 'index' => 1])
                                </div>
                            </div>

                            @php
                                // Support for custom specs
                                $found = false;
                                $oldSpec = old('spec') ? old('spec') : ($character ? $character->spec : null);
                            @endphp

                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    @include('character/partials/specInput', ['index' => 1])
                                </div>
                            </div>

                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    @include('character/partials/specLabelInput', ['oldValue' => old('spec_label'), 'index' => 1])
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    @include('character/partials/raceInput', ['oldValue' => old('race')])
                                </div>
                            </div>

                            <div class="col-sm-3 col-6">
                                <div class="form-group">
                                    @include('character/partials/levelInput', ['oldValue' => old('level')])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4 pb-1 pt-2 bg-light rounded">
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            @include('character/partials/mainRaidGroupInput', ['oldValue' => old('raid_group_id')])
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            @include('character/partials/generalRaidGroupInput', ['oldPrefix' => ''])
                        </div>
                    </div>
                </div>

                <div class="row mb-4 pb-1 pt-2 bg-light rounded">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    @include('character/partials/profession1Input', ['oldValue' => old('profession_1')])
                                </div>
                            </div>
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    @include('character/partials/profession2Input', ['oldValue' => old('profession_2')])
                                </div>
                            </div>
                        </div>

                        @if ($guild->expansion_id === 1)
                            <div class="row mt-4">
                                <div class="col-sm-3 col-6">
                                    <div class="form-group">
                                        @include('character/partials/pvpRankInput', ['oldValue' => old('rank')])
                                    </div>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <div class="form-group">
                                        @include('character/partials/pvpRankGoalInput', ['oldValue' => old('rank_goal')])
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row mb-4 pb-1 pt-2 bg-light rounded">
                    <div class="col-12">
                        <div class="form-group">
                            @include('character/partials/publicNoteInput', ['oldValue' => old('public_note')])
                        </div>
                    </div>

                    @if ($currentMember->hasPermission('edit.officer-notes'))
                        <div class="col-12 mt-4">
                            <div class="form-group">
                                @include('character/partials/officerNoteInput', ['oldValue' => old('officer_note')])
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
                                <span class="fas fa-fw fa-scroll-old text-muted"></span><span class="fas fa-fw fa-lock-open text-muted"></span>
                                <a href="{{ route('member.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $character->member->id, 'usernameSlug' => $character->member->slug]) }}">
                                    {{ __("Unlock Wishlist / Received Loot") }}
                                </a>
                            </div>
                        </div>
                    @endif
                    @if ($character && ($currentMember->hasPermission('inactive.characters') || $currentMember->id == $character->member_id))
                        <div class="col-6">
                            <div class="form-group mb-0">
                                @include('character/partials/archiveInput', ['oldValue' => old('inactive_at')])
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                @include('character/partials/altInput', ['oldValue' => old('is_alt')])
                            </div>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="form-group mb-0">
                                @include('character/partials/altInput', ['oldValue' => old('is_alt')])

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
