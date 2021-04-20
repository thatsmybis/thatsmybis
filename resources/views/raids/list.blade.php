@extends('layouts.app')
@section('title', 'Raids - ' . config('app.name'))

@php
    $now = getDateTime();
@endphp

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-helmet-battle text-dk"></span>
                        Raids
                    </h1>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="form-group">
                        <label for="raid_group_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-helmet-battle text-muted"></span>
                            Raid Group
                        </label>
                        <select name="raid_group_id" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                            <option value="">
                                —
                            </option>
                            @foreach ($guild->raidGroups as $raidGroup)
                                <option value="{{ $raidGroup->id }}"
                                    data-tokens="{{ $raidGroup->id }}"
                                    style="color:{{ $raidGroup->getColor() }};"
                                    {{ Request::get('raid_group_id') && Request::get('raid_group_id') == $raidGroup->id ? 'selected' : ''}}>
                                    {{ $raidGroup->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="form-group">
                        <label for="character_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-users text-muted"></span>
                            Character
                        </label>
                        <select name="character_id" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                            <option value="">
                                —
                            </option>
                            @foreach ($guild->characters as $character)
                                <option value="{{ $character->id }}"
                                        data-tokens="{{ $character->id }}" class="text-{{ strtolower($character->class) }}-important"
                                        {{ Request::get('character_id') && Request::get('character_id') == $character->id ? 'selected' : ''}}>
                                    {{ $character->name }} &nbsp; {{ $character->class ? '(' . $character->class . ')' : '' }} &nbsp; {{ $character->is_alt ? "Alt" : '' }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="form-group">
                        <label for="member_id" class="font-weight-bold">
                            <span class="fas fa-fw fa-user text-muted"></span>
                            Member
                        </label>
                        <select name="member_id" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                            <option value="">
                                —
                            </option>
                            @foreach ($guild->members as $member)
                                <option value="{{ $member->id }}"
                                    data-tokens="{{ $member->id }}"
                                    {{ Request::get('member_id') && Request::get('member_id') == $member->id ? 'selected' : ''}}>
                                    {{ $member->username }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    @if ($raids->count())
                        <ol class="no-bullet no-indent striped">
                            @foreach ($raids as $raid)
                                @php
                                    $isFuture = $raid->date > $now;
                                @endphp
                                <li class="pt-3 pb-3 pl-3 p-1 rounded">

                                    <ul class="list-inline">
                                        @include('raids/partials/listRaid', ['bold' => true, 'text' => (!$isFuture || $raid->cancelled_at ? 'muted' : 'white')])

                                        <li class="list-inline-item text-muted">
                                            {{ $raid->character_count }} raiders
                                        </li>
                                        @if (!$isFuture)
                                            <li class="list-inline-item text-muted">
                                                {{ $raid->item_count }} items
                                            </li>
                                        @endif
                                    </ul>
                                    <ul class="list-inline">
                                        <li class="list-inline-item text-muted">
                                            {{ $isFuture ? 'in' : '' }}
                                            <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $raid->date }}"></span>
                                            {{ !$isFuture ? 'ago' : '' }}
                                            <span class="js-timestamp" data-timestamp="{{ $raid->date }}" data-format="@ h:mm a, ddd MMM D {{ $isFuture ? '' : 'YYYY' }}"></span>
                                        </li>

                                        @if ($raid->instances->count() > 0)
                                            <li class="list-inline-item">
                                                <ul class="list-inline font-weight-bold text-muted">
                                                    @foreach ($raid->instances as $instance)
                                                        <span class="text-{{ $raid->cancelled_at ? 'muted' : 'legendary' }}">{{ $instance->short_name }}</span>{{ !$loop->last ? ',' : '' }}
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endif

                                        @if ($raid->raidGroups->count() > 0)
                                            <li class="list-inline-item">
                                                <ul class="list-inline">
                                                    @foreach ($raid->raidGroups as $raidGroup)
                                                        <li class="list-inline-item">
                                                            @include('partials/raidGroup', ['raidGroupColor' => $raidGroup->getColor(), 'text' => ($raid->cancelled_at ? 'muted' : '')])
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p class="mt-4 ml-2  text-3">
                            No raids found
                        </p>
                    @endif
                </div>

                <div class="col-12 mt-3">
                    {{ $raids->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var guild = {!! $guild->toJson() !!};

    $("select").change(function () {
        updateUrl($(this).prop("name"), $(this).val());
    });

    // Updates the URL with the given parameter and value, then reloads the page
    function updateUrl(paramName, paramValue) {
        let url = new URL(location);
        url.searchParams.set(paramName, paramValue);
        url.searchParams.delete('page');
        location = url;
    }
</script>
@endsection
