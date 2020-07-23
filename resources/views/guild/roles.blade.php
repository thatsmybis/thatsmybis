@extends('layouts.app')
@section('title', 'Roles - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-wight-medium">
                        <span class="fab fa-fw fa-discord text-muted"></span>
                        Discord Roles
                    </h1>
                    <p>
                        If you've added or changed roles on your Discord server, consider syncing them
                    </p>
                </div>

                <div class="col-12">
                    <ol class="no-bullet no-indent striped">
                        @foreach ($guild->roles->sortByDesc('position') as $role)
                            <li class="p-1 pl-3 rounded" title="pos:{{ $role->position}} id:{{ $role->id }} disc-id:{{ $role->discord_id }} slug:{{ $role->slug}}">
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
                    <a class="btn btn-success" href="{{ route('guild.syncRoles', ['guildSlug' => $guild->slug]) }}" onclick="return confirm('Are you sure? If a role has been deleted and then recreated on Discord, this could break this website until a dev can fix it.')">
                        <span class="fas fa-fw fa-sync"></span>
                        Sync Roles
                    </a>
                    <br>
                    <small class="text-muted">
                        Fetches roles from the Discord server and adds them to the list of usable roles on the website
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
