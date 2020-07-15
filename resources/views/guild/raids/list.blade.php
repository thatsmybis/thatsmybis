@extends('layouts.app')
@section('title', 'Raids - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 mb-3">
                    <h4>Raids</h4>
                </div>
                <div class="col-12">
                    @if ($guild->raids->count() > 0)
                        <ol class="no-bullet">
                            @foreach ($guild->raids as $raid)
                                <li>
                                    <span class="role-circle" style="{{ $raid->role ? 'background-color:' . $raid->role->getColor() : '' }}" title="{{ $raid->role ? $raid->role->getColor() : ''}}"></span>
                                    <span title="{{ $raid->slug }}">{{ $raid->name }}</span>
                                    <small class="text-muted">
                                        @if ($raid->role)
                                            - <span title="Discord Role: {{ $raid->role->discord_id }}">{{ $raid->role->name }}</span>
                                        @endif
                                    </small>
                                    <ul class="list-inline">
                                        <li class="list-inline-item">
                                            <a href="{{ route('guild.editRaid', ['guildSlug' => $guild->slug, 'id' => $raid->id]) }}">
                                                <span class="fas fa-fw fa-pencil"></span>
                                                edit
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <form class="form-inline" role="form" method="POST" action="{{ route('guild.removeRaid', ['guildSlug' => $guild->slug]) }}">
                                                {{ csrf_field() }}
                                                <input hidden name="id" value="{{ $raid->id }}">
                                                <button type="submit" class="btn btn-link text-danger p-0 m-0 pl-3" onclick="return confirm('PERMANENTLY remove raid? Cannot be undone.')">
                                                    <span class="fas fa-fw fa-trash"></span>
                                                    remove
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @endforeach
                        </ol>
                    @else
                    <p class="text-4">
                        No raids yet
                    </p>
                    @endif
                </div>
                <div class="col-12 mt-3">
                    <a href="{{ route('guild.editRaid', ['guildSlug' => $guild->slug]) }}" class="btn btn-success">
                        <span class="fas fa-fw fa-plus"></span> Create New Raid
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
