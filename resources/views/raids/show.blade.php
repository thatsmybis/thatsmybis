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
                                            <span class="text-danger">archived</span>
                                        @endif
                                        @if ($raid->cancelled_at)
                                            <span class="text-warning">cancelled</span>
                                        @endif
                                        {{ $raid->name }}
                                    </h1>
                                </li>
                               @if ($showEditRaid)
                                    <li class="list-inline-item">
                                        <a href="{{ route('guild.raids.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id]) }}">
                                            <span class="fas fa-pencil"></span>
                                            edit
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a class="text-success" href="{{ route('item.assignLoot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $raid->id]) }}">
                                            <span class="fa-fw fas fa-sack"></span>
                                            Assign Loot
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a href="{{ route('guild.raids.copy', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id]) }}">
                                            <span class="fas fa-copy"></span>
                                            copy
                                        </a>
                                    </li>
                                @endif
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $raid->id]) }}">
                                        <span class="fas fa-clipboard-list-check"></span>
                                        history
                                    </a>
                                </li>
                            </ul>

                            <ul class="no-indent no-bullet">
                                <li class="mt-2 text-5 font-weight-bold">
                                    {{ $isFuture ? 'in' : '' }}
                                    <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $raid->date }}"></span>
                                    {{ !$isFuture ? 'ago' : '' }}
                                    <span class="js-timestamp" data-timestamp="{{ $raid->date }}" data-format="@ h:mm a, ddd MMM D {{ $isFuture ? '' : 'YYYY' }}"></span>
                                    <span class="small text-muted">in your timezone</span>
                                </li>
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
                                                @if (!$loop->first)
                                                    <li class="list-inline-item">
                                                        &sdot;
                                                    </li>
                                                @endif
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
                        @if ($raid->logs)
                            <div class="col-lg-6 col-12">
                                <div class="mb-3">
                                    <span class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-link"></span>
                                        Raid Logs
                                    </span>
                                    <div class="js-markdown-inline">
                                        {{ $raid->logs }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        @if ($raid->public_note)
                            <div class="col-lg-6 col-12">
                                <div class="list-group-item rounded mb-3">
                                    <span class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
                                        Notes
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
                                        <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
                                        Officer Notes
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
                                    Character
                                </th>
                                <th>
                                    <span class="fas fa-fw fa-comment-alt-lines text-muted"></span>
                                    Notes
                                </th>
                                <th>
                                    <span class="fas fa-fw fa-sack text-success"></span>
                                    Loot Received
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($raid->characters as $character)
                                <tr>
                                    <td>
                                        <ul class="no-indent no-bullet">
                                            <li>
                                                @include('member/partials/listMemberCharacter', ['bold' => true])
                                            </li>
                                            @if ($character->pivot->is_exempt)
                                                <li class="text-warning">
                                                    <span class="fas fa-fw fa-user-chart text-muted"></span>
                                                    Excused
                                                    @if ($character->pivot->remark_id)
                                                        <span class="text-muted">
                                                            {{ $remarks[$character->pivot->remark_id] }}
                                                        </span>
                                                    @endif
                                                </li>
                                            @elseif (!$isFuture)
                                                <li class="{{ getAttendanceColor($character->pivot->credit) }}">
                                                    <span class="fas fa-fw fa-user-chart text-muted"></span>
                                                    {{ $character->pivot->credit * 100 }}% credit
                                                    @if ($character->pivot->remark_id)
                                                        <span class="text-muted">
                                                            {{ $remarks[$character->pivot->remark_id] }}
                                                        </span>
                                                    @endif
                                                </li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td>
                                        <ul class="list-inline">
                                            <div>
                                                @if ($character->pivot->public_note)
                                                    <span class="js-markdown-inline">{{ $character->pivot->public_note }}</span>
                                                @else
                                                    —
                                                @endif
                                            </div>
                                            @if ($showOfficerNote && $character->pivot->officer_note)
                                                <div>
                                                    <span class="font-weight-bold small font-italic text-gold">Officer's Note</span>
                                                    <br>
                                                    <span class="js-markdown-inline">{{ $character->pivot->officer_note }}</span>
                                                </div>
                                            @endif
                                        </ul>
                                    </td>
                                    <td>
                                        @if($loop->first)
                                            feature coming when it's done™
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
            "order"  : [], // Disable initial auto-sort; relies on server-side sorting
            "paging" : false,
            "fixedHeader" : true, // Header row sticks to top of window when scrolling down
            "columns" : [
                { "orderable" : false },
                { "orderable" : false },
                { "orderable" : false },
            ]
        });
    });
</script>
@endsection

