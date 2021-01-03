@extends('layouts.app')
@section('title', 'Raid Groups - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-helmet-battle text-dk"></span>
                        Raid Groups
                    </h1>
                </div>
                <div class="col-12 pt-3 pb-1 mb-2 bg-light rounded">
                    @if ($guild->allRaids->count() > 0)
                        <ol class="no-bullet no-indent striped">
                            @foreach ($guild->allRaids as $raid)
                                <li class="p-3 mb-3 rounded">
                                    <span class="role-circle" style="{{ $raid->role ? 'background-color:' . $raid->role->getColor() : '' }}" title="{{ $raid->role ? $raid->role->getColor() : ''}}"></span>
                                    <span class="font-weight-bold text-danger">{{ $raid->disabled_at ? 'DISABLED' : '' }}</span>
                                    <span title="{{ $raid->slug }}">{{ $raid->name }}</span>
                                    <small class="text-muted">
                                        @if ($raid->role)
                                            - <span title="Discord Role: {{ $raid->role->discord_id }}">{{ $raid->role->name }}</span>
                                        @endif
                                    </small>
                                    <ul class="list-inline">
                                        <li class="list-inline-item">
                                            <a href="{{ route('guild.raid.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raid->id]) }}">
                                                <span class="fas fa-fw fa-pencil"></span>edit
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $raid->id]) }}">
                                                <span class="fas fa-fw fa-clipboard-list-check"></span>logs
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <form class="form-inline" role="form" method="POST" action="{{ route('guild.raid.toggleDisable', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                                {{ csrf_field() }}
                                                <input hidden name="id" value="{{ $raid->id }}">
                                                <input hidden type="checkbox" name="disabled_at" value="1" class="" autocomplete="off"
                                                    {{ $raid->disabled_at ? '' : 'checked' }}>
                                                <button type="submit"
                                                    class="btn btn-link text-{{ $raid->disabled_at ? 'success' : 'danger' }} p-0 m-0 ml-3"
                                                    title="{{ $raid->disabled_at ? 'Raid is shown in dropdowns again' : 'Raid is no longer shown in dropdowns. Characters already assigned to this raid will remain assigned to it.' }}">
                                                    <span class="fas fa-fw fa-{{ $raid->disabled_at ? 'trash-undo' : 'trash' }}"></span>{{ $raid->disabled_at ? 'enable' : 'disable' }}
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @endforeach
                        </ol>
                    @else
                    <p class="text-4">
                        No raid groups yet
                    </p>
                    @endif
                </div>
                <div class="col-12 mt-3">
                    <a href="{{ route('guild.raid.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" class="btn btn-success">
                        <span class="fas fa-fw fa-plus"></span> Create New Raid
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
