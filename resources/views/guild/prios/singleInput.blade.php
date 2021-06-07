@extends('layouts.app')
@section('title', "Prios for " . $raidGroup-> name . " " . $item-> name . " - " . config('app.name'))

@php
    // Iterating over 100+ characters 100+ items results in TENS OF THOUSANDS OF ITERATIONS.
    // So we're iterating over the characters only one time, saving the results, and printing them.
    $characterSelectOptions = (string)View::make('partials.characterOptions', ['characters' => $guild->characters]);
@endphp

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12">

            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium font-blizz">
                        <span class="fas fa-fw fa-sack text-muted"></span>
                        {{ $raidGroup->name }} Prios
                    </h1>
                    <small>
                        <strong>Note:</strong> When someone receives an item, we'll attempt to automatically remove it from their prios. If they have the same item prio'd in multiple raid groups, we'll remove only the first one we find.
                    </small>
                </div>
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

            <form id="editForm" class="form-horizontal" role="form" method="POST" action="{{ route('guild.item.prios.submit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="raid_group_id" value="{{ $raidGroup->id }}">
                <input hidden name="item_id" value="{{ $item->item_id }}">

                @foreach ([$item] as $item)
                    @include('guild.prios.partials.itemInput')
                @endforeach

                <div class="form-group mt-3">
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Submit</button>
                    <br>
                    <small>WARNING: This form expires if you don't submit it within {{ env('SESSION_LIFETIME') / 60 }} hours (security reasons)
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ loadScript('prio.js') }}"></script>
<script>
    @include('guild.prios.partials.inputTemplate')
</script>
@endsection

@section('wowheadIconSize', 'medium')
