@extends('layouts.app')
@section('title', __('Permissions') . ' - ' . config('app.name'))


@section('content')
<div class="container-fluid">
    <div class="col-12">
        {{ __("Current website permissions:") }}
        <ul class="no-bullet">
            @foreach ($permissions as $permission)
                <li class="mb-2">
                    <strong>{{ $permission->name }}</strong>
                    <small class="">- {{ $permission->description }} <span class="text-muted">{{ null }}</small>
                    <br>
                    <small class="text-muted">
                        <ul class="no-bullet">
                            @foreach ($permission->roles as $role)
                                <li>
                                    <span class="role-circle" style="background-color:{{ $role->getColor() }}"></span>
                                    {{ $role->name }}
                                </li>
                            @endforeach
                        </ul>
                    </small>
                </li>
            @endforeach
        </ul>
        <a class="btn btn-danger" href="{{ route('guild.addPermissions', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" onclick="return confirm("{{__("Are you sure? This only needs to be run once or when a developer is ready to update the permissions.") }}')">
            {{ __("(danger) Load Permissions") }}
        </a>
    </div>
</div>
@endsection
