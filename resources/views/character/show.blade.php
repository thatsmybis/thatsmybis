@extends('layouts.app')
@section('title', $character->name . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        @if (!request()->get('hideAds'))
            <div class="col-lg-2 d-lg-block d-none m-0 p-0">
                @include('partials/adLeftBanner')
            </div>
        @endif
        <div class="col-12 {{ request()->get('hideAds') ? 'col-xl-8 offset-xl-2 col-lg-10 offset-lg-1' : 'col-xl-8 col-lg-10 offset-lg-0' }}">
            <div class="row mb-3">
                <div class="col-12 pt-2 bg-lightest rounded">
                    @include('character/partials/header', [
                        'headerSize' => 1,
                        'showEdit' => $showEdit,
                        'showIcon' => false,
                        'showLogs' => true,
                        'showSecondaryRaidGroups' => true,
                    ])
                </div>
            </div>

            <div class="row mb-3 pt-3 bg-light rounded">
                <div class="col-12 pb-3">
                    <label class="sr-only">{{ __("Raid History") }}</label>
                    @if ($character->raids->count())
                        @include('partials/raidHistoryTable', ['raids' => $character->raids, 'showOfficerNote' => ($viewOfficerNotePermission && !isStreamerMode())])
                    @else
                        <strong>{{ __("Raid History") }}:</strong>
                        {{ __("None yet") }}
                    @endif
                </div>
            </div>

            <div class="row mb-3 pt-3 bg-light rounded">

                @if ($showPrios)
                    <div class="col-12 mb-2">
                        <span class="text-gold font-weight-bold">
                            <span class="fas fa-fw fa-sort-amount-down"></span>
                            {{ __("Prios") }}
                        </span>
                        <span id="hide-prio-os" class="text-unselectable text-link cursor-pointer small" title="{{ __('hide OS items') }}">
                            <span class="fas fa-fw fa-eye-slash"></span>
                        </span>
                    </div>
                    <div class="col-12 pb-3">
                        @if ($character->relationLoaded('prios') && $character->prios->count() > 0)
                            <ol class="">
                                @php
                                    $lastRaidGroupId = null;
                                @endphp
                                @foreach ($character->prios as $item)
                                    @if ($item->pivot->raid_group_id != $lastRaidGroupId)
                                        @php
                                            $lastRaidGroupId = $item->pivot->raid_group_id;
                                        @endphp
                                        <li class="text-muted no-bullet font-italic small font-weight-bold">
                                            {{ $guild->allRaidGroups->find($item->pivot->raid_group_id)->name }}
                                        </li>
                                    @endif
                                    <li value="{{ $item->pivot->order }}" class="{{ $item->pivot->is_offspec ? 'js-prio-os' : '' }}">
                                        @include('partials/item', [
                                            'wowheadLink'   => false,
                                            'itemDate'      => $item->pivot->created_at,
                                            'itemUsername'  => $item->added_by_username,
                                            'strikeThrough' => $item->pivot->is_received,
                                            'showTier'      => true,
                                            'tierMode'      => $guild->tier_mode,
                                        ])
                                        @include('character/partials/itemDetails', ['hideCreatedAt' => true, 'hideRaidGroup' => true])
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <div class="pl-4">
                                —
                            </div>
                        @endif
                    </div>
                @endif

                @if ($showWishlist)
                    @php
                        $wishlists = [];
                        for ($i = 1; $i <= App\Http\Controllers\CharacterLootController::MAX_WISHLIST_LISTS; $i++) {
                            if ($i ==  $guild->current_wishlist_number || $character->allWishlists->where('list_number', $i)->count()) {
                                $wishlists[$i] = $character->allWishlists->where('list_number', $i);
                            }
                        }

                        $wishlistNames = $guild->getWishlistNames();
                    @endphp

                    @include('character/partials/wishlist', [
                        'wishlist'       => $wishlists[$guild->current_wishlist_number],
                        'isActive'       => true,
                        'wishlistNames'  => $wishlistNames,
                        'wishlistNumber' => $guild->current_wishlist_number,
                    ])

                    @if (count($wishlists))
                        <div class="col-12 mb-4">
                            <span id="show-inactive-wishlists" class="cursor-pointer font-weight-bold text-warning">
                                <span class="text-legendary fas fa-fw fa-scroll-old"></span> {{ __("show inactive wishlists") }} ({{count($wishlists) - 1 }}) <span class="text-link fas fa-fw fa-eye-slash"></span>
                            </span>
                        </div>

                        <div id="inactive-wishlists" style="display:none;">
                            @foreach ($wishlists as $key => $wishlist)
                                @if ($key != $guild->current_wishlist_number && count($wishlist))
                                    @include('character/partials/wishlist', [
                                        'wishlist'       => $wishlist,
                                        'isActive'       => false,
                                        'wishlistNames'  => $wishlistNames,
                                        'wishlistNumber' => $key,
                                    ])
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endif

                <div class="col-12 mb-2">
                    @if ($showEditLoot)
                        <a href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                            <span class="text-success font-weight-bold">
                                <span class="fas fa-fw fa-sack"></span>
                                {{ __("Loot Received") }}
                            </span>
                            <span class="small align-text- fas fa-fw fa-pencil"></span>
                        </a>
                    @else
                        <span class="text-success font-weight-bold">
                            <span class="fas fa-fw fa-sack"></span>
                            {{ __("Loot Received") }}
                        </span>
                    @endif
                </div>
                <div class="col-12 pb-3">
                    @if ($character->received->count() > 0)
                        <ol class="">
                            @foreach ($character->received as $item)
                                <li class="">
                                    @include('partials/item', [
                                        'wowheadLink' => false,
                                        'itemDate' => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                                        'itemUsername' => $item->added_by_username,
                                        'showTier'      => true,
                                        'tierMode'      => $guild->tier_mode,
                                    ])
                                    @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <div class="pl-4">
                            —
                        </div>
                    @endif
                </div>

                <div class="col-12 mb-2">
                    @if ($showEditLoot)
                        <a href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                            <span class="text-gold font-weight-bold">
                                <span class="fas fa-fw fa-book"></span>
                                {{ __("Recipes") }}
                            </span>
                            <span class="small align-text- fas fa-fw fa-pencil"></span>
                        </a>
                    @else
                        <span class="text-gold font-weight-bold">
                            <span class="fas fa-fw fa-book"></span>
                            {{ __("Recipes") }}
                        </span>
                    @endif
                </div>
                <div class="col-12 pb-3">
                    @if ($character->recipes->count() > 0)
                        <ol class="">
                            @foreach ($character->recipes as $item)
                                <li class="">
                                    @include('partials/item', ['wowheadLink' => false, 'itemDate' => $item->pivot->created_at, 'itemUsername' => $item->added_by_username])
                                    @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <div class="pl-4">
                            —
                        </div>
                    @endif
                </div>
            </div>

            <div class="row mb-3 pt-3 bg-light rounded">
                <div class="col-12 pb-3">
                    <label class="sr-only">{{ __("Received") }}</label>
                    @if ($character->raids->count())
                        @include('partials/itemSlotsTable', ['items' => $character->received])
                    @else
                        {{ __("None yet") }}
                    @endif
                </div>
            </div>

            <form id="noteForm" role="form" method="POST" action="{{ route('character.updateNote', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="id" value="{{ $character->id }}" />
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
                            {{ __("Public Note") }}
                        </span>
                    </div>
                    <div class="col-12 mb-3 pl-4">
                        <span class="js-markdown-inline">{{ $character->public_note ? $character->public_note : '—' }}</span>
                        @if ($currentMember->id == $character->member_id || $editOfficerNotePermission)
                            <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                        @endif
                    </div>
                    @if ($currentMember->id == $character->member_id || $editOfficerNotePermission)
                        <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                            <div class="form-group">
                                <label for="public_note" class="font-weight-bold">
                                    <span class="sr-only">{{ __("Public Note") }}</span>
                                    <small class="text-muted">{{ __("anyone in the guild can see this") }}</small>
                                </label>
                                <textarea maxlength="140" data-max-length="140" name="public_note" rows="2" placeholder="anyone in the guild can see this" class="form-control dark">{{ old('public_note') ? old('public_note') : ($character ? $character->public_note : '') }}</textarea>
                            </div>
                        </div>
                    @endif

                    @if ($viewOfficerNotePermission)
                        <div class="col-12">
                            <span class="text-muted font-weight-bold">
                                <span class="fas fa-fw fa-shield"></span>
                                {{ __("Officer Note") }}
                            </span>
                        </div>
                        <div class="col-12 mb-3 pl-4">
                            @if (!isStreamerMode())
                                <span class="js-markdown-inline">{{ $character->officer_note ? $character->officer_note : '—' }}</span>
                                @if ($editOfficerNotePermission)
                                    <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                                @endif
                            @else
                                {{ __("Hidden in streamer mode") }}
                            @endif
                        </div>
                        <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                            <div class="form-group">
                                <label for="officer_note" class="font-weight-bold">
                                    <span class="sr-only">{{ __("Officer Note") }}</span>
                                    <small class="text-muted">{{ __("only officers can see this") }}</small>
                                </label>
                                @if (isStreamerMode())
                                    {{ __("Hidden in streamer mode") }}
                                @else
                                    <textarea maxlength="140" data-max-length="140" name="officer_note" rows="2" placeholder="only officers can see this" class="form-control dark">{{ old('officer_note') ? old('officer_note') : ($character ? $character->officer_note : '') }}</textarea>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{--
                        @if ($showPersonalNote)
                            <div class="col-12">
                                <span class="text-muted font-weight-bold">
                                    <span class="fas fa-fw fa-eye-slash"></span>
                                    {{ __("Personal Note") }}
                                </span>
                            </div>
                            <div class="col-12 mb-3 pl-4">
                                <span class="js-markdown-inline">{{ $character->personal_note ? $character->personal_note : '—' }}</span>
                                <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                            </div>
                            <div class="js-note-input col-12 pl-4" style="display:none;">
                                <div class="form-group">
                                    <label for="personal_note" class="font-weight-bold">
                                        <span class="sr-only">{{ __("Personal Note") }}</span>
                                        <small class="text-muted">{{ __("only you can see this") }}</small>
                                    </label>
                                    <textarea maxlength="2000" data-max-length="2000" name="personal_note" rows="2" placeholder="only you can see this" class="form-control dark">{{ old('personal_note') ? old('personal_note') : ($character ? $character->personal_note : '') }}</textarea>
                                </div>
                            </div>
                        @endif
                    --}}

                    <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                        <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> {{ __("Save") }}</button>
                    </div>
                </div>
            </form>
        </div>
        @if (!request()->get('hideAds'))
            <div class="col-xl-2 d-lg-block d-none m-0 p-0">
                @include('partials/adRightBanner')
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $("#raids").DataTable({
            order       : [], // Disable initial auto-sort; relies on server-side sorting
            paging      : true,
            pageLength  : 5,
            fixedHeader : { // Header row sticks to top of window when scrolling down
                headerOffset : 43,
            },
            oLanguage: {
                sSearch: "<abbr title='{{ __('Fuzzy searching is ON. To search exact text, wrap your search in \"quotes\"') }}'>{{ __('Search') }}</abbr>"
            },
            columns : [
                { orderable : false },
            ]
        });

        $("#itemSlots").DataTable({
            order       : [], // Disable initial auto-sort; relies on server-side sorting
            paging      : false,
            fixedHeader : { // Header row sticks to top of window when scrolling down
                headerOffset : 43,
            },
            oLanguage: {
                sSearch: "<abbr title='{{ __('Fuzzy searching is ON. To search exact text, wrap your search in \"quotes\"') }}'>{{ __('Search') }}</abbr>"
            },
            columns : [
                { orderable : false },
                { orderable : false },
            ]
        });

        warnBeforeLeaving("#noteForm");

        $("#show-inactive-wishlists").click(function () {
            $("#inactive-wishlists").toggle();
        });

        $("#hide-prio-os").click(function () {
            $(".js-prio-os").toggle();
        });
    });
</script>
@endsection
