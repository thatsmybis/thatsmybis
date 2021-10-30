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
                        Edit User
                        @include('admin/partials/nav')
                    </h1>
                    <div class="text-5 text-danger">
                        <span class="font-weight-bold">DANGER! DO NOT</span> modify anything in guilds that you do not belong to. That isn't supported yet and will break many things.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 rounded bg-light">
            <ul class="no-bullet no-indent">
                <li class="">
                    <span class="fas fa-fw fa-id-card text-muted"></span>
                    <span class="text- font-weight-bold">{{ $user->id }}</span>
                    &sdot; <span class="small text-muted">ID</span>
                </li>
                <li class="">
                    <span class="fab fa-fw fa-discord text-discord"></span>
                    <a class="text- font-weight-bold" href="{{ route('admin.users.edit.show', ['userId' => $user->id]) }}">
                        <span class="text- font-weight-bold">{{ $user->discord_username }}</span>
                    </a>
                    &sdot; <span class="small text-muted">Discord Username</span>
                </li>
                @if ($user->banned_at)
                    <li class="small text-danger">
                        <span class="font-weight-bold">BANNED</span>
                    </li>
                @endif
                @if ($user->ads_disabled_at)
                    <li class="small text-warning">
                        <span class="font-weight-bold">ADS DISABLED</span>
                    </li>
                @endif
                <li class="">
                    <span class="fas fa-fw fa-user text-muted"></span>
                    <span class="text- font-weight-bold">{{ $user->username }}</span>
                    &sdot; <span class="small text-muted">Username</span>
                </li>
                <li>
                    <span class="fas fa-fw fa-id-card-alt text-muted"></span>
                    <span class="text- font-weight-bold">{{ $user->discord_id }}</span>
                    &sdot; <span class="small text-muted">Discord ID</span>
                </li>
                <li class="">
                    <span class="fas fa-fw fa-language text-muted"></span>
                    <span class="text- font-weight-bold">{{ $user->locale }}</span>
                    &sdot; <span class="small text-muted">Locale</span>
                </li>
                <li class="">
                    <span class="fas fa-fw fa-users-crown text-muted"></span>
                    <span class="small text-muted">Guilds</span>
                    <ul>
                        @foreach ($user->members as $member)
                            <li>
                                <a href="{{ route('member.show', ['guildId' => $member->guild->id, 'guildSlug' => $member->guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]) }}"
                                    class="text-{{ getExpansionColor($member->guild->expansion_id) }} font-weight-medium">
                                    &lt;{{ $member->guild->name }}&gt;
                                </a>
                                &sdot; <span class="small text-muted">{{ $member->guild->id }}</span>
                                <span class="text-danger">{{ $member->guild->disabled_at ? 'DISABLED' : '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.users.edit.submit', ['userId' => $user->id]) }}">
        <div class="row bg-light rounded">
            {{ csrf_field() }}
            <input hidden name="id" value="{{ $user->id }}" />

            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="ads_disabled_at" value="1" class="" autocomplete="off"
                            {{ old('ads_disabled_at') && old('ads_disabled_at') == 1 ? 'checked' : ($user->ads_disabled_at ? 'checked' : '') }}>
                            Disable ads
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="banned_at" value="1" class="" autocomplete="off"
                            {{ old('banned_at') && old('banned_at') == 1 ? 'checked' : ($user->banned_at ? 'checked' : '') }}>
                            Banned
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Update</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
