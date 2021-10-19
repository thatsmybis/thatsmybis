@extends('layouts.app')
@section('title',  __('Members') . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row pt-2 mb-3">
        <div class="col-12 pr-0 pl-0">
            <div class="col-12 pb-3 pr-2 pl-2 rounded">
                <table id="members" class="table table-border table-hover stripe">
                    <thead>
                        <tr>
                            <th>
                                <span class="fas fa-fw fa-user text-muted"></span>
                                {{ __("Member") }} <span class="text-muted small">({{ $guild->allMembers->whereNull('inactive_at')->whereNull('banned_at')->count() }})</span>
                            </th>
                            <th>
                                <span class="fas fa-fw fa-users text-muted"></span>
                                {{ __("Characters") }}
                            </th>
                            <th>
                                <span class="fas fa-fw fa-comment-alt-lines text-muted"></span>
                                {{ __("Notes") }}
                            </th>
                            <th>
                                {{ __("Roles") }} (<abbr title="these get updated when the user loads a page and may be cached for up to several minutes between page loads">{{ __("cached") }}</abbr>)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($unassignedCharacters->whereNull('inactive_at')->count() > 0)
                            <tr class="{{ request()->input('unclaimed') ? 'highlight bg-lightest' : '' }}">
                                <td>
                                    <span class="font-weight-bold text-danger">
                                        {{ __("Unclaimed") }} <span class="text-muted small">({{ $unassignedCharacters->whereNull('inactive_at')->count() }})</span>
                                    </span>
                                    <br>
                                    <span class="text-muted small">
                                        {{ __("Contact an officer to have a character assigned to you") }}
                                    </span>
                                </td>
                                <td>
                                    <ul class="list-inline">
                                        @foreach($unassignedCharacters->whereNull('inactive_at')->sortBy('name') as $character)
                                            @include('member/partials/listMemberCharacter', ['tag' => 1])
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>
                        @endif
                        @foreach($guild->allMembers->whereNull('inactive_at')->whereNull('banned_at') as $member)
                            <tr>
                                <td>
                                    @php
                                        $benchedCount = $member->characters->sum(function ($character) {
                                            return $character->benched_count;
                                        });
                                        $raidCount = $member->characters->sum(function ($character) {
                                            return $character->raid_count;
                                        });
                                        $raidsAttended = $member->characters->where('raid_count', '>', 0)->sum(function ($character) {
                                            return $character->raid_count * $character->attendance_percentage;
                                        });
                                        $attendancePercentage = $raidCount > 0 ? ($raidsAttended / $raidCount) : 100;
                                    @endphp
                                    @include('member/partials/listMember', [ 'attendancePercentage' => $attendancePercentage, 'raidCount' => $raidCount, 'tag' => true, 'maxWidth' => true])
                                </td>
                                <td>
                                    <ul class="list-inline">
                                        @foreach($member->characters->sortBy('name') as $character)
                                            @include('member/partials/listMemberCharacter', ['tag' => 1])
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <div>
                                        <span class="js-markdown-inline">{{ $member->public_note ? $member->public_note : '—' }}</span>
                                    </div>
                                    @if ($showOfficerNote && $member->officer_note)
                                        <div>
                                            <span class="font-weight-bold small font-italic text-gold">{{ __("Officer's Note") }}</span>
                                            <br>
                                            <span class="js-markdown-inline">{{ $member->officer_note }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <ul class="list-inline">
                                        @foreach($member->roles->sortBy('name') as $role)
                                            @if (in_array($role->discord_id, [$guild->admin_role_id, $guild->gm_role_id, $guild->officer_role_id, $guild->raid_leader_role_id, $guild->class_leader_role_id]))
                                                <li class="list-inline-item">
                                                    <span class="font-weight-bold text-gold">
                                                        {{ $role->name }}
                                                    </span>
                                                </li>
                                            @elseif(in_array($role->discord_id, $guild->getMemberRoleIds()))
                                                <li class="list-inline-item">
                                                    <span>
                                                        {{ $role->name }}
                                                    </span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                        @if ($guild->allMembers->whereNotNull('inactive_at')->count() > 0)
                            <tr>
                                <td>
                                    <span class="font-weight-bold text-muted">
                                        {{ __("Archived Members") }}
                                    </span>
                                    <br>
                                    <span id="showInactiveMembers" class="small font-italic cursor-pointer">
                                        {{ __("click to show") }}
                                    </span>
                                </td>
                                <td>
                                    <ul id="inactiveMembers" class="list-inline" style="display:none;">
                                        @foreach($guild->allMembers->whereNotNull('inactive_at') as $member)
                                            @include('member/partials/listMember', ['attendancePercentage' => null, 'raidCount' => null])
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
                        @if ($unassignedCharacters->whereNotNull('inactive_at')->count() > 0)
                            <tr>
                                <td>
                                    <span class="font-weight-bold text-muted">
                                        {{ __("Archived Characters") }}
                                    </span>
                                    <br>
                                    <span id="showOrphanCharacters" class="small font-italic cursor-pointer">
                                        {{ __("click to show") }}
                                    </span>
                                </td>
                                <td>
                                    <ul id="orphanCharacters" class="list-inline" style="display:none;">
                                        @foreach($unassignedCharacters->whereNotNull('inactive_at') as $character)
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
        order  : [], // Disable initial auto-sort; relies on server-side sorting
        paging : false,
        fixedHeader : true, // Header row sticks to top of window when scrolling down
        oLanguage: {
            sSearch: "<abbr title='{{ __('Fuzzy searching is ON. To search exact text, wrap your search in \"quotes\"') }}'>{{ __('Search') }}</abbr>"
        },
        columns : [
            null,
            { orderable : false },
            { orderable : false },
            { orderable : false },
        ]
    });

    $("#showInactiveMembers").click(function () {
        $("#inactiveMembers").toggle();
    });

    $("#showOrphanCharacters").click(function () {
        $("#orphanCharacters").toggle();
    });
});
</script>
@endsection

