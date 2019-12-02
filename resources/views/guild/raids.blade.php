@extends('layouts.app')
@section('title', 'Raids - ' . config('app.name'))


@section('content')
<div class="container-fluid">
    <div class="col-12">
        Current raids:
        <ol class="no-bullet">
            @foreach ($raids as $raid)
                <li>
                    <span class="role-circle" style="background-color:{{ $raid->role->getColor() }}"></span>
                    {{ $raid->name }}
                    <small class="text-muted">
                        - <span title="Discord Role ID">{{ $raid->discord_role_id }}</span>
                        - <span title="slug">{{ $raid->slug }}</span>
                        - <span title="Discord Channel ID">{{ $raid->discord_channel_id }}</span>
                    </small>
                </li>
            @endforeach
        </ol>
    </div>
</div>
@endsection
