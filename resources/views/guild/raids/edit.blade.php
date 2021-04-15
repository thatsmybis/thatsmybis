@extends('layouts.app')
@section('title', ($raid ? "Edit" : "Create") . " Raid - " . config('app.name')

)@php
    $maxDate = (new \DateTime())->modify('+1 day')->format('Y-m-d');

    // Iterating over 100+ characters 100+ items results in TENS OF THOUSANDS OF ITERATIONS.
    // So we're iterating over the characters only one time, saving the results, and printing them.
    $characterSelectOptions = (string)View::make('partials.characterOptions', ['characters' => $guild->characters]);
@endphp

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row mb-3">
                @if ($raid)
                    <div class="col-12 pt-2 bg-lightest rounded">
                        {{ $raid->name }}
                    </div>
                @else
                    <div class="col-12 pt-2 mb-2">
                        <h1 class="font-weight-medium ">Create a Raid</h1>
                    </div>
                @endif
            </div>

            @if (count($errors) > 0)
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            @endif
            <form id="editForm" class="form-horizontal" role="form" method="POST" action="{{ route(($raid ? 'guild.raids.update' : 'guild.raids.create'), ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="id" value="{{ $raid ? $raid->id : '' }}" />

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="row mb-4">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-helmet-battle"></span>
                                        Raid Name
                                    </label>
                                    <input name="name"
                                        maxlength="40"
                                        type="text"
                                        class="form-control dark"
                                        placeholder="eg. Gurgthock"
                                        value="{{ old('name') ? old('name') : ($raid ? $raid->name : '') }}" />
                                </div>
                            </div>

                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="date" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-calendar"></span>
                                        Date
                                    </label>
                                    <input name="date"
                                        type="date"
                                        class="form-control dark"
                                        value="{{ old('date') ? old('date') : ($raid ? $raid->date : '') }}" />
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="logs" class="font-weight-bold">
                                        Link to Logs
                                    </label>
                                    <input name="logs"
                                        maxlength="255"
                                        type="text"
                                        class="form-control dark"
                                        placeholder="a Warcraft Logs link perhaps?"
                                        value="{{ old('logs') ? old('logs') : ($raid ? $raid->logs : '') }}" />
                                </div>
                            </div>
                            @if ($raid)
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mb-0">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="is_cancelled" value="1" class="" autocomplete="off"
                                                    {{ old('is_cancelled') && old('is_cancelled') == 1 ? 'checked' : ($raid && $raid->is_cancelled ? 'checked' : '') }}>
                                                    Cancelled <small class="text-muted">closest you can get to deleting this</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mb-3 pb-1 pt-2 bg-light rounded">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="public_note" class="font-weight-bold">
                                <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
                                Public Note
                                <small class="text-muted">anyone in the guild can see this</small>
                            </label>
                            <textarea maxlength="140" data-max-length="140" name="public_note" rows="2" placeholder="anyone in the guild can see this" class="form-control dark">{{ old('public_note') ? old('public_note') : ($raid ? $raid->public_note : '') }}</textarea>
                        </div>
                    </div>

                    @if ($currentMember->hasPermission('edit.officer-notes'))
                        <div class="col-12 mt-4">
                            <div class="form-group">
                                <label for="officer_note" class="font-weight-bold">
                                    <span class="text-muted fas fa-fw fa-shield"></span>
                                    Officer Note
                                    <small class="text-muted">only officers can see this</small>
                                </label>
                                @if (isStreamerMode())
                                    Hidden in streamer mode
                                @else
                                    <textarea maxlength="140" data-max-length="140" name="officer_note" rows="2" placeholder="only officers can see this" class="form-control dark">{{ old('officer_note') ? old('officer_note') : ($raid ? $raid->officer_note : '') }}</textarea>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row mb-3 pb-1 pt-2 bg-light rounded">
                    <div class="col-sm-6 col-12">
                        <label for="instance_id[0]" class="font-weight-bold">
                            <span class="fas fa-fw fa-helmet-battle text-muted"></span>
                            Dungeons
                        </label>
                        @for ($i = 0; $i < $maxInstances; $i++)
                            <div class="form-group {{ $i > 1 ? 'js-hide-empty' : '' }}" style="{{ $i > 1 ? 'display:none;' : '' }}">
                                <select name="instance_id[]" class="form-control dark {{ $i > 0 ? 'js-show-next' : '' }}">
                                    <option value="" selected>
                                        —
                                    </option>

                                    @foreach ($instances as $instance)
                                        <option value="{{ $instance->id }}"
                                            {{ old('instance_id.' . $i) && old('instance_id.' . $i) == $instance->id ? 'selected' : ($raid && $raid->instances->slice($i, 1) && $raid->instances->slice($i, 1) == $instance->id ? 'selected' : '') }}>
                                            {{ $instance->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endfor
                    </div>

                    <div class="col-sm-6 col-12">
                        <label for="raid_group_id[0]" class="font-weight-bold">
                            <span class="fas fa-fw fa-helmet-battle text-muted"></span>
                            Raids
                        </label>
                        @for ($i = 0; $i < $maxRaids; $i++)
                            <div class="form-group {{ $i > 1 ? 'js-hide-empty' : '' }}" style="{{ $i > 1 ? 'display:none;' : '' }}">
                                <select name="raid_group_id[]" class="form-control dark {{ $i > 0 ? 'js-show-next' : '' }}">
                                    <option value="" selected>
                                        —
                                    </option>

                                    @foreach ($guild->raidGroups as $raidGroup)
                                        <option value="{{ $raidGroup->id }}"
                                            style="color:{{ $raidGroup->getColor() }};"
                                            {{ old('raid_group_id.' . $i) && old('raid_group_id.' . $i) == $raidGroup->id ? 'selected' : ($raid && $raid->raidGroups->slice($i, 1) && $raid->raidGroups->slice($i, 1) == $raidGroup->id ? 'selected' : '') }}>
                                            {{ $raidGroup->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="form-group">
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        warnBeforeLeaving("#editForm")

        $(".js-show-next").change(function() {
            showNext(this);
        }).change();

        $(".js-show-next").keyup(function() {
            showNext(this);
        });
    });

    // If the current element has a value, show it and the next element that is hidden because it is empty
    function showNext(currentElement) {
        if ($(currentElement).val() != "") {
            $(currentElement).show();
            $(currentElement).parent().next(".js-hide-empty").show();
        }
    }
</script>
@endsection
