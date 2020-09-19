@extends('layouts.app')
@section('title', $item->name . ' - ' . config('app.name'))


@section('content')
<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-12">
            <ul class="list-inline">
                <li class="list-inline-item bg-lightest rounded pl-3 pb-3 pr-3 mt-3 item-tooltip align-top">
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <h1 class="font-weight-bold">
                                @if ($itemJson)
                                    {{-- %69 (code for 'i') is a workaround that masks the link so wowhead's script won't parse it, allowing *us* to style it however we want --}}
                                    <a class="q{!! $itemJson->quality !!}" href="https://classic.wowhead.com/%69tem={{ $item->item_id}}" target="_blank">
                                        <span class="iconlarge">
                                            <ins style='background-image: url("https://wow.zamimg.com/images/wow/icons/large/{!! $itemJson->icon !!}.jpg");'></ins><del></del></span>{!! $itemJson->name !!}
                                    </a>
                                @else
                                    @include('partials/item', ['wowheadLink' => true])
                                @endif
                            </h1>
                        </li>
                        <li class="list-inline-item">
                            <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $item->item_id]) }}">
                                <span class="fas fa-fw fa-clipboard-list-check"></span>logs
                            </a>
                        </li>
                    </ul>
                    @if ($itemJson)
                        {!! $itemJson->tooltip !!}
                    @endif
                </li>
                @if ($guild)
                    <li class="list-inline-item bg-lightest rounded p-3 mt-3 align-top item-notes">
                        <form role="form" method="POST" action="{{ route('guild.item.updateNote', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            {{ csrf_field() }}

                            <input hidden name="id" value="{{ $item->item_id }}" />

                            <div class="row mb-3 pt-3">

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
                                        Guild Note
                                    </span>
                                </div>
                                <div class="col-12 mb-3 pl-4">
                                    {{ $notes['note'] ? $notes['note'] : '—' }}
                                    @if ($showNoteEdit)
                                        <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                                    @endif
                                </div>
                                @if ($showNoteEdit)
                                    <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                                        <div class="form-group">
                                            <label for="note" class="sr-only">
                                                Item Priority
                                            </label>
                                            <textarea data-max-length="144" name="note" rows="2" placeholder="add a note" class="form-control dark">{{ old('note') ? old('note') : ($item ? $notes['note'] : '') }}</textarea>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <span class="text-muted font-weight-bold">
                                        <span class="fas fa-fw fa-sort-amount-down"></span>
                                        Guild Prio Note
                                    </span>
                                </div>
                                <div class="col-12 mb-3 pl-4">
                                    {{ $notes['priority'] ? $notes['priority'] : '—' }}
                                    @if ($showNoteEdit)
                                        <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                                    @endif
                                </div>
                                @if ($showNoteEdit)
                                    <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                                        <div class="form-group">
                                            <label for="priority" class="sr-only">
                                                Item Priority
                                            </label>
                                            <textarea data-max-length="144" name="priority" rows="2" placeholder="eg. mage > warlock > boomkin > arcane shot hunter" class="form-control dark">{{ old('priority') ? old('priority') : ($item ? $notes['priority'] : '') }}</textarea>
                                        </div>
                                    </div>
                                @endif

                                <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Save</button>
                                </div>
                            </div>
                        </form>
                    </li>
                    @if ($showPrios)
                        <li class="list-inline-item bg-lightest rounded p-3 mt-3 align-top">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <h2 class="font-weight-bold mb-3">
                                        Character Prios
                                    </h2>
                                </li>
                                <li class="list-inline-item">
                                    @if ($showPrioEdit)
                                        <div class="dropdown">
                                            <span class="dropdown-toggle text-link" role="button" id="editPrioLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="fas fa-fw fa-pencil"></span>
                                                edit
                                            </span>
                                            <div class="dropdown-menu" aria-labelledby="editPrioLink">
                                                @foreach ($raids as $raid)
                                                    <a class="dropdown-item" href="{{ route('guild.item.prios', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $item->item_id, 'raidId' => $raid->id]) }}">
                                                        {{ $raid->name }}
                                                    </a>
                                                @endforeach
                                                <a class="dropdown-item" href="{{ route('guild.raid.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                                    <span class="fas fa-fw fa-plus"></span> Create New Raid
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            </ul>
                            @if ($priodCharacters && $priodCharacters->count() > 0)
                                @php
                                    $lastRaid = '';
                                @endphp
                                <ul class="list-inline">
                                    @foreach ($priodCharacters as $character)
                                        @if ($character->pivot->raid_id != $lastRaid)
                                            @if (!$loop->first)
                                                    </ol>
                                                </li>
                                            @endif
                                            @php
                                                $lastRaid = $character->pivot->raid_id;
                                            @endphp
                                            <li class="list-inline-item align-top">
                                                <ol class="lesser-indent">
                                                    <li data-raid-id="{{ $character->pivot->raid_id }}" class="no-bullet font-weight-bold mt-2">
                                                        {{ $raids->where('id', $character->pivot->raid_id)->first()->name }}
                                                    </li>
                                        @endif
                                            <li data-raid-id="{{ $character->pivot->raid_id }}" class="js-item-wishlist-character font-weight-normal mb-1" value="{{ $character->pivot->order }}">
                                                <a href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}"
                                                    title="{{ $character->raid_name ? $character->raid_name . ' -' : '' }} {{ $character->level ? $character->level : '' }} {{ $character->race ? $character->race : '' }} {{ $character->spec ? $character->spec : '' }} {{ $character->class ? $character->class : '' }} {{ $character->username ? '(' . $character->username . ')' : '' }}"
                                                    class="text-{{ $character->class ? strtolower($character->class) : ''}}-important tag d-inline">
                                                    <span class="role-circle" style="background-color:{{ getHexColorFromDec(($character->raid_color ? $character->raid_color : '')) }}"></span>{{ $character->name }}
                                                    @if ($character->is_alt)
                                                        <span class="text-legendary">alt</span>
                                                    @endif
                                                    <span class="js-watchable-timestamp smaller text-muted"
                                                        data-timestamp="{{ $character->pivot->created_at }}"
                                                        data-is-short="1">
                                                    </span>
                                                </a>
                                            </li>
                                        @if ($loop->last)
                                                </ol>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @else
                                <div class="lead ml-4 mt-3">
                                    <em>None</em>
                                </div>
                            @endif
                        </li>
                    @endif
                @endif
            </ul>
        </div>
    </div>

    {{--
    <div class="row pt-2 mb-3 bg-lightest rounded">
        <div class="col-12">
            <h2 class="font-weight-bold pl-2">Prio'd</h2>
        </div>
        <div class="col-12 pr-0 pl-0">
            @if ($priodCharacters && $priodCharacters->count() > 0)
                @include('partials/characterDatatable', ['characters' => $priodCharacters])
            @else
                <ul>
                    <li class="lead no-bullet">
                        <em>Nobody has been prio'd for this item yet</em>
                    </li>
                </ul>
            @endif
        </div>
    </div>
    --}}

    @if ($showWishlist)
        <div class="row pt-2 mb-3 bg-lightest rounded">
            <div class="col-12">
                <h2 class="font-weight-bold pl-2">Wishlisted</h2>
            </div>
            <div class="col-12 pr-0 pl-0">
                @if ($wishlistCharacters && $wishlistCharacters->count() > 0)
                    @include('partials/characterDatatable', ['characters' => $wishlistCharacters])
                @else
                    <ul>
                        <li class="lead no-bullet">
                            <em>Nobody has this item in their wishlist yet</em>
                        </li>
                    </ul>
                @endif
            </div>
        </div>
    @endif

    <div class="row mr-1 ml-1 mb-3 pt-1  bg-lightest rounded">
        <div class="col-12">
            <h2>Have It</h2>
            @if ($receivedAndRecipeCharacters->count() > 0)
                <ul class="list-inline striped">
                    @foreach ($receivedAndRecipeCharacters as $character)
                        <li class="list-inline-item rounded pt-2 pl-3 pb-3 pr-3">
                            @include('character/partials/header', ['showDetails' => false, 'showEdit' => false, 'showOwner' => false])
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="lead mb-3">
                    <em>Nobody has this item in their character sheet yet</em>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var characters      = {!! $showWishlist ? ($showOfficerNote ? $wishlistCharacters->makeVisible('officer_note')->toJson() : $wishlistCharacters->toJson()) : null !!};
    var guild           = {!! $guild->toJson() !!};
    var raids           = {!! $raids->toJson() !!};
    var showEdit        = {{ $showEdit ? 'true' : 'false' }};
    var showOfficerNote = {{ $showOfficerNote ? 'true' : 'false' }};
    var showPrios       = {{ $showPrios ? 'true' : 'false' }};
    var showWishlist    = {{ $showWishlist ? 'true' : 'false' }};
</script>
<script src="{{ env('APP_ENV') == 'local' ? asset('/js/roster.js') : mix('js/processed/roster.js') }}"></script>
@endsection

