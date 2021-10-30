@extends('layouts.app')
@section('title', 'Admin Users - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-users text-gold"></span>
                        Users
                        @include('admin/partials/nav')
                    </h1>
                    <div class="text-5 text-danger">
                        <span class="font-weight-bold">DANGER! DO NOT</span> modify anything in guilds that you do not belong to. That isn't supported yet and will break many things.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.users.list') }}">
        <div class="row">
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
                    <label for="order_by" class="font-weight-bold">
                        <span class="fas fa-fw fa-sort text-muted"></span>
                        Sort By
                    </label>

                    <select name="order_by" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                        <option value="" data-tokens="">
                            Discord Username
                        </option>
                        <option value="username"
                            data-tokens="username"
                            {{ Request::get('order_by') && Request::get('order_by') == 'username' ? 'selected' : ''}}>
                            Username
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
            {{ $users->appends(request()->input())->links() }}
        </div>
        <div class="col-12 pr-0 pl-0">
            <div class="col-12 pb-3 pr-2 pl-2 rounded">
                <table id="users" class="table table-border table-hover table-striped stripe">
                    <thead>
                        <tr>
                            <th>
                                <span class="fab fa-fw fa-discord text-discord"></span>
                                Discord Username
                            </th>
                            <th>
                                <span class="fas fa-fw fa-user text-muted"></span>
                                Username
                            </th>
                            <th>
                                <span class="fas fa-fw fa-id-card-alt text-muted"></span>
                                Discord ID

                            </th>
                            <th>
                                <span class="fas fa-fw fa-language text-muted"></span>
                                Locale
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <ul class="no-bullet no-indent">
                                        <li class="">
                                            <a class="text- font-weight-bold" href="{{ route('admin.users.edit.show', ['userId' => $user->id]) }}">
                                                {{ $user->discord_username }}
                                            </a>
                                        </li>
                                        @if ($user->ads_disabled_at)
                                            <li class="small text-warning">
                                                ADS DISABLED
                                            </li>
                                        @endif
                                        @if ($user->banned_at)
                                            <li class="small text-danger">
                                                BANNED
                                            </li>
                                        @endif
                                    </ul>
                                </td>
                                <td>
                                    <ul class="no-bullet no-indent">
                                        <li class="">
                                            {{ $user->username }}
                                        </li>
                                    </ul>
                                </td>
                                <td class="">
                                    <ul class="no-bullet no-indent">
                                        <li>
                                            {{ $user->discord_id }}
                                        </li>
                                    </ul>
                                </td>
                                <td class="">
                                    <ul class="no-bullet no-indent">
                                        <li class="">
                                            {{ $user->locale }}
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                        @php
                        // Clear variable
                        $user = null;
                        @endphp
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12 mt-3">
            {{ $users->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $("#users").DataTable({
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
