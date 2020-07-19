@extends('layouts.app')
@section('title', $member->username . " - " . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-12">
            <div class="row mb-4">
                <div class="col-12">
                    @include('member/partials/header', ['discordUsername' => $user->discord_username, 'headerSize' => 1, 'showEdit' => true, 'titlePrefix' => null])
                    <hr class="light">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        Characters
                    </span>
                </div>
                <div class="col-12">
                    @if ($characters->count() > 0)
                        <ol class="striped no-bullet lesser-indent">
                            @foreach ($characters as $character)
                                <li class="pt-2 pl-3 pb-3 pr-3 rounded">
                                    @include('character/partials/header', ['character' => $character, 'showEdit' => true, 'showOwner' => false])
                                </li>
                            @endforeach
                        </ol>
                    @else
                        —
                    @endif
                </div>
            </div>

             <div class="row mb-3">
                <div class="col-12">
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
                                    <small >
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

            <div class="row mb-3">
                <div class="col-12">
                    <hr class="light">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        <span class="fas fa-fw fa-comment-alt-lines"></span>
                        Public Note
                    </span>
                </div>
                <div class="col-12">
                    {{ $member->public_note ? $member->public_note : '—' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        <span class="fas fa-fw fa-shield"></span>
                        Officer Note
                    </span>
                </div>
                <div class="col-12">
                    {{ $member->officer_note ? $member->officer_note : '—' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        <span class="fas fa-fw fa-lock"></span>
                        Personal Note
                    </span>
                </div>
                <div class="col-12">
                    {{ $member->personal_note ? $member->personal_note : '—' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
