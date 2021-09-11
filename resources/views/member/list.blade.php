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
                        @if ($unassignedCharacters->count() > 0)
                            <tr>
                                <td>
                                    <span class="font-weight-bold text-danger">
                                        {{ __("Unassigned") }} <span class="text-muted small">({{ $unassignedCharacters->count() }})</span>
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
                                </td>
                                <td>
                                </td>
                            </tr>
                        @endif
                        @foreach($guild->allMembers->whereNull('inactive_at')->whereNull('banned_at') as $member)
                            <tr>
                                <td>
                                    @php
                                        $raidCount = $member->charactersWithAttendance->sum(function ($character) {
                                            return $character->raid_count;
                                        });
                                        $raidsAttended = $member->charactersWithAttendance->where('raid_count', '>', 0)->sum(function ($character) {
                                            return $character->raid_count * $character->attendance_percentage;
                                        });
                                        $attendancePercentage = $raidsAttended / $raidCount;
                                    @endphp
                                    @include('member/partials/listMember', ['raidCount' => $raidCount, 'attendancePercentage' => $attendancePercentage])
                                </td>
                                <td>
                                    <ul class="list-inline">
                                        @foreach($member->charactersWithAttendance->sortBy('name') as $character)
                                            @include('member/partials/listMemberCharacter')
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
        "fixedHeader" : true, // Header row sticks to top of window when scrolling down
        "columns" : [
            null,
            { "orderable" : false },
            { "orderable" : false },
            { "orderable" : false },
        ]
    });

    $("#showInactiveMembers").click(function () {
        $("#inactiveMembers").toggle();
    });
});
</script>
@endsection

