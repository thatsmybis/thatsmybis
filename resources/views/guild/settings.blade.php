@extends('layouts.app')
@section('title', __("Guild Settings") . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-users-crown text-gold"></span>
                        {{ __("Guild Settings") }}
                    </h1>
                </div>
            </div>
            @if (count($errors) > 0)
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            @endif
            <form id="editForm" class="form-horizontal" role="form" method="POST" action="{{ route('guild.submitSettings', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-12 pt-2 pb-0 mb-3 bg-light rounded">
                        <!-- Expansion -->
                        <div class="row">
                            <div class="col-md-8 col-12">
                                <div class="form-group">
                                    <ul class="no-bullet no-indent">
                                        @foreach ($expansions as $expansion)
                                            @php
                                                $matchingGuild = $guild->guilds->where('expansion_id', $expansion->id)->first();
                                            @endphp
                                            <li>
                                                @if ($expansion->id == $guild->expansion_id)
                                                    <span class="text-{{ getExpansionColor($guild->expansion_id) }} font-weight-bold">
                                                        <span class="fab fa-fw fa-battle-net text-muted"></span>
                                                        {{ $expansion->name_long }}
                                                    </span>
                                                @elseif ($matchingGuild)
                                                    <a href="{{ route('guild.settings', ['guildId' => $matchingGuild->id, 'guildSlug' => $matchingGuild->slug]) }}"
                                                        class="text-{{ getExpansionColor($matchingGuild->expansion_id) }}">
                                                        <span class="fab fa-fw fa- text-muted"></span>
                                                        {{ $expansion->name_long }}
                                                        <span class="text-muted font-italic small">{{ __("owned by") }} <span class="text-discord">{{ $matchingGuild->user->discord_username }}</span></span>
                                                    </a>
                                                @else
                                                    <span class="text-muted">
                                                        <span class="fab fa-fw fa- text-muted"></span>
                                                        {{ $expansion->name_long }}
                                                    </span>
                                                @endif

                                                @if (!$expansion->is_enabled)
                                                    <span class="text-muted font-italic small">{{ __("not yet supported") }}</span>
                                                @elseif (!$matchingGuild)
                                                    <a href="{{ route('guild.showRegisterExpansion', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'expansionSlug' => $expansion->slug]) }}" class="font-italic small">
                                                        {{ __("add expansion") }}
                                                    </a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 pt-2 pb-0 mb-3 bg-light rounded">

                        <div class="row">
                            <div class="col-md-8 col-12">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">
                                        <span class="fas fa-fw fa-users text-muted"></span>
                                        {{ __("Guild Name") }}
                                    </label>
                                    <input name="name" maxlength="36" type="text" class="form-control dark" placeholder="must be unique" value="{{ old('name') ? old('name') : $guild->name }}" />
                                </div>
                            </div>
                        </div>

                        <!-- Guild Owner -->
                        <div class="row">
                            <div class="col-md-8 col-12">
                                <div class="form-group">
                                    <label for="discord_id" class="font-weight-normal">
                                        <span class="fas fa-fw fa-user-crown text-muted"></span>
                                        <span class="text-muted">{{ __("Guild Owner") }}</span>
                                        @if (request()->get('isGuildAdmin'))
                                            <a href="{{ route('guild.owner', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">{{ __("change") }}</a>
                                        @endif
                                    </label>
                                    <div class="font-italic">
                                        <span class="text-discord">
                                            {{ $guild->user->discord_username }}
                                        </span>
                                        <a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $owner->id, 'usernameSlug' => $owner->slug]) }}" class="text-muted">
                                            ({{ $guild->user->username }})
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Discord ID -->
                        <div class="row">
                            <div class="col-md-8 col-12">
                                <div class="form-group">
                                    <label for="discord_id" class="font-weight-normal">
                                        <span class="text-muted">{{ __("Discord Server ID") }}</span>
                                        <a href="{{ route('guild.changeDiscord', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">{{ __("change") }}</a>
                                        <small class="text-warning font-italic">
                                            {{ __("useful for starting over") }}
                                        </small>
                                    </label>
                                    <input disabled
                                        name="discord_id"
                                        maxlength="255"
                                        type="text"
                                        class="form-control dark"
                                        placeholder="paste your guild's Discord ID here"
                                        value="{{ old('discord_id') ? old('discord_id') : $guild->discord_id }}" />
                                </div>
                            </div>
                        </div>

                        <!-- Disable Guild -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="disabled_at" value="1" class="" autocomplete="off"
                                                {{ old('disabled_at') && old('disabled_at') == 1 ? 'checked' : ($guild->disabled_at ? 'checked' : '') }}>
                                                {{ __("Disable guild") }} <span class="text-muted small">{{ __("members will only be shown the guild name and MOTD") }} - <strong>{{ __("can") }}</strong> {{ __("be undone") }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Yes, this calendar feature is functional. It's been cut to minimize clutter.
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="calendar_link" class="font-weight-normal">
                                        <span class="fas fa-fw fa-calendar-alt text-muted"></span>
                                        Google Calendar Link <small class="text-muted">optional <a href="{{ route('faq') }}#google-calendar">what's this?</a></small>
                                    </label>

                                    <input
                                        name="calendar_link"
                                        maxlength="255"
                                        type="text"
                                        class="form-control dark"
                                        placeholder="paste the calendar's public URL"
                                        value="{{ old('calendar_link') ? old('calendar_link') : $guild->calendar_link }}" />
                                    <small class="text-muted">
                                        Calendar settings > Integrate calendar > Public URL to this calendar
                                    </small>
                                </div>
                            </div>
                        </div>
                        -->

                        <!-- MOTD -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="show_message" value="1" class="" autocomplete="off"
                                                {{ old('message') && old('message') == 1 ? 'checked' : ($guild->message ? 'checked' : '') }}>
                                                {{ __("Show Message of the Day and/or Rules") }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12" id="message" style="{{ $guild->message ? '' : 'display:none;' }}">
                                <div class="form-group">
                                    <label for="message" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-megaphone"></span>
                                        {{ __("Message Of The Day") }}
                                        <small class="text-muted">{{ __("your guildmates will see this") }}</small>
                                    </label>
                                    <textarea maxlength="500" data-max-length="500" name="message" rows="2" placeholder="a short message about what's happening and/or some rules" class="form-control dark">{{ old('message') ? old('message') : $guild->message }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 mt-3 mb-2">
                        <h2 class="font-weight-medium">
                            <span class="fas fa-fw fa-ballot-check text-muted"></span>
                            {{ __("Settings") }}
                        </h2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="form-group mb-0">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_received_locked" value="1" class="" autocomplete="off"
                                        {{ old('is_received_locked') && old('is_received_locked') == 1 ? 'checked' : ($guild->is_received_locked ? 'checked' : '') }}>
                                        {{ __("Lock loot received") }}
                                        <span class="text-muted small">
                                            {{ __("raid leader and above can still edit") }}
                                        </span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_raid_group_locked" value="1" class="" autocomplete="off"
                                        {{ old('is_raid_group_locked') && old('is_raid_group_locked') == 1 ? 'checked' : ($guild->is_raid_group_locked ? 'checked' : '') }}>
                                        {{ __("Don't let members set their raid group") }}
                                        <span class="text-muted small">
                                            {{ __("raid leader and above can still edit") }}
                                        </span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_attendance_hidden" value="1" class="" autocomplete="off"
                                        {{ old('is_attendance_hidden') && old('is_attendance_hidden') == 1 ? 'checked' : ($guild->is_attendance_hidden ? 'checked' : '') }}>
                                        {{ __("Don't show attendance") }}
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="attendance_decay_days">
                                    {{ __("Attendance decay rate") }}
                                    <span class="small text-muted">
                                        {{ __("how far back to count attendance") }}
                                    </span>
                                </label>
                                @php
                                    $rates = [
                                        31  => '1 month',
                                        61  => '2 months',
                                        91  => '3 months',
                                        122 => '4 months',
                                        152 => '5 months',
                                        183 => '6 months',
                                        213 => '7 months',
                                        243 => '8 months',
                                        274 => '9 months',
                                        304 => '10 months',
                                        335 => '11 months',
                                        365 => '1 year',
                                        456 => '1 ¼ years',
                                        548 => '1 ½ years',
                                        639 => '1 ¾ years',
                                        730 => '2 years',
                                    ];
                                @endphp
                                <select name="attendance_decay_days" class="form-control dark">
                                    <option value="" {{ old('attendance_decay_days') && old('attendance_decay_days') == 36500 || $guild->attendance_decay_days == 36500 ? 'selected' : '' }}>
                                        {{ __("No limit") }}
                                    </option>
                                    @foreach ($rates as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('attendance_decay_days') && old('attendance_decay_days') == $key || $guild->attendance_decay_days == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tier_mode">
                                <span class="fas fa-fw fa-trophy text-muted"></span>
                                {{ __("Tier mode") }}
                                <span class="small text-muted">
                                    {{ __("rank each item in the Item Notes admin dropdown menu") }}
                                </span>
                            </label>
                            <select name="tier_mode" class="form-control dark">
                                <option value="s"
                                    {{ old('tier_mode') && old('tier_mode') == 's' || $guild->tier_mode == 's' ? 'selected' : '' }}>
                                    {{ __("S-tier") }}
                                </option>
                                <option value="num"
                                    {{ old('tier_mode') && old('tier_mode') == 'num' || $guild->tier_mode == 'num' ? 'selected' : '' }}>
                                    {{ __("Numbered") }}
                                </option>
                                <option value="" {{ old('tier_mode') && old('tier_mode') == '' || $guild->tier_mode == '' ? 'selected' : '' }}>
                                    {{ __("Off") }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 mt-3 mb-2">
                        <h2 class="font-weight-medium">
                            <span class="fas fa-fw fa-scroll-old text-legendary"></span>
                            {{ __("Wishlists") }}
                        </h2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="form-group mb-0">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_wishlist_locked" value="1" class="" autocomplete="off"
                                        {{ old('is_wishlist_locked') && old('is_wishlist_locked') == 1 ? 'checked' : ($guild->is_wishlist_locked ? 'checked' : '') }}>
                                        {{ __("Lock wishlists") }}
                                        <span class="text-muted small">
                                            {{ __("raid leader and above can still edit") }}
                                        </span>
                                </label>
                            </div>
                            <div id="wishlistLockedExceptions" class="ml-4 mb-1" style="display:none;">
                                <div class="mb-1 font-weight-bold">
                                    {{ __("unlock specific wishlists") }}
                                </div>
                                @php
                                    $wishlistLockedExceptions = $guild->getWishlistLockedExceptions();
                                @endphp
                                @for ($i = 1; $i <= App\Http\Controllers\CharacterLootController::MAX_WISHLIST_LISTS; $i++)
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="wishlist_locked_exceptions[{{ $i }}]" value="1" class="" autocomplete="off"
                                                {{ old('wishlist_locked_exceptions.' . $i) && old('wishlist_locked_exceptions.' . $i) == 1 ? 'checked' : (in_array($i, $wishlistLockedExceptions) ? 'checked' : '') }}>
                                                {{ __("wishlist") }} {{ $i }}
                                        </label>
                                    </div>
                                @endfor
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_wishlist_private" value="1" class="" autocomplete="off"
                                        {{ old('is_wishlist_private') && old('is_wishlist_private') == 1 ? 'checked' : ($guild->is_wishlist_private ? 'checked' : '') }}>
                                        {{ __("Limit") }} <strong>{{ __("wishlist visibility") }}</strong> {{ __("to Raid Leaders") }}
                                        <span class="text-muted small">
                                            {{ __("members can still see <em>their own</em> characters' wishlists") }}
                                        </span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_wishlist_autopurged" value="1" class="" autocomplete="off"
                                        {{ old('is_wishlist_autopurged') && old('is_wishlist_autopurged') == 1 ? 'checked' : ($guild->is_wishlist_autopurged ? 'checked' : '') }}>
                                        {{ __("By default, delete items from wishlists when they are distributed") }}
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="do_sort_items_by_instance" value="1" class="" autocomplete="off"
                                        {{ old('do_sort_items_by_instance') && old('do_sort_items_by_instance') == 1 ? 'checked' : ($guild->do_sort_items_by_instance ? 'checked' : '') }}>
                                        {{ __("Sort wishlists by dungeon") }}
                                        <span class="text-muted small">
                                            {{ __("default is to sort by user's priority") }}
                                        </span>
                                        <!--
                                            Q: Why is this a guild setting and not a user setting?
                                            A: So that everyone in the guild sees the same thing, avoiding inconsistencies and mistakes.
                                        -->
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_wishlist_disabled" value="1" class="" autocomplete="off"
                                        {{ old('is_wishlist_disabled') && old('is_wishlist_disabled') == 1 ? 'checked' : ($guild->is_wishlist_disabled ? 'checked' : '') }}>
                                        {{ __("Disable wishlists") }}
                                        <span class="text-muted small">
                                            {{ __("if your guild doesn't use them") }}
                                        </span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="current_wishlist_number" class="">
                                    {{ __("Current Wishlist") }}
                                    <small class="text-muted">
                                        {{ __("which wishlist should be used throughout the site?") }}
                                        {{ __("(useful for phases)") }}
                                    </small>
                                </label>
                                <input name="current_wishlist_number"
                                    min="1"
                                    max="{{ App\Http\Controllers\CharacterLootController::MAX_WISHLIST_LISTS }}"
                                    type="number"
                                    class="form-control dark"
                                    placeholder="{{ App\Http\Controllers\CharacterLootController::MAX_WISHLIST_LISTS }}"
                                    value="{{ old('current_wishlist_number') ? old('current_wishlist_number') : $guild->current_wishlist_number }}" />
                            </div>
                            <div class="form-group">
                                <label for="max_wishlist_items" class="">
                                    {{ __("Max Wishlist Items") }}
                                    <small class="text-muted">{{ __("won't affect existing wishlists until they're resubmitted") }}</small>
                                </label>
                                <input name="max_wishlist_items"
                                    min="0"
                                    max="{{ App\Http\Controllers\CharacterLootController::MAX_WISHLIST_ITEMS }}"
                                    type="number"
                                    class="form-control dark"
                                    placeholder="{{ App\Http\Controllers\CharacterLootController::MAX_WISHLIST_ITEMS }}"
                                    value="{{ old('max_wishlist_items') ? old('max_wishlist_items') : $guild->max_wishlist_items }}" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 mt-3 mb-2">
                        <h2 class="font-weight-medium">
                            <span class="fas fa-fw fa-sort-amount-down text-gold"></span>
                            Prios
                        </h2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="form-group mb-0">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_prio_private" value="1" class="" autocomplete="off"
                                        {{ old('is_prio_private') && old('is_prio_private') == 1 ? 'checked' : ($guild->is_prio_private ? 'checked' : '') }}>
                                        {{ __("Limit") }} <strong>{{ __("prio visibility") }}</strong> {{ __("to Raid Leaders") }}
                                        <span class="text-muted small">
                                            {{ __("character prios are hidden, but prio notes on items are still visible") }}
                                        </span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_prio_autopurged" value="1" class="" autocomplete="off"
                                        {{ old('is_prio_autopurged') && old('is_prio_autopurged') == 1 ? 'checked' : ($guild->is_prio_autopurged ? 'checked' : '') }}>
                                        {{ __("By default, delete items from prio lists when they are distributed") }}
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_prio_disabled" value="1" class="" autocomplete="off"
                                        {{ old('is_prio_disabled') && old('is_prio_disabled') == 1 ? 'checked' : ($guild->is_prio_disabled ? 'checked' : '') }}>
                                        {{ __("Disable prios") }}
                                        <span class="text-muted small">
                                            {{ __("if your guild doesn't use them") }}
                                        </span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="prio_show_count" class="">
                                    {{ __("Prio ranks to show") }}
                                    <span class="text-muted small">
                                        {{ __("eg. only show top 3 prios to raiders") }}
                                    </span>
                                </label>
                                <input name="prio_show_count" min="1" max="{{ App\Http\Controllers\PrioController::MAX_PRIOS }}" type="number" class="form-control dark" placeholder="{{ App\Http\Controllers\PrioController::MAX_PRIOS }}" value="{{ old('prio_show_count') ? old('prio_show_count') : $guild->prio_show_count }}" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 mt-3 mb-2">
                        <h2 class="font-weight-medium">
                            <span class="fas fa-fw fa-key text-success"></span>
                            {{ __("Permissions") }}
                        </h2>
                        <span class="text-muted">
                            {{ __("Based on user roles in your Discord server") }}
                        </span>
                        <div class="small text-muted mb-3">
                            {{ __("Not seeing all of your roles?") }}
                            <a href="{{ route('guild.roles', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}#role-whitelisting">{{ __("sync roles") }}</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="row">
                            <div class="col-12">
                                <label for="gm_role_id" class="font-weight-bold">
                                    <span class="fas fa-fw fa-crown text-gold"></span>
                                    {{ __("Guild Master Role") }}
                                </label>
                            </div>
                            <div class="col-md-6 col-sm-8 col-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <select name="gm_role_id" class="form-control dark">
                                            <option value="" selected>
                                                —
                                            </option>

                                            @foreach ($guild->roles as $role)
                                                <option value="{{ $role->discord_id }}"
                                                    style="color:{{ $role->getColor() }};"
                                                    {{ old('gm_role_id') && old('gm_role_id') == $role->discord_id ? 'selected' : ($guild->gm_role_id == $role->discord_id ? 'selected' : '') }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="small text-muted mb-3">
                                    <ul>
                                        @foreach ($permissions->whereIn('role_note', ['guild_master', 'officer', 'raid_leader']) as $permission)
                                            <li>
                                                {{ $permission->description }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="row">
                            <div class="col-12">
                                <label for="officer_role_id" class="font-weight-bold">
                                    <span class="fas fa-fw fa-gavel text-legendary"></span>
                                    {{ __("Officer Role") }}
                                </label>
                            </div>
                            <div class="col-md-6 col-sm-8 col-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <select name="officer_role_id" class="form-control dark">
                                            <option value="" selected>
                                                —
                                            </option>

                                            @foreach ($guild->roles as $role)
                                                <option value="{{ $role->discord_id }}"
                                                    style="color:{{ $role->getColor() }};"
                                                    {{ old('officer_role_id') && old('officer_role_id') == $role->discord_id ? 'selected' : ($guild->officer_role_id == $role->discord_id ? 'selected' : '') }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="small text-muted mb-3">
                                    <ul>
                                        @foreach ($permissions->whereIn('role_note', ['officer', 'raid_leader']) as $permission)
                                            <li>
                                                {{ $permission->description }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="row">
                            <div class="col-12">
                                <label for="raid_leader_role_id" class="font-weight-bold">
                                    <span class="fas fa-fw fa-helmet-battle text-dk"></span>
                                    {{ __("Raid Leader Role") }}
                                </label>
                            </div>
                            <div class="col-md-6 col-sm-8 col-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <select name="raid_leader_role_id" class="form-control dark">
                                            <option value="" selected>
                                                —
                                            </option>

                                            @foreach ($guild->roles as $role)
                                                <option value="{{ $role->discord_id }}"
                                                    style="color:{{ $role->getColor() }};"
                                                    {{ old('raid_leader_role_id') && old('raid_leader_role_id') == $role->discord_id ? 'selected' : ($guild->raid_leader_role_id == $role->discord_id ? 'selected' : '') }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="small text-muted mb-3">
                                    <ul>
                                        @foreach ($permissions->where('role_note', 'raid_leader') as $permission)
                                            <li>
                                                {{ $permission->description }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="row">
                            <div class="col-12">
                                <label for="auditor_role_id" class="font-weight-bold">
                                    <span class="fas fa-fw fa-glasses text-success"></span>
                                    {{ __("Auditor Role") }}
                                    <span class="small text-muted font-weight-normal">{{ __("if you have wishlist/prio visibility locked") }}</span>
                                </label>
                            </div>
                            <div class="col-md-6 col-sm-8 col-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <select name="auditor_role_id" class="form-control dark">
                                            <option value="" selected>
                                                —
                                            </option>

                                            @foreach ($guild->roles as $role)
                                                <option value="{{ $role->discord_id }}"
                                                    style="color:{{ $role->getColor() }};"
                                                    {{ old('auditor_role_id') && old('auditor_role_id') == $role->discord_id ? 'selected' : ($guild->auditor_role_id == $role->discord_id ? 'selected' : '') }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="small text-muted mb-3">
                                    <ul>
                                        @foreach ($permissions->where('role_note', 'auditor') as $permission)
                                            <li>
                                                {{ $permission->description }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="row">
                            <div class="col-12">
                                <label for="member_roles" class="font-weight-bold">
                                    <span class="fas fa-fw fa-swords text-success"></span>
                                    {{ __("Members") }}
                                    <br>
                                    <small class="text-muted">
                                        <!-- I don't know if this is true...
                                        If this is empty, anyone on the Discord server can join this guild
                                        <br> -->
                                        {{ __("Discord users with") }} <strong>{{ __("any") }}</strong> {{ __("of these roles are allowed to join") }}
                                    </small>
                                </label>
                            </div>
                            <div class="col-md-6 col-sm-8 col-12">
                                <div class="form-group">
                                    @php
                                        $memberRoleIds = $guild->getMemberRoleIds();
                                        $memberRoleLength = count($memberRoleIds) - 1;
                                    @endphp

                                    @for ($i = 0; $i < 12; $i++)
                                        <div class="form-group {{ $i > 1 ? 'js-hide-empty' : '' }}" style="{{ $i > 1 ? 'display:none;' : '' }}">
                                            <select name="member_roles[]" class="form-control dark {{ $i > 0 ? 'js-show-next' : '' }}">
                                                <option value="" selected>
                                                    —
                                                </option>

                                                @foreach ($guild->roles as $role)
                                                    <option value="{{ $role->discord_id }}"
                                                        style="color:{{ $role->getColor() }};"
                                                        {{ old('member_roles.' . $i) ? (old('member_roles.' . $i) == $role->discord_id ? 'selected' : '') : ($memberRoleLength >= $i && $memberRoleIds[$i] == $role->discord_id ? 'selected' : '') }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button class="btn btn-success">
                        <span class="fas fa-fw fa-save"></span>
                        {{ __("Save") }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ loadScript('guildSettings.js') }}"></script>
<script>
    $(document).ready(() => warnBeforeLeaving("#editForm"));
</script>
@endsection
