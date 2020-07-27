@extends('layouts.app')
@section('title', 'Discord Roles - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-wight-medium">
                        <span class="fab fa-fw fa-discord text-discord"></span>
                        Discord Roles
                    </h1>
                    <p>
                        When we see a new role attached to a member, we'll sync this list
                    </p>
                    <p>
                        If you want to manually trigger an update, hit the button down at the bottom
                        <br>
                        This will update colors, names, order, add new roles, and remove old ones
                    </p>
                </div>

                <div class="col-12">
                    <ol class="no-bullet no-indent striped">
                        @foreach ($guild->roles->sortByDesc('position') as $role)
                            <li class="p-1 pl-3 rounded" title="Discord ID:{{ $role->discord_id }}">
                                <span class="role-circle" style="background-color:{{ $role->getColor() }}"></span>
                                {{ $role->name }}
                                <small class="text-muted">
                                    - <span title="Discord Position (higher is better)">{{ $role->position }}</span>
                                </small>
                            </li>
                        @endforeach
                    </ol>
                </div>

                <div class="col-xs-12 mt-3">
                    <a class="btn btn-success" href="{{ route('guild.syncRoles', ['guildSlug' => $guild->slug]) }}">
                        <span class="fas fa-fw fa-sync"></span>
                        Sync Roles
                    </a>
                    <br>
                    <small class="text-muted">
                        Fetches roles from your Discord server and adds them to the list of usable roles on here
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
