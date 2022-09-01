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

                                    @php
                                        $wowheadSubdomain = 'www';
                                        if ($guild->expansion_id === 1) {
                                            $wowheadSubdomain = 'classic';
                                        } elseif ($guild->expansion_id === 2) {
                                            $wowheadSubdomain = 'tbc';
                                        } elseif ($guild->expansion_id === 3) {
                                            $wowheadSubdomain = 'wotlk';
                                        }

                                        $wowheadLocale = App::getLocale();
                                        if ($wowheadLocale === "en") {
                                            $wowheadLocale = '';
                                        } else {
                                            $wowheadLocale .= '.';
                                        }

                                        $wowheadUrl = '';

                                        if ($guild->expansion_id === 3) {
                                            $wowheadUrl = 'https://' . $wowheadLocale . 'wowhead.com/' . $wowheadSubdomain . '/%69tem=' . $item->item_id;
                                        } else {
                                            // %69 (code for 'i') is a workaround that masks the link so wowhead's script won't parse it, allowing *us* to style it however we want
                                            $wowheadUrl = 'https://' . $wowheadLocale . $wowheadSubdomain . '.wowhead.com/%69tem=' . $item->item_id;
                                        }
                                    @endphp

                                    <a class="q{!! $itemJson->quality !!}" href="{{ $wowheadUrl }}" target="_blank">
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
                                <span class="fas fa-fw fa-clipboard-list-check"></span>{{ __("history") }}
                            </a>
                        </li>
                    </ul>
                    @if ($itemJson)
                        {!! $itemJson->tooltip !!}
                    @endif

                    @if ($item->itemSources->count())
                        <ul class="list-inline mt-3">
                            <li class="list-inline-item">
                                Source(s)
                            </li>
                            @foreach ($item->itemSources as $itemSource)
                                <li class="list-inline-item">
                                    <a class="text-muted" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => $itemSource->instance->slug]) }}">
                                        {{ $itemSource->name}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
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

                                <div class="col-12" style="{{ $guild->tier_mode ? '' : 'display:none;' }}">
                                    <span class="text-muted font-weight-bold">
                                        <span class="fas fa-fw fa-trophy"></span>
                                        {{ __("Guild Tier") }}
                                    </span>
                                </div>
                                <div class="col-12 mb-3 pl-4 font-weight-bold text-tier-{{ $notes['tier'] ? $notes['tier'] : '' }}" style="{{ $guild->tier_mode ? '' : 'display:none;' }}">
                                    {{ $notes['tier'] ? $guild->tier_mode == App\Guild::TIER_MODE_NUM ? $notes['tier'] : App\Guild::tiers()[$notes['tier']] : '—' }}
                                    @if ($showNoteEdit)
                                        <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                                    @endif
                                </div>
                                @if ($showNoteEdit)
                                    <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                                        <div class="form-group">
                                            <label for="tier" class="sr-only">
                                                {{ __("Item Tier") }}
                                            </label>
                                            <select name="tier" class="form-control dark">
                                                <option value="" selected>
                                                —
                                                </option>
                                                @foreach ($guild->tiers() as $key => $tier)
                                                    <option value="{{ $key }}"
                                                        class="text-tier-{{ $key }}"
                                                        {{ old('tier') && old('tier') == $key ? 'selected' : ($notes['tier'] && $notes['tier'] == $key ? 'selected' : '') }}>
                                                        {{ $guild->tier_mode == App\Guild::TIER_MODE_NUM ? $key : $tier }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <span class="text-muted font-weight-bold">
                                        <span class="fas fa-fw fa-comment-alt-lines"></span>
                                        {{ __("Guild Note") }}
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
                                                {{ __("Item Note") }}
                                            </label>
                                            <textarea maxlength="140" data-max-length="140" name="note" rows="2" placeholder="add a note" class="form-control dark">{{ old('note') ? old('note') : ($item ? $notes['note'] : '') }}</textarea>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <span class="text-muted font-weight-bold">
                                        <span class="fas fa-fw fa-sort-amount-down"></span>
                                        {{ __("Guild Prio Note") }}
                                    </span>
                                </div>
                                <div class="col-12 mb-3 pl-4">
                                    {{ $notes['priority'] ? $notes['priority'] : '—' }}
                                    @if ($showPrioEdit)
                                        <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                                    @endif
                                </div>
                                @if ($showPrioEdit)
                                    <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                                        <div class="form-group">
                                            <label for="priority" class="sr-only">
                                                {{ __("Item Priority") }}
                                            </label>
                                            <textarea maxlength="140" data-max-length="140" name="priority" rows="2" placeholder="eg. mage > warlock > boomkin > arcane shot hunter" class="form-control dark">{{ old('priority') ? old('priority') : ($item ? $notes['priority'] : '') }}</textarea>
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
                            @if ($item->itemSources->count())
                                <ul class="list-inline">
                                    <li class="list-inline-item">
                                        <h2 class="font-weight-bold mb-3">
                                            <span class="fas fa-fw fa-sort-amount-down text-gold"></span>
                                            {{ __("Character Prios") }}
                                        </h2>
                                    </li>

                                    <li class="list-inline-item">
                                        @if ($showPrioEdit)
                                            <div class="dropdown">
                                                <span class="dropdown-toggle text-link" role="button" id="editPrioLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="fas fa-fw fa-pencil"></span>
                                                    {{ __("edit") }}
                                                </span>
                                                <div class="dropdown-menu" aria-labelledby="editPrioLink">
                                                    @foreach ($raidGroups as $raidGroup)
                                                        <a class="dropdown-item" href="{{ route('guild.item.prios', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $item->item_id, 'raidGroupId' => $raidGroup->id]) }}">
                                                            {{ $raidGroup->name }}
                                                        </a>
                                                    @endforeach
                                                    <a class="dropdown-item" href="{{ route('guild.raidGroup.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                                        <span class="fas fa-fw fa-plus"></span> {{ __("Create New Raid Group") }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </li>
                                </ul>
                                @if ($priodCharacters && $priodCharacters->count() > 0)
                                    @php
                                        $lastRaidGroup = '';
                                    @endphp
                                    <ul class="list-inline">
                                        @foreach ($priodCharacters as $character)
                                            @if ($character->pivot->raid_group_id != $lastRaidGroup)
                                                @if (!$loop->first)
                                                        </ol>
                                                    </li>
                                                @endif
                                                @php
                                                    $lastRaidGroup = $character->pivot->raid_group_id;
                                                    $newRaidGroup = $raidGroups->where('id', $character->pivot->raid_group_id)->first();
                                                @endphp
                                                <li class="list-inline-item align-top">
                                                    <ol class="lesser-indent">
                                                        <li data-raid-group-id="{{ $character->pivot->raid_group_id }}" class="no-bullet font-weight-bold mt-2">
                                                            {{ $newRaidGroup ? $newRaidGroup->name : '' }}
                                                        </li>
                                            @endif
                                                <li data-raid-group-id="{{ $character->pivot->raid_group_id }}"
                                                    class="js-item-wishlist-character font-weight-normal mb-1 {{ $character->pivot->is_received || $character->pivot->received_at? 'font-strikethrough' : '' }}"
                                                    value="{{ $character->pivot->order }}">
                                                    <a href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}"
                                                        title="{{ $character->raid_group_name ? $character->raid_group_name . ' -' : '' }} {{ $character->level ? $character->level : '' }} {{ $character->race ? $character->race : '' }} {{ $character->spec ? $character->spec : '' }} {{ $character->class ? $character->class : '' }} {{ $character->username ? '(' . $character->username . ')' : '' }}"
                                                        class="text-{{ $character->class ? slug($character->class) : ''}}-important tag d-inline">
                                                        <span class="role-circle" style="background-color:{{ getHexColorFromDec(($character->raid_group_color ? $character->raid_group_color : '')) }}"></span>{{ $character->name }}
                                                        @if ($character->is_alt)
                                                            <span class="text-gold">{{ __("alt") }}</span>
                                                        @endif
                                                        @if ($character->pivot->is_offspec)
                                                            <span class="text-muted">{{ __("OS") }}</span>
                                                        @endif
                                                        @if (!$guild->is_attendance_hidden && (isset($character->attendance_percentage) || isset($character->raid_count)))
                                                            <span class="small">
                                                                @include('partials/attendanceTag', ['attendancePercentage' => $character->attendance_percentage, 'raidCount' => $character->raid_count, 'raidShort' => true])
                                                            </span>
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
                                        <em>{{ __("None") }}</em>
                                    </div>
                                @endif
                            @else
                                <div class="text-muted">
                                    {{ __("Can't set prios for this item") }} <abbr title="{{ __("Cannot set prios for items that aren't in the loot tables for a boss. This includes token rewards. Set prios on the token instead.") }}">?</abbr>
                                </div>
                            @endif
                        </li>
                    @endif
                    @if ($item->childItems->count() || $item->parentItem)
                        <li class="list-inline-item bg-lightest rounded p-3 mt-3 align-top">
                            <ul class="no-indent no-bullet">
                                <li class="">
                                    <h2 class="font-weight-bold mb-3">
                                        {{ __("Related") }}
                                        <span class="font-weight-normal smaller text-muted">{{ $item->childItems->count() }}</span>
                                    </h2>
                                </li>
                                @if ($item->parentItem)
                                    <li class="">
                                        @include('partials/item', ['item' => $item->parentItem, 'wowheadLink' => false])
                                    </li>
                                @endif
                                @foreach ($item->childItems as $childItem)
                                    <li class="">
                                        @include('partials/item', ['item' => $childItem, 'wowheadLink' => false])
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endif
            </ul>
        </div>
    </div>

    {{--
    <div class="row pt-2 mb-3 bg-lightest rounded">
        <div class="col-12">
            <h2 class="font-weight-bold pl-2">
                {{ __("Prio'd") }}
            </h2>
        </div>
        <div class="col-12 pr-0 pl-0">
            @if ($priodCharacters && $priodCharacters->count() > 0)
                @include('partials/characterDatatable', ['characters' => $priodCharacters])
            @else
                <ul>
                    <li class="lead no-bullet">
                        <em>{{ __("Nobody has been prio'd for this item yet") }}</em>
                    </li>
                </ul>
            @endif
        </div>
    </div>
    --}}

    @if ($showWishlist)
        <div class="row pt-2 mb-3 bg-lightest rounded">
            <div class="col-12">
                <h2 class="font-weight-bold pl-2">
                    <span class="fas fa-fw fa-scroll-old text-legendary"></span>
                    {{ __("Wishlisted") }}
                    <span class="small text-muted">{{ __("ordered by who ranked it higher") }}</span>
                </h2>
            </div>
            <div class="col-12 pr-0 pl-0">
                @if ($wishlistCharacters && $wishlistCharacters->count() > 0)
                    @include('partials/characterDatatable', ['characters' => $wishlistCharacters])
                @else
                    <ul>
                        <li class="lead no-bullet">
                            <em>{{ __("Nobody has this item in their wishlist yet") }}</em>
                        </li>
                    </ul>
                @endif
            </div>
        </div>
    @endif

    <div class="row mr-1 ml-1 mb-3 pt-1  bg-lightest rounded">
        <div class="col-12">
            <h2>
                <span class="fas fa-fw fa-sack text-success"></span>
                {{ __("Have It") }}
            </h2>
            @if ($receivedAndRecipeCharacters->count() > 0)
                <ul class="list-inline striped">
                    @foreach ($receivedAndRecipeCharacters as $character)
                        <li class="list-inline-item rounded pt-2 pl-3 pb-3 pr-3">
                            <ul class="list-inline">
                                @if ($character->pivot->is_offspec)
                                    <li class="list-inline-item font-weight-bold">
                                        <span title="offspec item">{{ __("OS") }}</span>
                                    </li>
                                @endif
                                @if ($character->pivot->received_at || $character->pivot->created_at)
                                    <li class="list-inline-item text-muted small">
                                        {{ __("received") }}
                                        <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $character->pivot->received_at ? $character->pivot->received_at : $character->pivot->created_at }}"></span>
                                        {{ __("ago") }}
                                    </li>
                                @endif
                            </ul>
                            @include('character/partials/header', ['showDetails' => false, 'showEdit' => false, 'showOwner' => false])
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="lead mb-3">
                    <em>{{ __("Nobody has this item in their character sheet yet") }}</em>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    @php
        // Warglaives have a custom display string added...
        $itemName = $item->name;
        $itemName = str_replace(' (offhand)', '', $itemName);
        $itemName = str_replace(' (mainhand)', '', $itemName);

        $itemNames = [$itemName];
        foreach ($item->childItems as $childItem) {
            $itemNames[] = $childItem->name;
        }
        if ($item->parentItem) {
            $itemNames[] = $item->parentItem->name;
        }
    @endphp
    var characters       = {!! $showWishlist ? ($showOfficerNote ? $wishlistCharacters->makeVisible('officer_note')->toJson() : $wishlistCharacters->toJson()) : null !!};
    var currentWishlistNumber = {{ $guild->current_wishlist_number }};
    var guild            = {!! $guild->toJson() !!};
    var filterWishlistsByItemName = true;
    // When we filter wishlists to decide whether or not it contains the item on this page, filter by these names
    var itemNames        = {!! json_encode($itemNames) !!};
    var maxWishlistLists = {{ App\Http\Controllers\CharacterLootController::MAX_WISHLIST_LISTS }};
    var raidGroups       = {!! $raidGroups->toJson() !!};
    var showEdit         = {{ $showEdit ? 'true' : 'false' }};
    var showOfficerNote  = {{ $showOfficerNote ? 'true' : 'false' }};
    var showPrios        = {{ $showPrios ? 'true' : 'false' }};
    var showWishlist     = {{ $showWishlist ? 'true' : 'false' }};
</script>
<script src="{{ loadScript('roster.js') }}"></script>
@endsection

