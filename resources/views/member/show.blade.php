@extends('layouts.app')
@section('title', $member->username . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row mb-3">
                <div class="col-12 pt-2 bg-lightest rounded">
                    @include('member/partials/header', ['discordUsername' => $user->discord_username, 'headerSize' => 1, 'showEdit' => true, 'titlePrefix' => null])
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
                        @foreach ($characters as $character)
                            <li class="pt-2 pl-3 pb-3 pr-3 rounded">
                                @include('character/partials/header', ['character' => $character, 'showEdit' => true, 'showEditLoot' => true, 'showIcon' => false, 'showOwner' => false])
                            </li>
                        @endforeach
                        <li class="pt-3 pl-3 pb-3 pr-3 rounded">
                            <a href="{{ route('character.create', ['guildSlug' => $guild->slug]) }}" class="font-weight-medium">
                                <span class="fas fa-plus"></span>
                                create character
                            </a>
                        </li>
                    </ol>
                </div>
            </div>

             <div class="row mb-3 pt-3 bg-light rounded">
                <div class="col-12 mb-2">
                    <span class="text-gold font-weight-bold">
                        <span class="fas fa-fw fa-book"></span>
                        Recipes
                    </span>
                </div>
                <div class="col-12">
                    @if ($recipes->count() > 0)
                        <ol class="">
                            @foreach ($recipes as $item)
                                @php
                                    $itemCharacter = $characters->find($item->pivot->character_id);
                                @endphp
                                <li class="">
                                    @include('partials/item', ['wowheadLink' => true])
                                    <small class="text-muted">
                                        on
                                        <a href="{{route('character.show', ['guildSlug' => $guild->slug, 'name' => $character->name]) }}"
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

            <div class="row pt-3 mb-3 bg-light rounded">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        <span class="fas fa-fw fa-comment-alt-lines"></span>
                        Public Note
                    </span>
                </div>
                <div class="col-12 mb-3">
                    {{ $member->public_note ? $member->public_note : '—' }}
                </div>

                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        <span class="fas fa-fw fa-shield"></span>
                        Officer Note
                    </span>
                </div>
                <div class="col-12 mb-3">
                    {{ $member->officer_note ? $member->officer_note : '—' }}
                </div>

                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        <span class="fas fa-fw fa-lock"></span>
                        Personal Note
                    </span>
                </div>
                <div class="col-12 mb-3">
                    {{ $member->personal_note ? $member->personal_note : '—' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
