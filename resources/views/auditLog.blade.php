@extends('layouts.app')
@section('title', __("Audit Log") . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-clipboard-list-check text-gold"></span>
                        {{ __("Audit Log") }}
                    </h1>
                    <ul>
                        <li class="small no-bullet font-italic">
                        {{ __("Whodunit?") }}
                        </li>
                        @if (!$showPrios)
                            <li class="small text-danger">
                        {{ __("Prios are hidden by your guild master(s)") }}
                            </li>
                        @elseif ($guild->is_prio_private)
                            <li class="small text-warning">
                                {{ __("Prios are hidden from raiders") }}
                            </li>
                        @endif
                        @if (!$showWishlist)
                            <li class="small text-danger">
                                {{ __("Wishlists are hidden by your guild master(s)") }}
                            </li>
                        @elseif ($guild->is_wishlist_private)
                            <li class="small text-warning">
                                {{ __("Wishlists are hidden from raiders") }}
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="row">
                @if ($resources)
                    <div class="col-12 mb-3 text-5">
                        {{ __("Filter:") }}
                        <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            {{ __("reset") }}
                        </a>
                    </div>
                    <div class="col-12 pb-3 d-flex flex-wrap">
                        @foreach ($resources as $resource)
                            <div>
                                <div class="bg-light rounded pt-1 pb-1 pl-3 pr-3 mr-3 mb-3">
                                    @if($resource instanceof \App\Batch)
                                        @if ($resource->name)
                                            {{ $resource->name }}
                                        @else
                                            {{ __("Batch") }} {{ $resource->id }}
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
                            {{ __("Min Date") }}
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
                            {{ __("Max Date") }}
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
                            {{ __("Character") }}
                        </label>
                        <select name="character_id" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                            <option value="">
                                —
                            </option>
                            @foreach ($guild->characters as $character)
                                <option value="{{ $character->id }}"
                                        data-tokens="{{ $character->id }}" class="text-{{ slug($character->class) }}-important"
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
                            {{ __("Member") }}
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
                            {{ __("Raid Group") }}
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
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="form-group">
                        <label for="type" class="font-weight-bold">
                            <span class="fas fa-fw fa-hat-wizard text-muted"></span>
                            {{ __("Class") }}
                        </label>
                        <select name="character_class" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                            <option value="" data-tokens="">
                                —
                            </option>
                            @foreach (App\Character::classes($guild->expansion_id) as $key => $class)
                                <option value="{{ $key }}" class="text-{{ strtolower($key) }}-important"
                                    {{ Request::get('character_class') && Request::get('character_class') == $key ? 'selected' : '' }}>
                                    {{ $class }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="form-group">
                        <label for="type" class="font-weight-bold">
                            <span class="fas fa-fw fa-scroll-old text-muted"></span>
                            {{ __("Loot Type") }}
                        </label>
                        <select name="type" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                            <option value="" data-tokens="">
                                —
                            </option>
                            <option value="{{ \App\Item::TYPE_PRIO }}"
                                data-tokens="{{ \App\Item::TYPE_PRIO }}"
                                {{ Request::get('type') && Request::get('type') == \App\Item::TYPE_PRIO ? 'selected' : ''}}>
                                {{ __("Prio") }}
                            </option>
                            <option value="{{ \App\Item::TYPE_RECIPE }}"
                                data-tokens="{{ \App\Item::TYPE_RECIPE }}"
                                {{ Request::get('type') && Request::get('type') == \App\Item::TYPE_RECIPE ? 'selected' : ''}}>
                                {{ __("Recipe") }}
                            </option>
                            <option value="{{ \App\Item::TYPE_WISHLIST }}"
                                data-tokens="{{ \App\Item::TYPE_WISHLIST }}"
                                {{ Request::get('type') && Request::get('type') == \App\Item::TYPE_WISHLIST ? 'selected' : ''}}>
                                {{ __("Wishlist") }}
                            </option>
                            <option value="{{ 'received_all' }}"
                                data-tokens="received_all"
                                {{ Request::get('type') && Request::get('type') == 'received_all' ? 'selected' : ''}}>
                                {{ __("Received (all)") }}
                            </option>
                            <option value="{{ \App\AuditLog::TYPE_ASSIGN }}"
                                data-tokens="{{ \App\AuditLog::TYPE_ASSIGN }}"
                                {{ Request::get('type') && Request::get('type') == \App\AuditLog::TYPE_ASSIGN ? 'selected' : ''}}>
                                {{ __("Received (via assign loot page)") }}
                            </option>
                            <option value="{{ \App\Item::TYPE_RECEIVED }}"
                                data-tokens="{{ \App\Item::TYPE_RECEIVED }}"
                                {{ Request::get('type') && Request::get('type') == \App\Item::TYPE_RECEIVED ? 'selected' : ''}}>
                                {{ __("Received (via character loot page)") }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="form-group">
                        <label for="item_instance_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-dungeon text-muted"></span>
                            {{ __("Item Dungeon") }}
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
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="form-group">
                        <label for="item_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-sack text-muted"></span>
                            {{ __("Item") }}
                        </label>
                        <input name="item_id" maxlength="40" data-max-length="40" type="text" placeholder="{{ __('type an item name') }}" autocomplete="off" class="js-item-autocomplete js-input-text form-control dark">
                        <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;
                    </div>
                </div>
            </div>

            <div class="row">
                @if ($logs->count())
                    <div class="col-12 mb-3">
                        <ul class="list-inline">
                            <li class="list-inline-item">
                                <span id="download_csv" class="btn btn-success">
                                    <span class="fas fa-fw fa-file-export"></span>
                                    {{ __("Download CSV") }}
                                </span>
                            </li>
                            <li class="list-inline-item">
                                <a id="view_csv" class="text-link cursor-pointer">
                                    <span class="fas fa-fw fas fa-file-csv"></span>
                                    {{ __("View CSV") }}
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
                <div class="col-12">
                    <ol class="no-bullet no-indent striped">
                        @if ($logs->count())
                            @foreach ($logs as $log)
                                <li class="p-1 pl-3 rounded">
                                    <div class="d-flex flex-row">
                                        <div class="list-timestamp text-right text-muted p-2 small">
                                            @if ($log->member_id)
                                                <a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $log->member_id, 'usernameSlug' => $log->member_slug]) }}" class="text-muted">
                                                    <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $log->created_at }}"></span> {{ __("ago") }}
                                                </a>
                                            @else
                                                <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $log->created_at }}"></span> {{ __("ago") }}
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
                                                    <li class="list-inline-item text-muted">
                                                        &sdot;
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $log->member_id, 'usernameSlug' => $log->other_member_slug]) }}" class="text-muted">
                                                            {{ $log->other_member_username }}
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($log->character_id)
                                                    <li class="list-inline-item text-muted">
                                                        &sdot;
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <a href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $log->character_id, 'nameSlug' => $log->character_slug]) }}" class="text-{{ slug($log->character_class) }}">
                                                            {{ $log->character_name }}
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($log->instance_id)
                                                    <li class="list-inline-item text-muted">
                                                        &sdot;
                                                    </li>
                                                    <li class="list-inline-item text-muted">
                                                        {{ $log->instance_name }}
                                                    </li>
                                                @endif
                                                @if ($log->item_source_id)
                                                    <li class="list-inline-item text-muted">
                                                        &sdot;
                                                    </li>
                                                    <li class="list-inline-item text-muted">
                                                        {{ $log->item_source_name }}
                                                    </li>
                                                @endif
                                                @if ($log->raid_id)
                                                    <li class="list-inline-item text-muted">
                                                        &sdot;
                                                    </li>
                                                    <li class="list-inline-item text-muted">
                                                        <a href="{{ route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $log->raid_id, 'raidSlug' => $log->raid_slug]) }}" class="text-muted">
                                                            {{ $log->raid_name }}
                                                            <span class="js-timestamp small" data-timestamp="{{ $log->raid_date }}" data-format="MMM D"></span>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($log->raid_group_id)
                                                    <li class="list-inline-item text-muted">
                                                        &sdot;
                                                    </li>
                                                    <li class="list-inline-item text-muted">
                                                        <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_group_id' => $log->raid_group_id]) }}" class="text-muted">
                                                            {{ $log->raid_group_name }}
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($log->role_id)
                                                    <li class="list-inline-item text-muted">
                                                        &sdot;
                                                    </li>
                                                    <li class="list-inline-item text-muted">
                                                        {{ $log->role_name }}
                                                    </li>
                                                @endif

                                                @if ($log->batch_id)
                                                    <li class="list-inline-item text-muted">
                                                        &sdot;
                                                    </li>
                                                    <li class="list-inline-item text-muted">
                                                        <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'batch_id' => $log->batch_id]) }}" class="small text-muted">
                                                            {{ $log->batch_name ? $log->batch_name : 'Batch ' . $log->batch_id }}
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
                                {{ __("No results found") }}
                            </li>
                        @endif
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

    // THIS IS A HACK: See autocomplete.js
    var updateUrlOnItemAutocompleteSelect = true;

    $("input[type='date']").change(function () {
        updateUrl($(this).prop("name"), $(this).val());
    });

    $("select").change(function () {
        updateUrl($(this).prop("name"), $(this).val());
    });

    $("#download_csv").click(function () {
        let url = new URL(location);
        url.searchParams.set('export', 1);
        url.searchParams.set('rows', 500);
        window.open(url, '_blank').focus();
    });

    $("#view_csv").click(function (e) {
        e.preventDefault();
        let url = new URL(location);
        url.searchParams.set('export', 1);
        url.searchParams.set('view', 1);
        url.searchParams.set('rows', 500);
        window.open(url, '_blank').focus();
    });

    // Updates the URL with the given parameter and value, then reloads the page
    function updateUrl(paramName, paramValue) {
        let url = new URL(location);
        url.searchParams.set(paramName, paramValue);
        url.searchParams.delete('page');
        location = url;
    }
</script>
@endsection
