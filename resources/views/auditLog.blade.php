@extends('layouts.app')
@section('title', 'Audit Log - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-wight-medium">
                        <span class="fas fa-fw fa-clipboard-list-check text-gold"></span>
                        Audit Log
                    </h1>
                    <ul>
                        <li class="small no-bullet font-italic">
                            Whodunit?
                        </li>
                        @if (!$showPrios)
                            <li class="small text-danger">
                                Prios are hidden by your guild master(s)
                            </li>
                        @elseif ($guild->is_prio_private)
                            <li class="small text-warning">
                                Prios are hidden from raiders
                            </li>
                        @endif
                        @if (!$showWishlist)
                            <li class="small text-danger">
                                Wishlists are hidden by your guild master(s)
                            </li>
                        @elseif ($guild->is_wishlist_private)
                            <li class="small text-warning">
                                Wishlists are hidden from raiders
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="row">
                @if ($resources)
                    <div class="col-12 pb-3 d-flex flex-wrap">
                        @foreach ($resources as $resource)
                            <div>
                                <div class="bg-light rounded pt-1 pb-1 pl-3 pr-3 mr-3 mb-3">
                                    @if($resource instanceof \App\Batch)
                                        @if ($resource->name)
                                            {{ $resource->name }}
                                        @else
                                            Batch {{ $resource->id }}
                                        @endif
                                        @if ($resource->note)
                                            <p>
                                                {{ $resource->note }}
                                            </p>
                                        @endif
                                    @elseif($resource instanceof \App\Character)
                                        @include('character/partials/header', ['character' => $resource, 'headerSize' => 1, 'showEdit' => false, 'showIcon' => false])
                                    @elseif($resource instanceof \App\Item)
                                        @include('partials/item', ['item' => $resource, 'wowheadLink' => false])
                                    @elseif($resource instanceof \App\Member)
                                        @include('member/partials/header', ['member' => $resource, 'discordUsername' => $resource->user->discord_username, 'headerSize' => 1, 'showEdit' => false, 'titlePrefix' => null])
                                    @elseif($resource instanceof \App\Raid)
                                        <div class="mt-1 mb-2 font-weight-bold">
                                            @include('partials/raid', ['raid' => $resource, 'raidColor' => $resource->getColor()])
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="form-group">
                        <label for="character_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-users text-muted"></span>
                            Character
                        </label>
                        <select name="character_id" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                            <option value="">
                                —
                            </option>
                            @foreach ($guild->characters as $character)
                                <option value="{{ $character->id }}"
                                        data-tokens="{{ $character->id }}" class="text-{{ strtolower($character->class) }}-important"
                                        {{ Request::get('character_id') && Request::get('character_id') == $character->id ? 'selected' : ''}}>
                                    {{ $character->name }} &nbsp; {{ $character->class ? '(' . $character->class . ')' : '' }} &nbsp; {{ $character->is_alt ? "Alt" : '' }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="form-group">
                        <label for="member_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-user text-muted"></span>
                            Member
                        </label>
                        <select name="member_id" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                            <option value="">
                                —
                            </option>
                            @foreach ($guild->members as $member)
                                <option value="{{ $member->id }}"
                                    data-tokens="{{ $member->id }}"
                                    {{ Request::get('member_id') && Request::get('member_id') == $member->id ? 'selected' : ''}}>
                                    {{ $member->username }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="form-group">
                        <label for="raid_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-helmet-battle text-muted"></span>
                            Raid
                        </label>
                        <select name="raid_id" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                            <option value="">
                                —
                            </option>
                            @foreach ($guild->raids as $raid)
                                <option value="{{ $raid->id }}"
                                    data-tokens="{{ $raid->id }}"
                                    {{ Request::get('raid_id') && Request::get('raid_id') == $raid->id ? 'selected' : ''}}>
                                    {{ $raid->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="form-group">
                        <label for="type" class="font-weight-bold">
                            <span class="fas fa-fw fa-scroll-old text-muted"></span>
                            Category
                        </label>
                        <select name="type" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                            <option value="" data-tokens="">
                                —
                            </option>
                            <option value="{{ \App\Item::TYPE_PRIO }}"
                                data-tokens="{{ \App\Item::TYPE_PRIO }}"
                                {{ Request::get('type') && Request::get('type') == \App\Item::TYPE_PRIO ? 'selected' : ''}}>
                                Prio
                            </option>
                            <option value="{{ 'received_all' }}"
                                data-tokens="received_all"
                                {{ Request::get('type') && Request::get('type') == 'received_all' ? 'selected' : ''}}>
                                Received (all)
                            </option>
                            <option value="{{ \App\AuditLog::TYPE_ASSIGN }}"
                                data-tokens="{{ \App\AuditLog::TYPE_ASSIGN }}"
                                {{ Request::get('type') && Request::get('type') == \App\AuditLog::TYPE_ASSIGN ? 'selected' : ''}}>
                                Received (via assign loot page)
                            </option>
                            <option value="{{ \App\Item::TYPE_RECEIVED }}"
                                data-tokens="{{ \App\Item::TYPE_RECEIVED }}"
                                {{ Request::get('type') && Request::get('type') == \App\Item::TYPE_RECEIVED ? 'selected' : ''}}>
                                Received (via character loot page)
                            </option>
                            <option value="{{ \App\Item::TYPE_RECIPE }}"
                                data-tokens="{{ \App\Item::TYPE_RECIPE }}"
                                {{ Request::get('type') && Request::get('type') == \App\Item::TYPE_RECIPE ? 'selected' : ''}}>
                                Recipe
                            </option>
                            <option value="{{ \App\Item::TYPE_WISHLIST }}"
                                data-tokens="{{ \App\Item::TYPE_WISHLIST }}"
                                {{ Request::get('type') && Request::get('type') == \App\Item::TYPE_WISHLIST ? 'selected' : ''}}>
                                Wishlist
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="form-group">
                        <label for="item_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-sack text-muted"></span>
                            Item
                        </label>
                        <input name="item_id" maxlength="40" data-max-length="40" type="text" placeholder="type an item name" class="js-item-autocomplete-link js-input-text form-control dark">
                        <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <ol class="no-bullet no-indent striped">
                        @foreach ($logs as $log)
                            <li class="p-1 pl-3 rounded">
                                <div class="d-flex flex-row">
                                    <div class="list-timestamp text-right text-muted p-2">
                                        @if ($log->member_id)
                                            <a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $log->member_id, 'usernameSlug' => $log->member_slug]) }}" class="text-muted">
                                                <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $log->created_at }}"></span> ago
                                            </a>
                                        @else
                                            <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $log->created_at }}"></span> ago
                                        @endif
                                    </div>

                                    <div class="p-2">
                                        <ul class="list-inline">
                                            <li class="list-inline-item">
                                                {{ $log->description }}
                                            </li>

                                            @if ($log->item_id)
                                                <li class="list-inline-item">
                                                    @include('partials/item', ['wowheadLink' => false, 'auditLink' => false, 'itemId' => $log->item_id, 'itemName' => $log->item_name, 'fontWeight' => 'light'])
                                                </li>
                                            @endif
                                            @if ($log->other_member_id)
                                                <li class="list-inline-item">
                                                    <a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $log->member_id, 'usernameSlug' => $log->other_member_slug]) }}" class="text-muted">
                                                        {{ $log->other_member_username }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($log->character_id)
                                                <li class="list-inline-item">
                                                    <a href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $log->character_id, 'nameSlug' => $log->character_slug]) }}" class="text-muted">
                                                        {{ $log->character_name }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($log->instance_id)
                                                <li class="list-inline-item text-muted">
                                                    {{ $log->instance_name }}
                                                </li>
                                            @endif
                                            @if ($log->item_source_id)
                                                <li class="list-inline-item text-muted">
                                                    {{ $log->item_source_name }}
                                                </li>
                                            @endif
                                            @if ($log->raid_id)
                                                <li class="list-inline-item text-muted">
                                                    <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $log->raid_id]) }}" class="text-muted">
                                                        {{ $log->raid_name }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($log->role_id)
                                                <li class="list-inline-item text-muted">
                                                    {{ $log->role_name }}
                                                </li>
                                            @endif

                                            @if ($log->batch_id)
                                                <li class="list-inline-item text-muted">
                                                    <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'batch_id' => $log->batch_id]) }}" class="small text-muted">
                                                        {{ $log->batch_name ? $log->batch_name : 'batch ' . $log->batch_id }}
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </div>

                <div class="col-12 mt-3">
                    {{ $logs->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var guild = {!! $guild->toJson() !!};

    $("select").change(function () {
        updateUrl($(this).prop("name"), $(this).val());
    });

    // Updates the URL with the given parameter and value, then reloads the page
    function updateUrl(paramName, paramValue) {
        let url = new URL(location);
        url.searchParams.set(paramName, paramValue);
        url.searchParams.delete('page');
        location = url;
    }

    $(".js-item-autocomplete-link").each(function () {
        var self = this; // Allows callback functions to access `this`
        $(this).autocomplete({
            source: function (request, response) {
                $.ajax({
                    method: "get",
                    dataType: "json",
                    url: "/api/items/query/" + request.term,
                    success: function (data) {
                        response(data);
                        if (data.length <= 0) {
                            $(self).nextAll(".js-status-indicator").show();
                            $(self).nextAll(".js-status-indicator").html("<span class=\"bg-danger\">&nbsp;" + request.term + " not found&nbsp;</span>");
                        }
                    },
                    error: function () {
                    }
                });
            },
            search: function () {
                $(this).nextAll(".js-status-indicator").hide();
                $(this).nextAll(".js-status-indicator").empty();
                $(this).nextAll(".js-loading-indicator").show();
            },
            response: function () {
                $(this).nextAll(".js-loading-indicator").hide();
            },
            select: function (event, ui) {
                if (ui.item.value) {
                    // Put the value into a tag below the input
                    value = ui.item.value;
                    label = ui.item.label;

                    // Only allow numbers (an item ID must be found)
                    if (Number.isInteger(value)) {
                        updateUrl('item_id', value);
                    }

                    // prevent autocomplete from autofilling this.val()
                    return false;
                }
            },
            minLength: 1,
            delay: 400
        });
    });
</script>
@endsection
