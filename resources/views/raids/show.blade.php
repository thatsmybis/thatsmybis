@extends('layouts.app')
@section('title', $raid->name . ' - ' . config('app.name'))

@php
    $isFuture = $raid->date > getDateTime();
@endphp

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12">
            <div class="row mb-3">
                <div class="col-12 pt-2 bg-lightest rounded">
                    <div class="row">
                        <div class="col-12">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <h1 class="font-weight-bold">
                                        <span class="fa-fw fas fa-helmet-battle text-dk"></span>
                                        @if ($raid->archived_at)
                                            <span class="text-danger">{{ __("archived") }}</span>
                                        @endif
                                        @if ($raid->cancelled_at)
                                            <span class="text-warning">{{ __("cancelled") }}</span>
                                        @endif
                                        {{ $raid->name }}
                                    </h1>
                                </li>
                               @if ($showEditRaid)
                                    <li class="list-inline-item">
                                        <a href="{{ route('guild.raids.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id]) }}">
                                            <span class="fas fa-pencil"></span>
                                            {{ __("edit") }}
                                        </a>
                                    </li>
                                @endif
                                @if ($showAssignLoot)
                                    <li class="list-inline-item">
                                        <a class="text-success" href="{{ route('item.assignLoot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $raid->id]) }}">
                                            <span class="fa-fw fas fa-sack"></span>
                                            {{ __("Assign Loot") }}
                                        </a>
                                    </li>
                                @endif
                                @if($showEditRaid)
                                    <li class="list-inline-item">
                                        <a href="{{ route('guild.raids.copy', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id]) }}">
                                            <span class="fas fa-copy"></span>
                                            {{ __("copy") }}
                                        </a>
                                    </li>
                                @endif
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $raid->id]) }}">
                                        <span class="fas fa-clipboard-list-check"></span>
                                        {{ __("history") }}
                                    </a>
                                </li>
                            </ul>

                            <ul class="no-indent no-bullet">
                                <li class="mt-2 text-5 font-weight-bold">
                                    {{ $isFuture ? __('in') : '' }}
                                    <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $raid->date }}"></span>
                                    {{ !$isFuture ? __('ago') : '' }}
                                    <span class="js-timestamp" data-timestamp="{{ $raid->date }}" data-format="@ h:mm a, ddd MMM D {{ $isFuture ? '' : 'YYYY' }}"></span>
                                    <span class="small text-muted">{{ __("in your timezone") }}</span>
                                </li>
                                @if ($raid->ignore_attendance)
                                    <span class="text-warning">{{ __("attendance ignored") }}</span>
                                @endif
                                @if ($raid->instances->count())
                                    <li class="mt-2">
                                        <ul class="list-inline">
                                            @foreach ($raid->instances as $instance)
                                                @if (!$loop->first)
                                                    <li class="list-inline-item">
                                                        &sdot;
                                                    </li>
                                                @endif
                                                <li class="list-inline-item text-legendary font-weight-bold">
                                                    {{ $instance->name }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                                @if ($raid->raidGroups->count())
                                    <li class="mt-2">
                                        <ul class="list-inline">
                                            @foreach ($raid->raidGroups as $raidGroup)
                                                <li class="list-inline-item">
                                                    @include('partials/raidGroup', ['raidGroupColor' => $raidGroup->getColor()])
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        @if ($raid->logs->count())
                            <div class="col-lg-6 col-12">
                                <div class="list-group-item rounded mb-3">
                                    <span class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-link"></span>
                                        {{ __("Raid Logs") }}
                                    </span>
                                    <ul>
                                        @foreach ($raid->logs as $log)
                                            <li class="js-markdown-inline">
                                                {{ $log->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @elseif ($raid->logs_deprecated)
                            <div class="col-lg-6 col-12">
                                <div class="list-group-item rounded mb-3">
                                    <span class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-link"></span>
                                        {{ __("Raid Logs") }}
                                    </span>
                                    <ul>
                                        <li class="js-markdown-inline">
                                            {{ $raid->logs_deprecated }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif


                        <div class="col-lg-6 col-12">
                            <div class="list-group-item rounded mb-3">
                                <span class="font-weight-bold">
                                    <span class="text-muted fas fa-fw fa-gift"></span>
                                    {{ __("Loot Assignments") }}
                                </span>
                                <div>
                                    <ul class="list-inline">
                                        @if ($raid->batches->count())
                                            @foreach($raid->batches as $batch)
                                                @if (!$loop->first)
                                                    <li class="list-inline-item">
                                                        &sdot;
                                                    </li>
                                                @endif
                                                <li class="list-inline-item">
                                                    <a href="{{ route('item.assignLoot.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'batch_id' => $batch->id]) }}">
                                                        {{ $batch->name ? $batch->name : "Batch {$batch->id}" }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="list-inline-item text-muted">
                                                {{ __("No batch assignments") }}
                                            </li>
                                        @endif
                                        <li class="list-inline-item">
                                            &sdot;
                                        </li>
                                        <li class="list-inline-item">
                                            @if ($manualItemAssignmentCount)
                                                <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $raid->id, 'type' => 'received']) }}">
                                                    {{ $manualItemAssignmentCount }} {{ __("individual assignments") }}
                                                </a>
                                            @else
                                                <span class="text-muted">
                                                    {{ __("No individual assignments") }}
                                                </span>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        @if ($raid->public_note)
                            <div class="col-lg-6 col-12">
                                <div class="list-group-item rounded mb-3">
                                    <span class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
                                        {{ __("Notes") }}
                                    </span>
                                    <div>
                                        <p class="js-markdown-inline">
                                            {{ $raid->public_note }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($showOfficerNote && $raid->officer_note)
                            <div class="col-lg-6 col-12">
                                <div class="list-group-item rounded mb-3">
                                    <span class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-shield"></span>
                                        {{ __("Officer Notes") }}
                                    </span>
                                    <div>
                                        <p class="js-markdown-inline">
                                            {{ $raid->officer_note }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row mb-3 pt-3">
                <div class="col-12">
                    <table id="characters" class="table table-border table-hover stripe">
                        <thead>
                            <tr>
                                <th>
                                    <span class="fas fa-fw fa-user text-muted"></span>
                                    {{ __("Character") }}
                                    <span class="font-weight-normal">
                                        ({{ $raid->characters->where('pivot.is_exempt', 0)->count()}} {{ __("going") }})
                                    </span>
                                </th>
                                <th>
                                    <span class="fas fa-fw fa-comment-alt-lines text-muted"></span>
                                    {{ __("Notes") }}
                                    <span class="font-weight-normal">
                                        ({{ $raid->characters->where('pivot.is_exempt', 1)->count()}} {{ __("excused") }})
                                    </span>
                                </th>
                                <th>
                                    <span class="fas fa-fw fa-sack text-success"></span>
                                    {{ __("Loot Received") }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($raid->characters as $character)
                                <tr>
                                    <td>
                                        <ul class="no-indent no-bullet mb-0">
                                            <li>
                                                @include('member/partials/listMemberCharacter', ['bold' => true])
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <ul class="no-bullet no-indent mb-0">
                                            @if ($isFuture &&
                                                !$character->pivot->is_exempt &&
                                                !$character->pivot->remark_id &&
                                                !$character->pivot->public_note &&
                                                !($showOfficerNote && $character->pivot->officer_note))
                                                <li>
                                                    —
                                                </li>
                                            @else
                                                @if ($character->pivot->is_exempt)
                                                    <li class="text-warning">
                                                        {{ __("Excused") }}
                                                    </li>
                                                @elseif (!$isFuture)
                                                    <li class="{{ getAttendanceColor($character->pivot->credit) }}">
                                                        {{ $character->pivot->credit * 100 }}% {{ __("credit") }}
                                                    </li>
                                                @endif
                                                @if ($character->pivot->remark_id)
                                                    <li class="{{ getAttendanceColor($character->pivot->credit) }}">
                                                        <span class="text-muted">
                                                            {{ $remarks[$character->pivot->remark_id] }}
                                                        </span>
                                                    </li>
                                                @endif
                                                @if ($character->pivot->public_note)
                                                    <li>
                                                        <span class="js-markdown-inline">{{ $character->pivot->public_note }}</span>
                                                    </li>
                                                @endif
                                                @if ($showOfficerNote && $character->pivot->officer_note)
                                                    <li>
                                                        <span class="font-weight-bold small font-italic text-gold">{{ __("Officer's Note") }}</span>
                                                        <br>
                                                        <span class="js-markdown-inline">{{ $character->pivot->officer_note }}</span>
                                                    </li>
                                                @endif
                                            @endif
                                        </ul>
                                    </td>
                                    <td>
                                        @if($character->received->count())
                                            <ul class="list-inline mb-0">
                                                @foreach ($character->received as $item)
                                                    <li class="list-inline-item">
                                                        @include('partials/item', ['wowheadLink' => false])
                                                        @include('character/partials/itemDetails', ['hideAddedBy' => true, 'hideCreatedAt' => true])
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $("#characters").DataTable({
            order  : [], // Disable initial auto-sort; relies on server-side sorting
            paging : false,
            fixedHeader : { // Header row sticks to top of window when scrolling down
                headerOffset : 43,
            },
            oLanguage: {
                sSearch: "<abbr title='{{ __('Fuzzy searching is ON. To search exact text, wrap your search in \"quotes\"') }}'>{{ __('Search') }}</abbr>"
            },
            columns : [
                { orderable : false, className : "width-130" },
                { orderable : false, className : "width-250" },
                { orderable : false, className : "width-200" },
            ]
        });
    });
</script>
@endsection

