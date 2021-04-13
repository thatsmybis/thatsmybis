@extends('layouts.app')
@section('title', 'Raids - ' . config('app.name'))

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
                                <li class="p-1 pl-3 rounded">
                                    <div class="d-flex flex-row">
                                        <div class="list-timestamp text-right text-muted p-2">
                                            <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $raid->date }}"></span>
                                        </div>

                                        <div class="p-2">
                                            <ul class="list-inline">
                                                <li class="list-inline-item">
                                                    {{ $raid->name }}
                                                </li>

                                                <li class="list-inline-item">
                                                    {{ $raid->raider_count }}
                                                </li>

                                                <li class="list-inline-item">
                                                    {{ $raid->item_count }}
                                                </li>

                                                @if ($raid->note)
                                                    <li class="list-inline-item">
                                                        {{ $raid->note }}
                                                    </li>
                                                @endif
                                                @if ($raid->member_id)
                                                    <li class="list-inline-item">
                                                        <a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $raid->member_id, 'usernameSlug' => $raid->member->slug]) }}" class="text-muted">
                                                            {{ $raid->other_member_username }}
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($raid->instances->count() > 0)
                                                    <li class="list-inline-item text-muted">
                                                        <ul class="list-inline">
                                                            @foreach ($raid->instances as $instance)
                                                                {{ $instance->short_name }}
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endif
                                                @if ($raid->raidGroups->count() > 0)
                                                    <li class="list-inline-item text-muted">
                                                        <ul class="list-inline">
                                                            @foreach ($raid->raidGroups as $raidGroup)
                                                                <span style="color:{{ $raidGroup->getColor() }};">
                                                                    {{ $raidGroup->name }}
                                                                </span>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        This guild hasn't recorded any raids yet
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
