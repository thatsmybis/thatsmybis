@extends('layouts.app')
@section('title',  __("Roster Breakdown") . ' - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center pr-0 pl-0">
            <h1 class="font-weight-medium mb-0 font-blizz">
                <span class="fas fa-fw fa-book text-gold"></span>
                {{ __("Roster Breakdown") }}
            </h1>
        </div>

        <div class="col-12 pr-0 pl-0 mb-3">
            <div class="pr-2 pl-2">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">

                        <label for="raidGroupFilter" class="font-weight-light">
                            <span class="text-muted fas fa-fw fa-helmet-battle"></span>
                            {{ __("Raid Group") }}
                        </label>
                        <select id="raidGroupFilter" class="form-control dark selectpicker" autocomplete="off">
                            <option value="">—</option>
                            @foreach ($raidGroups->whereNull('disabled_at') as $raidGroup)
                                <option value="{{ $raidGroup->id }}" style="color:{{ $raidGroup->getColor() }};">
                                    {{ $raidGroup->name }}
                                </option>
                            @endforeach
                        </select>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-12 pb-3 pr-2 pl-2 rounded">
            <table id="rosterBreakdown" class="table table-border table-hover stripe">
                <thead>
                    <tr>
                        <th>
                            <span class="fas fa-fw fa- text-muted"></span>
                            {{ __("Class") }}
                        </th>
                        <th>
                            <span class="fas fa-fw fa- text-muted"></span>
                            {{ __("Spec") }}
                        </th>
                        <th>
                            <span class="fas fa-fw fa-users text-muted"></span>
                            {{ __("Characters") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $classKey => $class)
                        @php
                            $classFilteredCharacters = $characters->where('class', $classKey);
                        @endphp
                        @if ($classFilteredCharacters->count())
                            @php
                                $noSpecCharacters = $classFilteredCharacters->where('spec', null);
                            @endphp
                            @if ($noSpecCharacters->count())
                                <tr>
                                    <td>
                                        <span class="font-weight-bold text-{{ slug($classKey) }}">
                                            {{ $class }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ __("???") }}
                                    </td>
                                    <td>
                                        <ul class="list-inline mb-0">
                                            @foreach($noSpecCharacters as $character)
                                                @php
                                                    $raidGroupIds = '[' . implode(',', array_merge([$character->raid_group_id], $character->secondaryRaidGroups->map(function ($raidGroup) { return $raidGroup->id; })->all())) . ']';
                                                @endphp
                                                @include('member/partials/listMemberCharacter', ['tag' => 1, 'raidGroupIds' => $raidGroupIds])
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endif
                            @foreach ($specs as $specKey => $spec)
                                @if ($spec['class'] === $classKey)
                                    @php
                                        $specFilteredCharacters = $classFilteredCharacters->where('spec', $specKey);
                                    @endphp
                                    @if ($specFilteredCharacters->count())
                                    <tr>
                                        <td>
                                            <span class="font-weight-bold text-{{ slug($classKey) }}">
                                                {{ $class }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold" id="guild-{{ slug($classKey) }}-{{ slug($specKey) }}">
                                                {{ $spec['name'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <ul class="list-inline mb-0">
                                                @foreach($specFilteredCharacters as $character)
                                                @php
                                                    $raidGroupIds = '[' . implode(',', array_merge([$character->raid_group_id], $character->secondaryRaidGroups->map(function ($raidGroup) { return $raidGroup->id; })->all())) . ']';
                                                @endphp
                                                    @include('member/partials/listMemberCharacter', ['tag' => 1, 'raidGroupsIds' => $raidGroupIds])
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    datatable = $("#rosterBreakdown").DataTable({
        order  : [], // Disable initial auto-sort; relies on server-side sorting
        paging : false,
        fixedHeader : { // Header row sticks to top of window when scrolling down
            headerOffset : 43,
        },
        autoWidth : true,
        oLanguage: {
            sSearch: "<abbr title='{{ __('Fuzzy searching is ON. To search exact text, wrap your search in \"quotes\"') }}'>{{ __('Search') }}</abbr>"
        },
        columns : [
            { class : "width-130", orderable : true },
            { class : "width-130", orderable : true },
            { class : "", orderable : false },
        ]
    });
    $("#raidGroupFilter").change(function () {
        const raidGroupId = parseInt($(this).val());
        if (raidGroupId) {
            $("[data-raid-group-ids]").each(function () {
                const ids = $(this).data('raid-group-ids');
                if (ids.includes(raidGroupId)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $("[data-raid-group-ids]").show();
        }
    });
    $(".selectpicker").selectpicker("refresh");
});
</script>
@endsection

@section('wowheadIconSize', 'tiny')
