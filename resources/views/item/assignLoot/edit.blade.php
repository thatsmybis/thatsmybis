@extends('layouts.app')
@section('title', "Update Assigned Loot - " . config('app.name'))

@php
    $now = getDateTime();
    $maxDate = (new \DateTime())->modify('+1 day')->format('Y-m-d');
@endphp

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12 pt-2 mb-2">
            <h1 class="font-weight-medium">
                <span class="fas fa-fw fa-gift text-gold"></span>
                Update Assigned Loot Details
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12 bg-light rounded">
            <form id="editForm" class="form-horizontal" role="form" method="POST" action="{{ route('item.assignLoot.submitEdit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                <fieldset>
                    {{ csrf_field() }}

                    <input hidden name="id" value="{{ $batch->id }}" />

                    <div class="row mt-2 mb-3">

                        @if (count($errors) > 0)
                            <div class="col-12">
                                <ul class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <li>
                                            {{ $error }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="col-12">
                            <div class="ml-3 mb-3">
                                Assigned on <span class="js-timestamp" data-timestamp="{{ $batch->created_at }}" data-format="dddd MMMM Do"></span>
                                @if ($batch->note)
                                    <br>
                                    <span class="text-muted">Original description</span> <span class="font-italic">{{ $batch->note }}</span>
                                @endif
                            </div>
                        </div>

                        @include('item/assignLoot/partials/metadata')

                        <div class="col-sm-6 col-12 pt-2 mb-2">
                            <label for="new_date">
                                <span class="text-muted fas fa-fw fa-calendar-alt"></span>
                                Date Assigned <span class="text-muted small">optional, overwrites all old dates</span>
                            </label>
                            <input name="new_date" min="2004-09-22" max="{{ $maxDate }}" type="date" placeholder="defaults to today" class="form-control dark" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group">
                        <ul class="no-bullet no-indent">
                            <li class="mb-2">
                                <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Submit</button>
                            </li>
                        </ul>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div class="row mt-3">
        @if ($batch->items)
            <div class="col-12 bg-light rounded pt-2">
                <span class="text-4">The following loot assignments will be updated to match the details you select:</span>
                <ul class="mt-3">
                    @if ($batch->items->count())
                        @foreach($batch->items as $item)
                            <li>
                                @include('partials/item', ['wowheadLink' => false])
                                @if ($item->pivot->is_offspec)
                                    OS
                                @endif
                                @if ($item->character_name)
                                    to
                                    <a href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $item->character_id, 'nameSlug' => $item->character_slug]) }}" class="text-{{ strtolower($item->character_class) }}">
                                        {{ $item->character_name }}
                                    </a>
                                    {{ $item->character_is_alt ? 'alt' : '' }}
                                @endif
                            </li>
                        @endforeach
                    @else
                        <li>
                            There are no items associated with this
                        </li>
                    @endif
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(() => warnBeforeLeaving("#editForm"));
</script>
@endsection
