@extends('layouts.app')
@section('title', 'Roles - ' . config('app.name'))


@section('content')
<div class="container-fluid">
    <div class="col-12">
        Current roles:
        <ul class="no-bullet">
            @foreach ($roles as $role)
                <li>
                    <span class="role-circle" style="background-color:{{ $role->getColor() }}"></span>
                    {{ $role->name }}
                    <small class="text-muted">- <span title="Discord ID">{{ $role->discord_id }}</span> - <span title="Website ID">{{ $role->id }}</span></small>
                </li>
            @endforeach
        </ul>

        <a class="btn btn-danger" href="{{ route('guild.syncRoles') }}" onclick="return confirm('Are you sure? If a role has been deleted and then recreated on Discord, this could break this website until a dev can fix it.')">
            (danger) Sync Roles
        </a>
    </div>
</div>
@endsection
