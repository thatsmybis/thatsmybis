@extends('layouts.app')
@section('title', __('Discord Roles') . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fab fa-fw fa-discord text-discord"></span>
                        {{ __("Discord Roles") }}
                    </h1>
                    <p>
                        {{ __("When we see a new role attached to a member, we'll automatically sync this list.") }}
                    </p>
                    <p>
                        {{ __("Roles listed on offline members are") }} <strong>{{ __("cached") }}</strong>.
                        <br>
                        {{ __("They will be properly updated the next time that member logs in.") }}
                    </p>
                    <p>
                        {{ __("If you want to manually trigger an update, hit the button down at the bottom.") }}
                        <br>
                        {{ __("This will update colors, names, order, add new roles, and remove old ones.") }}
                        <br>
                        {{ __("It") }} <strong>{{ __("will not") }}</strong> {{ __("sync the") }} <strong>{{ __("display onl") }}y</strong> {{ __("roles listed alongside offline members.") }}
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

                <div class="col-12 mt-3">
                    <a class="btn btn-success" href="{{ route('guild.syncRoles', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                        <span class="fas fa-fw fa-sync"></span>
                        {{ __("Sync Roles") }}
                    </a>
                    <br>
                    <small class="text-muted">
                        {{ __("Fetches roles from your Discord server and adds them to the list of usable roles on here") }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
