@extends('layouts.app')
@section('title', $raidGroup->name . ' ' . __('Attendance') . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 mt-3 mb-3">
                    <a href="{{ route('guild.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" class="btn btn-primary">
                        <span class="fas fa-fw fa-arrow-left"></span> {{ __("Raid Groups") }}
                    </a>
                </div>
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-helmet-battle text-dk"></span>
                        <a href="{{ route('guild.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" style="{{ $raidGroup->role ? 'color:' . $raidGroup->getColor() : '' }}">{{ $raidGroup->name }}</a>
                        {{ __("Attendance") }}
                    </h1>
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <a href="{{ route('guild.raidGroup.attendance', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id]) }}" class="{{ $type === 'main' ? 'font-weight-bold text-white' : '' }}">
                                {{ __("main raider") }}
                            </a>
                        </li>
                        <li class="list-inline-item">&sdot;</li>
                        <li class="list-inline-item">
                            <a href="{{ route('guild.raidGroup.attendance', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id, 'type' => 'secondary']) }}" class="{{ $type === 'secondary' ? 'font-weight-bold text-white' : '' }}">
                                {{ __("general raider") }}
                            </a>
                        </li>
                        <li class="list-inline-item">&sdot;</li>
                        <li class="list-inline-item">
                            <a href="{{ route('guild.raidGroup.attendance', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id, 'type' => 'all']) }}" class="{{ $type === 'all' ? 'font-weight-bold text-white' : '' }}">
                                {{ __("all raiders") }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 mt-3">
            <ul class="list-inline">
                <li class="list-inline-item">
                    {{ $raids->appends(request()->input())->links() }}
                </li>
                <li class="list-inline-item">
                    <span class="js-show-all-loot text-link cursor-pointer font-weight-light" data-column="6">
                        <span class="text-muted fal fa-fw fa-eye"></span>
                        {{ __("Show all loot") }}
                    </span>
                </li>
            </ul>
        </div>
        <div class="row mb-3 pt-3">
            <div class="col-12 bg-lightest rounded">
                <table id="raids" class="table table-border table-hover stripe">
                    <thead>
                        <tr>
                            <th>
                                <span class="fas fa-fw fa-user text-muted"></span>
                                {{ __("Character") }}
                                <span class="small text-muted">
                                    ({{ $raidGroup->characters->count() }})
                                </span>
                            </th>
                            @if ($raids->count())
                                @foreach ($raids as $raid)
                                    <th>
                                        <ul class="no-indent no-bullet">
                                            <a href="{{ route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id, 'raidSlug' => $raid->slug]) }}">
                                                <li class="">
                                                    <span class="js-timestamp text-muted small" data-timestamp="{{ $raid->date }}" data-format="MMM D 'YY"></span>
                                                </li>
                                                @if ($raid->instances->count())
                                                    <li class="">
                                                        <ul class="list-inline">
                                                            @foreach ($raid->instances as $instance)
                                                                <li class="list-inline-item text-legendary font-weight-bold">
                                                                    {{ $instance->short_name }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endif
                                                <li class="small text-muted font-weight-normal">
                                                    {{ $raid->name }}
                                                </li>
                                            </a>
                                            <li class="small text-muted font-weight-normal">
                                                <span class="js-show-raid-loot text-muted cursor-pointer" data-raid-id="{{ $raid->id }}">
                                                    {{ __("show loot") }}
                                                </span>
                                            </li>
                                        </ul>
                                    </th>
                                @endforeach
                            @else
                                <th>
                                    No raids found for this raid group
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($raidGroup->characters as $character)
                            <tr>
                                <td>
                                    <ul class="no-indent no-bullet mb-0">
                                        <li>
                                            @include('member/partials/listMemberCharacter', ['bold' => true])
                                        </li>
                                        <li>
                                            <span class="js-show-character-loot small text-muted cursor-pointer" data-character-id="{{ $character->id }}">
                                                {{ __("show loot") }}
                                            </span>
                                        </li>
                                    </ul>
                                </td>
                                @if ($raids->count())
                                    @foreach ($raids as $raid)
                                        @php
                                            $raidCharacter = $raid->characters->where('id', $character->id)->first();
                                            $isFuture = $raid->date > getDateTime();
                                        @endphp
                                        <td>
                                            @if ($raidCharacter)
                                                @php
                                                    $loot = $raid->items->where('character_id', $raidCharacter->id);
                                                @endphp
                                                <ul class="no-bullet no-indent mb-0">
                                                    @if ($isFuture &&
                                                        !$raidCharacter->pivot->is_exempt &&
                                                        !$raidCharacter->pivot->remark_id &&
                                                        !$raidCharacter->pivot->public_note &&
                                                        !($showOfficerNote && $raidCharacter->pivot->officer_note))
                                                        <li>
                                                            —
                                                        </li>
                                                    @else
                                                        @if ($raidCharacter->pivot->is_exempt)
                                                            <li class="text-warning">
                                                                {{ __("Excused") }}
                                                            </li>
                                                        @elseif (!$isFuture)
                                                            <li class="{{ getAttendanceColor($raidCharacter->pivot->credit) }}">
                                                                {{ $raidCharacter->pivot->credit * 100 }}% {{ __("credit") }}
                                                            </li>
                                                        @endif
                                                        @if ($raidCharacter->pivot->remark_id)
                                                            <li class="{{ getAttendanceColor($raidCharacter->pivot->credit) }}">
                                                                <span class="text-muted">
                                                                    {{ $remarks[$raidCharacter->pivot->remark_id] }}
                                                                </span>
                                                            </li>
                                                        @endif
                                                        @if ($raidCharacter->pivot->public_note)
                                                            <li>
                                                                <span class="js-markdown-inline small">{{ $raidCharacter->pivot->public_note }}</span>
                                                            </li>
                                                        @endif
                                                        @if ($showOfficerNote && $raidCharacter->pivot->officer_note)
                                                            <li>
                                                                <span class="font-weight-bold small font-italic text-gold">{{ __("Officer's Note") }}</span>
                                                                <br>
                                                                <span class="js-markdown-inline small">{{ $raidCharacter->pivot->officer_note }}</span>
                                                            </li>
                                                        @endif
                                                    @endif
                                                </ul>
                                                @if($loot->count())
                                                    <span class="js-show-loot small text-muted cursor-pointer" data-raid-id="{{ $raid->id }}" data-character-id="{{ $raidCharacter->id }}">
                                                        {{ __("loot") }} ({{ $loot->count() }})
                                                    </span>
                                                    <ul class="js-loot list-inline mb-0" data-raid-id="{{ $raid->id }}" data-character-id="{{ $raidCharacter->id }}" style="display:none;">
                                                        @foreach ($raid->items->where('character_id', $raidCharacter->id) as $item)
                                                            <li class="list-inline-item">
                                                                @include('partials/item', ['wowheadLink' => false])
                                                                @include('character/partials/itemDetails', ['hideAddedBy' => true, 'hideCreatedAt' => true])
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            @else
                                                {{ __("n/a") }}
                                            @endif
                                        </td>
                                    @endforeach
                                @else
                                    <td>
                                        {{ __("n/a") }}
                                    </td>
                                @endif
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
    $("#raids").DataTable({
        order  : [], // Disable initial auto-sort; relies on server-side sorting
        paging : false,
        fixedHeader : { // Header row sticks to top of window when scrolling down
            headerOffset : 43,
        },
        oLanguage: {
            sSearch: "<abbr title='{{ __('Fuzzy searching is ON. To search exact text, wrap your search in \"quotes\"') }}'>{{ __('Search') }}</abbr>"
        },
        columns : [
            { orderable : true, className: "width-10pct"},
            @if ($raids->count())
                @foreach ($raids as $raid)
                    { orderable : false, className: "width-10pct"},
                @endforeach
            @else
                { orderable : false, className: "width-10pct"},
            @endif
        ]
    });
});
</script>
<script src="{{ loadScript('raidGroupAttendance.js') }}"></script>
@endsection
