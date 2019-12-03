@extends('layouts.app')
@section('title', $item->name . ' - ' . config('app.name'))


@section('content')
<div class="container-fluid">
    <div class="col-12">
        <h2>
            @include('partials/item', ['wowheadLink' => true])
        </h2>
    </div>
    <div class="col-12">
        <h3>Has It</h3>
        <ul>
            @if ($item->receivedUsers->count() > 0)
                @foreach ($item->receivedUsers as $user)
                    <li class="lead font-weight-bold">
                        <a href="{{ route('showUser', ['id' => $user->id, 'username' => $user->username]) }}">{{ $user->name }}</a>
                    </li>
                @endforeach
            @else
                <li class="lead">
                    <em>nobody?</em>
                </li>
            @endif
        </ul>
    </div>

    <div class="col-12">
        <h3>Wants It</h3>
        <ul>
            @if ($item->wishlistUsers->count() > 0)
                @foreach ($item->wishlistUsers as $user)
                    <li class="lead font-weight-bold">
                        <a href="{{ route('showUser', ['id' => $user->id, 'username' => $user->username]) }}">{{ $user->username }}</a>
                    </li>
                @endforeach
            @else
                <li class="lead">
                    <em>nobody?</em>
                </li>
            @endif
        </ul>
    </div>
</div>
@endsection
