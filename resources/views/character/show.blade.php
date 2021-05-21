@extends('layouts.app')
@section('title', $character->name . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row mb-3">
                <div class="col-12 pt-2 bg-lightest rounded">
                    @include('character/partials/header', ['headerSize' => 1, 'showEdit' => $showEdit, 'showIcon' => false, 'showLogs' => true])
                </div>
            </div>

            <div class="row mb-3 pt-3 bg-light rounded">
                <div class="col-12 mb-2">
                    <span class="font-weight-bold">
                        <span class="fas fa-fw fa-helmet-battle text-dk"></span>
                        Raid History
                    </span>
                </div>

                <div class="col-12 pb-3">
                    @if ($character->raids->count())
                        @include('partials/raidHistoryTable', ['raids' => $character->raids, 'showOfficerNote' => ($viewOfficerNotePermission && !isStreamerMode())])
                    @else
                        None yet
                    @endif
                </div>
            </div>

            <div class="row mb-3 pt-3 bg-light rounded">

                @if ($showPrios)
                    <div class="col-12 mb-2">
                        <span class="text-gold font-weight-bold">
                            <span class="fas fa-fw fa-sort-amount-down"></span>
                            Prios
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
                                    <li value="{{ $item->pivot->order }}">
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
                    <div class="col-12 mb-2">
                        @if ($showEditLoot)
                            <a href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                                <span class="text-legendary font-weight-bold">
                                    <span class="fas fa-fw fa-scroll-old"></span>
                                    Wishlist
                                </span>
                                <span class="small align-text- fas fa-fw fa-pencil"></span>
                            </a>
                        @else
                            <span class="text-legendary font-weight-bold">
                                <span class="fas fa-fw fa-scroll-old"></span>
                                Wishlist
                            </span>
                        @endif
                        <span class="js-sort-wishlists text-link">
                            <span class="fas fa-fw fa-exchange cursor-pointer"></span>
                        </span>
                    </div>
                    <div class="col-12 pb-3">
                        @if ($character->relationLoaded('wishlist') && $character->wishlist->count() > 0)
                            <ol class="js-wishlist-sorted" style="{{ $guild->do_sort_items_by_instance ? '' : 'display:none;' }}">
                                @php
                                $lastInstanceId = null;
                                @endphp
                                @foreach ($character->wishlist->sortBy('order')->sortByDesc('instance_order') as $item)
                                    @if ($item->instance_id != $lastInstanceId)
                                        <li class="no-bullet no-indent {{ !$loop->first ? 'mt-3' : '' }}">
                                            {{ $item->instance_name }}
                                        </li>
                                    @endif

                                    <li value="{{ $item->pivot->order }}">
                                        @include('partials/item', [
                                            'wowheadLink'   => false,
                                            'itemDate'      => $item->pivot->created_at,
                                            'itemUsername'  => $item->added_by_username,
                                            'strikeThrough' => $item->pivot->is_received,
                                            'showTier'      => true,
                                            'tierMode'      => $guild->tier_mode,
                                        ])
                                        @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                                    </li>

                                    @php
                                        $lastInstanceId = $item->instance_id;
                                    @endphp
                                @endforeach
                            </ol>

                            <ol class="js-wishlist-unsorted" style="{{ $guild->do_sort_items_by_instance ? 'display:none;' : '' }}">
                                @foreach ($character->wishlist as $item)
                                    <li value="{{ $item->pivot->order }}">
                                        @include('partials/item', [
                                            'wowheadLink'   => false,
                                            'itemDate'      => $item->pivot->created_at,
                                            'itemUsername'  => $item->added_by_username,
                                            'strikeThrough' => $item->pivot->is_received,
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
                @endif

                <div class="col-12 mb-2">
                    @if ($showEditLoot)
                        <a href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                            <span class="text-success font-weight-bold">
                                <span class="fas fa-fw fa-sack"></span>
                                Loot Received
                            </span>
                            <span class="small align-text- fas fa-fw fa-pencil"></span>
                        </a>
                    @else
                        <span class="text-success font-weight-bold">
                            <span class="fas fa-fw fa-sack"></span>
                            Loot Received
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
                                Recipes
                            </span>
                            <span class="small align-text- fas fa-fw fa-pencil"></span>
                        </a>
                    @else
                        <span class="text-gold font-weight-bold">
                            <span class="fas fa-fw fa-book"></span>
                            Recipes
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
                            Public Note
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
                                    <span class="sr-only">Public Note</span>
                                    <small class="text-muted">anyone in the guild can see this</small>
                                </label>
                                <textarea maxlength="140" data-max-length="140" name="public_note" rows="2" placeholder="anyone in the guild can see this" class="form-control dark">{{ old('public_note') ? old('public_note') : ($character ? $character->public_note : '') }}</textarea>
                            </div>
                        </div>
                    @endif

                    @if ($viewOfficerNotePermission)
                        <div class="col-12">
                            <span class="text-muted font-weight-bold">
                                <span class="fas fa-fw fa-shield"></span>
                                Officer Note
                            </span>
                        </div>
                        <div class="col-12 mb-3 pl-4">
                            @if (!isStreamerMode())
                                <span class="js-markdown-inline">{{ $character->officer_note ? $character->officer_note : '—' }}</span>
                                @if ($editOfficerNotePermission)
                                    <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                                @endif
                            @else
                                Hidden in streamer mode
                            @endif
                        </div>
                        <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                            <div class="form-group">
                                <label for="officer_note" class="font-weight-bold">
                                    <span class="sr-only">Officer Note</span>
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
                        @if ($showPersonalNote)
                            <div class="col-12">
                                <span class="text-muted font-weight-bold">
                                    <span class="fas fa-fw fa-eye-slash"></span>
                                    Personal Note
                                </span>
                            </div>
                            <div class="col-12 mb-3 pl-4">
                                <span class="js-markdown-inline">{{ $character->personal_note ? $character->personal_note : '—' }}</span>
                                <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                            </div>
                            <div class="js-note-input col-12 pl-4" style="display:none;">
                                <div class="form-group">
                                    <label for="personal_note" class="font-weight-bold">
                                        <span class="sr-only">Personal Note</span>
                                        <small class="text-muted">only you can see this</small>
                                    </label>
                                    <textarea maxlength="2000" data-max-length="2000" name="personal_note" rows="2" placeholder="only you can see this" class="form-control dark">{{ old('personal_note') ? old('personal_note') : ($character ? $character->personal_note : '') }}</textarea>
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

@section('scripts')
<script>
    $(document).ready(function () {
        $("#raids").DataTable({
            "order"       : [], // Disable initial auto-sort; relies on server-side sorting
            "paging"      : true,
            "pageLength"  : 5,
            "fixedHeader" : false, // Header row sticks to top of window when scrolling down
            "columns" : [
                { "orderable" : false },
            ]
        });

        warnBeforeLeaving("#noteForm");
    });
</script>
@endsection
