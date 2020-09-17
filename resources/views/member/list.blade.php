@extends('layouts.app')
@section('title',  'Members - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row pt-2 mb-3">
        <div class="col-12 pr-0 pl-0">
            <div class="col-12 pb-3 pr-2 pl-2 rounded">
                <table id="members" class="col-xs-12 table table-border table-hover stripe">
                    <thead>
                        <tr>
                            <th>
                                <span class="fas fa-fw fa-user text-muted"></span>
                                Name
                            </th>
                            <th>
                                <span class="fas fa-fw fa-users text-muted"></span>
                                Characters
                            </th>
                            <th>
                                <span class="fas fa-fw fa-comment-alt-lines text-muted"></span>
                                Notes
                            </th>
                            <th>
                                Roles (<abbr title="these get updated when the user loads a page and may be cached for up to several minutes between page loads">cached</abbr>)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guild->members as $member)
                            <tr>
                                <td>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle font-weight-bold text-white" id="member{{ $member->id }}Dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ $member->username }}
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="member{{ $member->id }}Dropdown">
                                            <a class="dropdown-item" href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]) }}" target="_blank">
                                                Profile
                                            </a>
                                            <a class="dropdown-item" href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'member_id' => $member->id]) }}" target="_blank">
                                                Logs
                                            </a>
                                            <a class="dropdown-item" href="{{ route('member.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]) }}" target="_blank">
                                                Edit
                                            </a>
                                        </div>
                                    </div>
                                    <span class="text-discord">
                                        {{ $member->user->discord_username }}
                                    </span>
                                </td>
                                <td>
                                    <ul class="list-inline">
                                        @foreach($member->characters->sortBy('name') as $character)
                                            @php
                                                $raid = ($character->raid_id ? $guild->raids->where('id', $character->raid_id)->first() : null);
                                            @endphp
                                            <li class="list-inline-item text-{{ $character->inactive_at ? 'muted' : strtolower($character->class) }}">
                                                <div class="dropdown">
                                                    <a class="dropdown-toggle" id="character{{ $character->id }}Dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <span class="role-circle" style="background-color:{{ $raid ? $raid->getColor() : null }}"></span>
                                                        {{ $character->name }}
                                                    </a>
                                                    <div class="dropdown-menu" aria-labelledby="character{{ $character->id }}Dropdown">
                                                        <a class="dropdown-item" href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}" target="_blank">
                                                            Profile
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'character_id' => $character->id]) }}" target="_blank">
                                                            Logs
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('character.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}" target="_blank">
                                                            Edit
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}" target="_blank">
                                                            Loot
                                                        </a>
                                                        <span class="dropdown-item disabled">
                                                            @if ($raid)
                                                                @include('partials/raid', ['raidColor' => $raid->getColor()])
                                                            @else
                                                                no raid
                                                            @endif
                                                        </span>
                                                        @if ($character->inactive_at)
                                                            <span class="dropdown-item disabled font-weight-bold text-danger">
                                                                INACTIVE
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <div>
                                        {{ $member->public_note ? $member->public_note : '—' }}
                                    </div>
                                    @if ($showOfficerNote)
                                        <div>
                                            <span class="font-weight-bold small font-underline">Officer's note</span>
                                            <br>
                                            <em>{{ $member->officer_note ? $member->officer_note : '—' }}</em>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <ul class="list-inline">
                                        @foreach($member->roles->sortBy('name') as $role)
                                            <li class="list-inline-item">
                                                @if (in_array($role->discord_id, [$guild->admin_role_id, $guild->gm_role_id, $guild->officer_role_id, $guild->raid_leader_role_id, $guild->class_leader_role_id]))
                                                    <span class="font-weight-bold text-gold">
                                                        {{ $role->name }}
                                                    </span>
                                                @elseif(in_array($role->discord_id, $guild->getMemberRoleIds()))
                                                    <span class="font-weight-bold">
                                                        {{ $role->name }}
                                                    </span>
                                                @else
                                                    {{ $role->name }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $("#members").DataTable({
        "order"  : [], // Disable initial auto-sort; relies on server-side sorting
        "paging" : false,
    });
});
</script>
<script src="{{ env('APP_ENV') == 'local' ? asset('/js/roster.js') : mix('js/processed/roster.js') }}"></script>
@endsection

