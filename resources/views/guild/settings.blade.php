@extends('layouts.app')
@section('title', "Guild Settings - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-wight-medium">
                        <span class="fas fa-fw fa-users-crown text-gold"></span>
                        Guild Settings
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
            <form class="form-horizontal" role="form" method="POST" action="{{ route('guild.submitSettings', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-12 pt-2 pb-0 mb-3 bg-light rounded">

                        <div class="row">
                            <div class="col-md-8 col-12">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">
                                        <span class="fas fa-fw fa-users text-muted"></span>
                                        Guild Name
                                    </label>
                                    <input name="name" maxlength="36" type="text" class="form-control dark" placeholder="must be unique" value="{{ old('name') ? old('name') : $guild->name }}" />
                                    <span class="text-muted small">
                                        Changing this can break existing links to the guild
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 col-12">
                                <div class="form-group">
                                    <label for="discord_id" class="font-weight-normal">
                                        <span class="text-muted">Discord ID</span>
                                        <small class="text-muted">
                                            locked
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

                        <div class="row">
                            <div class="col-md-8 col-12">
                                <div class="form-group">
                                    <label for="expansion">
                                        <span class="text-muted">Expansion</span>
                                        <small class="text-muted">
                                            locked
                                        </small>
                                    </label>
                                    <select disabled name="expansion" class="form-control dark">
                                        <option value="1" selected>
                                            Classic
                                        </option>
                                        <option value="2" {{ old('expansion') && old('expansion') == 2 ? 'selected' : '' }}>
                                            Burning Crusade
                                        </option>
                                        <option value="3" {{ old('expansion') && old('expansion') == 3 ? 'selected' : '' }}>
                                            Wrath of the Lich King
                                        </option>
                                    </select>
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

                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="form-group mb-0">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_wishlist_private" value="1" class="" autocomplete="off"
                                        {{ old('is_wishlist_private') && old('is_wishlist_private') == 1 ? 'checked' : ($guild->is_wishlist_private ? 'checked' : '') }}>
                                        Limit <strong>wishlist visibility</strong> to Raid Leaders
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_prio_private" value="1" class="" autocomplete="off"
                                        {{ old('is_prio_private') && old('is_prio_private') == 1 ? 'checked' : ($guild->is_prio_private ? 'checked' : '') }}>
                                        Limit <strong>prio visibility</strong> to Raid Leaders <small>(Prio Notes remain visible to raiders; only assigned characters are hidden)</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="form-group mb-0">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_received_locked" value="1" class="" autocomplete="off"
                                        {{ old('is_received_locked') && old('is_received_locked') == 1 ? 'checked' : ($guild->is_received_locked ? 'checked' : '') }}>
                                        Lock loot received <small>(Raid Leader and above can still edit)</small>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_wishlist_locked" value="1" class="" autocomplete="off"
                                        {{ old('is_wishlist_locked') && old('is_wishlist_locked') == 1 ? 'checked' : ($guild->is_wishlist_locked ? 'checked' : '') }}>
                                        Lock wishlists <small>(Raid Leader and above can still edit)</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="form-group mb-0">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_prio_autopurged" value="1" class="" autocomplete="off"
                                        {{ old('is_prio_autopurged') && old('is_prio_autopurged') == 1 ? 'checked' : ($guild->is_prio_autopurged ? 'checked' : '') }}>
                                        By default, delete items from prio lists when they are distributed
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_wishlist_autopurged" value="1" class="" autocomplete="off"
                                        {{ old('is_wishlist_autopurged') && old('is_wishlist_autopurged') == 1 ? 'checked' : ($guild->is_wishlist_autopurged ? 'checked' : '') }}>
                                        By default, delete items from wishlists when they are distributed
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="row">
                            <div class="col-12">
                                <label for="gm_role_id" class="font-weight-bold">
                                    <span class="fas fa-fw fa-crown text-gold"></span>
                                    Guild Master Role
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
                                    Officer Role
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
                                    Raid Leader Role
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
                                <label for="member_roles" class="font-weight-bold">
                                    <span class="fas fa-fw fa-swords text-success"></span>
                                    Raiders
                                    <br>
                                    <small class="text-muted">
                                        Discord users with <strong>any</strong> of these roles are allowed to join
                                        <br>
                                        Guild Masters, Officers, and Raid Leaders must also have at least one of these roles
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
                        Save
                    </button>
                    <div class="small text-muted mb-3">
                        Not seeing all of your roles?
                        <a href="{{ route('guild.roles', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}#role-whitelisting">sync roles</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ env('APP_ENV') == 'local' ? asset('/js/guildSettings.js') : mix('js/processed/guildSettings.js') }}"></script>
@endsection
