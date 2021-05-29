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
                    <span class="text-muted">
                        If you don't want the concept of "main raiders", use <strong>other</strong> and don't use "main" at all
                    </span>
                </div>
                <div class="col-12 mt-3 mb-3">
                    <a href="{{ route('guild.raidGroup.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" class="btn btn-success">
                        <span class="fas fa-fw fa-plus"></span> Create
                    </a>
                </div>
                <div class="col-12 pt-3 pb-1 mb-2 bg-light rounded">
                    @if ($guild->allRaidGroups->count() > 0)
                        <ol class="no-bullet no-indent striped">
                            @foreach ($guild->allRaidGroups as $raidGroup)
                                <li class="p-3 mb-3 rounded">
                                    <ul class="list-inline">
                                        <li class="list-inline-item">
                                            <span class="role-circle" style="{{ $raidGroup->role ? 'background-color:' . $raidGroup->role->getColor() : '' }}" title="{{ $raidGroup->role ? $raidGroup->role->getColor() : ''}}"></span>
                                            <span class="font-weight-bold text-danger">{{ $raidGroup->disabled_at ? 'ARCHIVED' : '' }}</span>
                                            <span class="font-weight-medium" title="{{ $raidGroup->slug }}">{{ $raidGroup->name }}</span>
                                        </li>
                                        @if ($raidGroup->role)
                                            <li class="list-inline-item text-muted small">
                                                &sdot;
                                            </li>
                                            <li class="list-inline-item text-muted small">
                                                <span title="Discord Role: {{ $raidGroup->role->discord_id }}">{{ $raidGroup->role->name }}</span>
                                            </li>
                                        @endif
                                        <li class="list-inline-item text-muted small">
                                            &sdot;
                                        </li>
                                        <li class="list-inline-item text-muted small">
                                            <a href="{{ route('guild.raidGroup.characters', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id]) }}">
                                                {{ $raidGroup->characters_count }} main raider{{ $raidGroup->characters_count != 1 ? 's' : '' }}
                                            </a>
                                        </li>
                                        <li class="list-inline-item text-muted small">
                                            &sdot;
                                        </li>
                                        <li class="list-inline-item text-muted small">
                                            <a href="{{ route('guild.raidGroup.secondaryCharacters', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id]) }}">
                                                {{ $raidGroup->secondary_characters_count }} other raider{{ $raidGroup->secondary_characters_count != 1 ? 's' : '' }}
                                            </a>
                                        </li>
                                    </ul>
                                    <ul class="list-inline">
                                        <li class="list-inline-item">
                                            <a href="{{ route('guild.raidGroup.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id]) }}">
                                                <span class="fas fa-fw fa-pencil"></span>edit
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_group_id' => $raidGroup->id]) }}">
                                                <span class="fas fa-fw fa-clipboard-list-check"></span>history
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <form class="form-inline" role="form" method="POST" action="{{ route('guild.raidGroup.toggleDisable', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                                {{ csrf_field() }}
                                                <input hidden name="id" value="{{ $raidGroup->id }}">
                                                <input hidden type="checkbox" name="disabled_at" value="1" class="" autocomplete="off"
                                                    {{ $raidGroup->disabled_at ? '' : 'checked' }}>
                                                <button type="submit"
                                                    class="btn btn-link text-{{ $raidGroup->disabled_at ? 'success' : 'danger' }} p-0 m-0 ml-3"
                                                    title="{{ $raidGroup->disabled_at ? 'Raid Group is shown in dropdowns again' : 'Raid Group is no longer shown in dropdowns. Characters already assigned to this raid group will remain assigned to it.' }}">
                                                    <span class="fas fa-fw fa-{{ $raidGroup->disabled_at ? 'trash-undo' : 'trash' }}"></span>{{ $raidGroup->disabled_at ? 'unarchive' : 'archive' }}
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
            </div>
        </div>
    </div>
</div>
@endsection
