@extends('layouts.app')
@section('title', 'Item Prios For ' . $instance-> name . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-wight-medium font-blizz">
                        <span class="fas fa-fw fa-dungeon text-muted"></span>
                        {{ $instance->name }} Prios
                    </h1>
                </div>
                <div class="col-12 pt-3 pb-1 mb-2 bg-light rounded">
                    <div class="text-3 font-weight-medium ml-4 mb-3">
                        Choose a raid
                    </div>
                    @if ($guild->raids->count() > 0)
                        <ol class="no-bullet no-indent striped">
                            @foreach ($guild->raids as $raid)
                                <li class="p-3 mb-3 rounded">
                                    <a href="{{ route('guild.prios.massInput', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => $instance->slug, 'raidId' => $raid->id]) }}" class="tag text-4">
                                        <span class="role-circle-large" style="{{ $raid->role ? 'background-color:' . $raid->role->getColor() : '' }}" title="{{ $raid->role ? $raid->role->getColor() : ''}}"></span>
                                        <span class="font-weight-bold text-danger">{{ $raid->disabled_at ? 'DISABLED' : '' }}</span>
                                        <span title="{{ $raid->slug }}">{{ $raid->name }}</span>
                                        <span class="fas fa-fw fa-arrow-alt-right text-success"></span>
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    @else
                    <p class="text-4">
                        No raid groups yet
                        <a href="{{ route('guild.raid.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" class="btn btn-success">
                            <span class="fas fa-fw fa-plus"></span> Create New Raid
                        </a>
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
