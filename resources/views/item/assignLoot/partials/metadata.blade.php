<!-- Name -->
@php
    $oldName = null;
    if (old('name')) {
        $oldName = old('name');
    } else if (isset($raid) && $raid) {
        $oldName = $raid->name . ' loot';
    } else if (isset($batch) && $batch && $batch->name) {
        $oldName = $batch->name;
    }
@endphp
<div class="col-12 {{ $errors->has('name') ? 'text-danger font-weight-bold' : '' }}">
    <div class="form-group">
        <label for="name" class="font-weight-bold">
            <span class="text-muted">optional</span> Give this assignment a name
        </label>
        <input name="name"
            autocomplete="off"
            maxlength="75"
            type="text"
            class="form-control dark"
            placeholder="eg. Week 1 MC clear"
            value="{{ $oldName }}" />
    </div>
</div>

<!-- Raid Group -->
@php
    $oldRaidGroupId = null;
    if (old('raid_group_id')) {
        $oldRaidGroupId = old('raid_group_id');
    } else if (isset($raid) && $raid && $raid->raidGroups->first()) {
        $oldRaidGroupId = $raid->raidGroups->first()->id;
    } else if (isset($batch) && $batch && $batch->raid_group_id) {
        $oldRaidGroupId = $batch->raid_group_id;
    }
@endphp
<div class="col-lg-3 col-sm-6 col-12 pt-2 mb-2">
    <label for="raid_group_id font-weight-light">
        <span class="text-muted fas fa-fw fa-helmet-battle"></span>
        Raid Group
    </label>
    <select name="raid_group_id" class="form-control dark selectpicker" data-live-search="true" autocomplete="off">
        <option value="">—</option>
        @php

        @endphp
        @foreach ($guild->raidGroups as $raidGroup)
            <option value="{{ $raidGroup->id }}" style="color:{{ $raidGroup->getColor() }};" {{ $oldRaidGroupId && $oldRaidGroupId == $raidGroup->id ? 'selected' : '' }}>
                {{ $raidGroup->name }}
            </option>
        @endforeach
    </select>
</div>

<!-- Raid -->
@if (isset($raid) && $raid)
    <input hidden name="raid_id" value="{{ $raid->id }}" />
    <div class="col-lg-3 col-sm-6 col-12 pt-2 mb-2">
        <label for="raid_id font-weight-light">
            <span class="text-muted fas fa-fw fa-calendar-alt"></span>
            Raid
            <span class="text-muted">locked</span>
            <a class="" target="_blank" href="{{ route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id, 'raidSlug' => $raid->slug]) }}">
                show raid
            </a>
        </label>
        <input
            disabled
            type="text"
            class="form-control dark text-warning font-weight-bold"
            value="{{ $raid->name }}" />
    </div>
@else
    @php
        $oldRaidId = null;
        if (old('raid_id')) {
            $oldRaidId = old('raid_id');
        } else if (isset($batch) && $batch && $batch->raid_id) {
            $oldRaidId = $batch->raid_id;
        }
    @endphp
    <div class="col-lg-3 col-sm-6 col-12 pt-2 mb-2">
        <label for="raid_id font-weight-light">
            <span class="text-muted fas fa-fw fa-calendar-alt"></span>
            Raid
            <span class="small text-muted">last {{ $raidHistoryLimit }} raids shown</span>
        </label>
        <select name="raid_id" class="form-control dark selectpicker" data-live-search="true" autocomplete="off">
            <option value="">—</option>
            @foreach ($guild->raids as $guildRaid)
                <option value="{{ $guildRaid->id }}" {{ $oldRaidId && $oldRaidId == $guildRaid->id ? 'selected' : '' }}>
                    {{ $guildRaid->name }}
                    {{ $guildRaid->date ? '(' . ($guildRaid->date > $now ? 'in ' . timeUntil(strtotime($guildRaid->date)) : timeSince(strtotime($guildRaid->date)) . ' ago')  . ')' : null }}
                </option>
            @endforeach
        </select>
    </div>
@endif
