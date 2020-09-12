@extends('layouts.app')
@section('title', $member->username . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row mb-3">
                <div class="col-12 pt-2 bg-lightest rounded">
                    @include('member/partials/header', ['discordUsername' => $member->user->discord_username, 'headerSize' => 1, 'showEdit' => $showEdit, 'titlePrefix' => null])
                </div>
            </div>

            <div class="row mb-3 pt-3 bg-light rounded">
                <div class="col-12 mb-2">
                    <span class="font-weight-bold">
                        <span class="fas fa-fw fa-user text-muted"></span>
                        Characters
                    </span>
                </div>
                <div class="col-12">
                    <ol class="striped no-bullet no-indent">
                        @if ($showEdit)
                            <li class="pt-3 pl-3 pb-3 pr-3 rounded">
                                <a href="{{ route('character.showCreate', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" class="font-weight-medium">
                                    <span class="fas fa-plus"></span>
                                    create character
                                </a>
                            </li>
                        @endif
                        @foreach ($characters as $character)
                            <li class="pt-2 pl-3 pb-3 pr-3 rounded">
                                @include('character/partials/header', ['character' => $character, 'showEdit' => $showEdit, 'showEditLoot' => $showEditLoot, 'showIcon' => false, 'showOwner' => false])
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>

             <div class="row mb-3 pb-3 pt-3 bg-light rounded">
                <div class="col-12 mb-2">
                    <span class="text-gold font-weight-bold">
                        <span class="fas fa-fw fa-book"></span>
                        Recipes
                    </span>
                </div>
                <div class="col-12">
                    @if ($recipes->count() > 0)
                        <ol class="mb-0">
                            @foreach ($recipes->sortBy('name') as $item)
                                @php
                                    $itemCharacter = $characters->find($item->pivot->character_id);
                                @endphp
                                <li class="">
                                    @include('partials/item', ['wowheadLink' => false])
                                    <small class="text-muted">
                                        on
                                        <a href="{{route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $itemCharacter->id, 'nameSlug' => $itemCharacter->slug]) }}"
                                            class="text-{{ $itemCharacter->class ? strtolower($itemCharacter->class) : '' }}">
                                            {{ $itemCharacter->name }}
                                        </a>
                                    </small>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        —
                    @endif
                </div>
            </div>

            <form role="form" method="POST" action="{{ route('member.updateNote', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="id" value="{{ $member->id }}" />
                <div class="row mb-3 pt-3 bg-light rounded">

                    @if (count($errors) > 0)
                        <div class="col-12">
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="col-12">
                        <span class="text-muted font-weight-bold">
                            <span class="fas fa-fw fa-comment-alt-lines"></span>
                            Public Note
                        </span>
                    </div>
                    <div class="col-12 mb-3 pl-4">
                        {{ $member->public_note ? $member->public_note : '—' }}
                        @if ($currentMember->id == $member->id || $currentMember->hasPermission('edit.officer-notes'))
                            <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                        @endif
                    </div>
                    @if ($currentMember->id == $member->id || $currentMember->hasPermission('edit.officer-notes'))
                        <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                            <div class="form-group">
                                <label for="public_note" class="font-weight-bold">
                                    <span class="sr-only">Public Note</span>
                                    <small class="text-muted">anyone in the guild can see this</small>
                                </label>
                                <textarea data-max-length="144" name="public_note" rows="2" placeholder="anyone in the guild can see this" class="form-control dark">{{ old('public_note') ? old('public_note') : ($member ? $member->public_note : '') }}</textarea>
                            </div>
                        </div>
                    @endif

                    @if ($currentMember->hasPermission('view.officer-notes'))
                        <div class="col-12">
                            <span class="text-muted font-weight-bold">
                                <span class="fas fa-fw fa-shield"></span>
                                Officer Note
                            </span>
                        </div>
                        <div class="col-12 mb-3 pl-4">
                            @if (!isStreamerMode())
                                {{ $member->officer_note ? $member->officer_note : '—' }}
                                @if ($currentMember->hasPermission('edit.officer-notes'))
                                    <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                                @endif
                            @else
                                Hidden in streamer mode
                            @endif
                        </div>
                        @if ($currentMember->hasPermission('edit.officer-notes') && !isStreamerMode())
                            <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                                <div class="form-group">
                                    <label for="officer_note" class="font-weight-bold">
                                        <span class="sr-only">Officer Note</span>
                                        <small class="text-muted">only officers can see this</small>
                                    </label>
                                    <textarea data-max-length="144" name="officer_note" rows="2" placeholder="only officers can see this" class="form-control dark">{{ old('officer_note') ? old('officer_note') : ($member ? $member->officer_note : '') }}</textarea>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{--
                        @if ($showPersonalNote)
                            <div class="col-12">
                                <span class="text-muted font-weight-bold">
                                    <span class="fas fa-fw fa-eye-slash"></span>
                                    Personal Note
                                </span>
                            </div>
                            <div class="col-12 mb-3 pl-4">
                                {{ $member->personal_note ? $member->personal_note : '—' }}
                                <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                            </div>
                            <div class="js-note-input col-12 pl-4" style="display:none;">
                                <div class="form-group">
                                    <label for="personal_note" class="font-weight-bold">
                                        <span class="sr-only">Personal Note</span>
                                        <small class="text-muted">only you can see this</small>
                                    </label>
                                    <textarea data-max-length="2000" name="personal_note" rows="2" placeholder="only you can see this" class="form-control dark">{{ old('personal_note') ? old('personal_note') : ($member ? $member->personal_note : '') }}</textarea>
                                </div>
                            </div>
                        @endif
                    --}}

                    <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                        <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
