@extends('layouts.app')
@section('title', 'Admin Guilds - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-users-crown text-gold"></span>
                        Guilds
                        @include('admin/partials/nav')
                    </h1>
                    <div class="text-5 text-danger">
                        <span class="font-weight-bold">DANGER! DO NOT</span> modify anything in guilds that you do not belong to. That isn't supported yet and will break many things.
                    </div>
                </div>
            </div>

            <!--
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="form-group">
                        <label for="item_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-signature"></span>
                            Name
                        </label>
                        <input name="name" maxlength="40" data-max-length="40" type="text" placeholder="type a a guild name" class="js-name-lookup form-control">
                    </div>
                </div>
            </div>
            -->
        </div>
    </div>

    <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.guilds') }}">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <label for="guild_name" class="font-weight-bold">
                        <span class="fas fa-fw fa-users-crown text-muted"></span>
                        Guild Name
                    </label>
                    <input name="guild_name"
                        value="{{ Request::get('guild_name') ? Request::get('guild_name') : ''}}"
                        placeholder=""
                        class="form-control dark">
                </div>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <label for="discord_username" class="font-weight-bold">
                        <span class="fab fa-fw fa-discord text-muted"></span>
                        Discord Username
                    </label>
                    <input name="discord_username"
                        value="{{ Request::get('discord_username') ? Request::get('discord_username') : ''}}"
                        placeholder=""
                        class="form-control dark">
                </div>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <label for="member_name" class="font-weight-bold">
                        <span class="fas fa-fw fa-user text-muted"></span>
                        Member Name
                    </label>
                    <input name="member_name"
                        value="{{ Request::get('member_name') ? Request::get('member_name') : ''}}"
                        placeholder=""
                        class="form-control dark">
                </div>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <label for="character_name" class="font-weight-bold">
                        <span class="fas fa-fw fa-users text-muted"></span>
                        Char Name
                    </label>
                    <input name="character_name"
                        value="{{ Request::get('character_name') ? Request::get('character_name') : ''}}"
                        placeholder=""
                        class="form-control dark">
                </div>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <label for="order_by" class="font-weight-bold">
                        <span class="fas fa-fw fa-sort text-muted"></span>
                        Sort By
                    </label>

                    <select name="order_by" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                        <option value="" data-tokens="">
                            Guild Name
                        </option>
                        <option value="member_count"
                            data-tokens="member_count"
                            {{ Request::get('order_by') && Request::get('order_by') == 'member_count' ? 'selected' : ''}}>
                            Member Count
                        </option>
                    </select>
                </div>
            </div>

            {{ csrf_field() }}

            <div class="col-12">
                <div class="form-group">
                    <button class="btn btn-success"><span class="fas fa-fw fa-search"></span> Query</button>
                </div>
            </div>
        </div>
    </form>

    <div class="row pt-2 mb-3">
        <div class="col-12 mt-3">
            {{ $guilds->appends(request()->input())->links() }}
        </div>
        <div class="col-12 pr-0 pl-0">
            <div class="col-12 pb-3 pr-2 pl-2 rounded">
                <table id="guilds" class="table table-border table-hover stripe">
                    <thead>
                        <tr>
                            <th>
                                <span class="fas fa-fw fa-users-crown text-muted"></span>
                                Guild
                            </th>
                            <th>
                                <span class="fas fa-fw fa-object-group text-muted"></span>
                                Objects
                            </th>
                            <th>
                                <span class="fas fa-fw fa-list text-muted"></span>
                                Config
                            </th>
                            <th>
                                <span class="fas fa-fw fa-sack text-muted"></span>
                                Loot
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guilds as $guild)
                            <tr>
                                <td>
                                    <ul class="no-bullet no-indent">
                                        <li class="">
                                            <a class="text-{{ getExpansionColor($guild->expansion_id) }} font-weight-bold" href="{{ route('guild.settings', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                                {{ $guild->name }}
                                            </a>
                                        </li>
                                        <li class="smaller text-muted">
                                            {{ $guild->discord_id }}
                                        </li>
                                        @if ($guild->disabled_at)
                                            <li class="small text-danger">
                                                DISABLED
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $guild->member_id, 'usernameSlug' => $guild->member_username]) }}"
                                                class="text-discord">
                                                {{ $guild->discord_username }}
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <ul class="no-bullet no-indent">
                                        <li class="">
                                            {{ $guild->character_count }} <span class="text-muted small">characters</span>
                                        </li>
                                        <li class="">
                                            {{ $guild->member_count }} <span class="text-muted small">members</span>
                                        </li>
                                        <li class="">
                                            {{ $guild->raid_group_count }} <span class="text-muted small">raid groups</span>
                                        </li>
                                    </ul>
                                </td>
                                <td class="">
                                    <ul class="no-bullet no-indent">
                                        <li class="">
                                            {{ $guild->tier_mode ? $guild->tier_mode : 'no' }} <span class="text-muted small">tier</span>
                                        </li>
                                        @if ($guild->is_prio_private)
                                            <li class="">
                                                <span class="text-muted small">private prios</span>
                                            </li>
                                        @endif
                                        @if ($guild->is_received_locked)
                                            <li class="">
                                                <span class="text-muted small">locked received</span>
                                            </li>
                                        @endif
                                        @if ($guild->is_wishlist_private)
                                            <li class="">
                                                <span class="text-muted small">private wishlist</span>
                                            </li>
                                        @endif
                                        @if ($guild->is_wishlist_locked)
                                            <li class="">
                                                <span class="text-muted small">locked wishlist</span>
                                            </li>
                                        @endif
                                        @if ($guild->is_prio_autopurged)
                                            <li class="">
                                                <span class="text-muted small">autopurged prio</span>
                                            </li>
                                        @endif
                                        @if ($guild->is_wishlist_autopurged)
                                            <li class="">
                                                <span class="text-muted small">autopurged wishlist</span>
                                            </li>
                                        @endif
                                        @if ($guild->do_sort_items_by_instance)
                                            <li class="">
                                                <span class="text-muted small">instance item sort</span>
                                            </li>
                                        @endif
                                    </ul>
                                </td>
                                <td class="">
                                    <ul class="no-bullet no-indent">
                                        <!--
                                        <li>
                                            {{ $guild->wishlist_item_count }} <span class="text-muted small">wishlist items</span>
                                        </li>
                                        <li>
                                            {{ $guild->prio_item_count }} <span class="text-muted small">prio items</span>
                                        </li>
                                        <li>
                                            {{ $guild->received_item_count }} <span class="text-muted small">received items</span>
                                        </li>
                                        <li>
                                            {{ $guild->batch_item_count }} <span class="text-muted small">batch items</span>
                                        </li>
                                        <li>
                                            {{ $guild->batch_count }} <span class="text-muted small">batches</span>
                                        </li>
                                        -->
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                        @php
                        // Clear variable
                        $guild = null;
                        @endphp
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12 mt-3">
            {{ $guilds->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $("#guilds").DataTable({
        order  : [], // Disable initial auto-sort; relies on server-side sorting
        paging : false,
        fixedHeader : true, // Header row sticks to top of window when scrolling down
        columns : [
            { orderable : false },
            { orderable : false },
            { orderable : false },
            { orderable : false },
        ]
    });
});
</script>
@endsection
