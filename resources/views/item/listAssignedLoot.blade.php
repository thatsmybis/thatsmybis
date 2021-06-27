@extends('layouts.app')
@section('title', 'Assigned Loot - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-gift text-gold"></span>
                        Assigned Loot
                    </h1>
                </div>
            </div>

            <div class="row">
                @if ($resources)
                    <div class="col-12 mb-3 text-5">
                        Filter:
                        <a href="{{ route('item.assignLoot.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            reset
                        </a>
                    </div>
                    <div class="col-12 pb-3 d-flex flex-wrap">
                        @foreach ($resources as $resource)
                            <div>
                                <div class="bg-light rounded pt-1 pb-1 pl-3 pr-3 mr-3 mb-3">
                                    @if($resource instanceof \App\Item)
                                        @include('partials/item', ['item' => $resource, 'wowheadLink' => false])
                                    @elseif($resource instanceof \App\Character)
                                        @include('character/partials/header', ['character' => $resource, 'headerSize' => 1, 'showEdit' => false, 'showIcon' => false])
                                    @elseif($resource instanceof \App\Member)
                                        @include('member/partials/header', ['member' => $resource, 'discordUsername' => $resource->user->discord_username, 'headerSize' => 1, 'showEdit' => false, 'titlePrefix' => null])
                                    @elseif($resource instanceof \App\Raid)
                                        <div class="mt-1 mb-2 font-weight-bold">
                                            @include('partials/raid', ['raid' => $resource])
                                        </div>
                                    @elseif($resource instanceof \App\RaidGroup)
                                        <div class="mt-1 mb-2 font-weight-bold">
                                            @include('partials/raidGroup', ['raidGroup' => $resource, 'raidGroupColor' => $resource->getColor()])
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="row">

                <div class="col-lg-2 col-md-3 col-6">
                    <div class="form-group">
                        <label for="min_date" class="font-weight-bold">
                            <span class="fas fa-fw fa-calendar-minus text-muted"></span>
                            Min Date
                        </label>
                        <input name="min_date" min="2004-09-22"
                            max="{{ getDateTime('Y-m-d') }}"
                            value="{{ Request::get('min_date') ? Request::get('min_date') : ''}}"
                            type="date"
                            placeholder="—"
                            class="form-control dark"
                            autocomplete="off">
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <div class="form-group">
                        <label for="max_date" class="font-weight-bold">
                            <span class="fas fa-fw fa-calendar-plus text-muted"></span>
                            Max Date
                        </label>
                        <input name="max_date"
                            min="2004-09-22"
                            value="{{ Request::get('max_date') ? Request::get('max_date') : ''}}"
                            max="{{ getDateTime('Y-m-d') }}"
                            type="date"
                            placeholder="—"
                            class="form-control dark"
                            autocomplete="off">
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
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
                        <label for="raid_group_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-helmet-battle text-muted"></span>
                            Raid Group
                        </label>
                        <select name="raid_group_id" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                            <option value="">
                                —
                            </option>
                            @foreach ($guild->raidGroups as $raidGroup)
                                <option value="{{ $raidGroup->id }}"
                                    data-tokens="{{ $raidGroup->id }}"
                                    {{ Request::get('raid_group_id') && Request::get('raid_group_id') == $raidGroup->id ? 'selected' : ''}}>
                                    {{ $raidGroup->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="form-group">
                        <label for="item_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-sack text-muted"></span>
                            Item
                        </label>
                        <input name="item_id" maxlength="40" data-max-length="40" type="text" placeholder="type an item name" autocomplete="off" class="js-item-autocomplete-link js-input-text form-control dark">
                        <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="form-group">
                        <label for="item_instance_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-dungeon text-muted"></span>
                            Item Dungeon
                        </label>
                        <select name="item_instance_id" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                            <option value="" data-tokens="">
                                —
                            </option>
                            @foreach ($instances as $instance)
                                <option value="{{ $instance->id }}"
                                    data-tokens="{{ $instance->name }}"
                                    {{ Request::get('item_instance_id') && Request::get('item_instance_id') == $instance->id ? 'selected' : ''}}>
                                    {{ $instance->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <ol class="no-bullet no-indent striped">
                        @if ($batches->count())
                            @foreach ($batches as $batch)
                                <li class="p-1 pl-3 rounded">
                                    <div class="d-flex flex-row">
                                        <div class="list-timestamp text-right text-muted p-2 small">
                                            @if ($batch->member_id)
                                                <a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $batch->member_id, 'usernameSlug' => $batch->member_slug]) }}" class="text-muted">
                                                    <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $batch->created_at }}"></span> ago
                                                </a>
                                            @else
                                                <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $batch->created_at }}"></span> ago
                                            @endif
                                        </div>

                                        <div class="p-2">
                                            <ul class="list-inline">
                                                <li class="list-inline-item">
                                                    <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'batch_id' => $batch->id]) }}" class="text-white">
                                                        {{ $batch->name ? $batch->name : "Batch {$batch->id}" }}
                                                    </a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'batch_id' => $batch->id]) }}">
                                                        <span class="text-uncommon">{{ $batch->item_count }}</span> <span class="text-muted">items</span>
                                                    </a>
                                                </li>

                                                @if ($batch->raid_id)
                                                    <li class="list-inline-item text-muted">
                                                        <a href="{{ route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $batch->raid_id, 'raidSlug' => $batch->raid_slug]) }}" class="text-muted">
                                                            {{ $batch->raid_name }}
                                                            <span class="js-timestamp small" data-timestamp="{{ $batch->raid_date }}" data-format="MMM D"></span>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($batch->raid_group_id)
                                                    <li class="list-inline-item text-muted">
                                                        <a href="{{ route('guild.raidGroup.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $batch->raid_group_id]) }}" class="text-muted">
                                                            @include('partials/raidGroup', ['raidGroupName' => $batch->raid_group_name, 'raidGroupColor' => getHexColorFromDec($batch->raid_group_color)])
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li class="p-3 text-warning">
                                No results found
                            </li>
                        @endif
                    </ol>
                </div>

                <div class="col-12 mt-3">
                    {{ $batches->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var guild = {!! $guild->toJson() !!};

    $("input[type='date']").change(function () {
        updateUrl($(this).prop("name"), $(this).val());
    });

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
                    url: "/api/items/query/" + guild.expansion_id + "/" + request.term,
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
