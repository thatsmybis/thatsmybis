@extends('layouts.app')
@section('title',  'Members - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row pt-2 mb-3">
        <div class="col-12 pr-0 pl-0">
            <div class="col-12 pb-3 pr-2 pl-2 rounded">
                <table id="members" class="col-12 table table-border table-hover stripe">
                    <thead>
                        <tr>
                            <th>
                                <span class="fas fa-fw fa-user text-muted"></span>
                                Member
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
                        @if ($unassignedCharacters->count() > 0)
                            <tr>
                                <td>
                                    <span class="font-weight-bold text-danger">
                                        Unassigned
                                    </span>
                                </td>
                                <td>
                                    <ul class="list-inline">
                                        @foreach($unassignedCharacters->sortBy('name') as $character)
                                            @include('member/partials/listMemberCharacter')
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    —
                                </td>
                                <td>
                                    —
                                </td>
                            </tr>
                        @endif
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
                                            @include('member/partials/listMemberCharacter')
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
                                                    <span>
                                                        {{ $role->name }}
                                                    </span>
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
        "columns" : [
            null,
            { "orderable" : false },
            { "orderable" : false },
            { "orderable" : false },
        ]
    });
});
</script>
@endsection

