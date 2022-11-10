@extends('layouts.app')
@section('title', __("Prios for") . " " . $raidGroup->name . " " . $instance->name . " - " . config('app.name'))

@php
    // Iterating over 100+ characters 100+ items results in TENS OF THOUSANDS OF ITERATIONS.
    // So we're iterating over the characters only one time, saving the results, and printing them.
    $characterSelectOptions = (string)View::make('partials.characterOptions', ['characters' => $guild->characters]);

    // used to keep track of the order of the list when the user pins items with javascript
    $headerCount = 0;
@endphp

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12 {{ request()->get('hideAds') ? '' : 'col-xl-10' }}">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium font-blizz">
                        <span class="fas fa-fw fa-dungeon text-muted"></span>
                        {{ $raidGroup->name }} {{ __("Prios for") }} {{ $instance->name }}
                    </h1>
                    <small>
                        <strong>{{ __("Note") }}:</strong>
                        {{ __("When someone receives an item, we'll attempt to automatically remove it from their prios. If they have the same item prio'd in multiple raid groups, we'll only remove the first one we find.") }}
                    </small>
                </div>
            </div>

            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <ul class="list-inline mb-0">
                        @foreach ($instance->itemSources as $itemSource)
                            @if (!$loop->first)
                                <li class="list-inline-item">
                                    &sdot;
                                </li>
                            @endif
                            <li class="list-inline-item mt-2 mb-2">
                                <a href="#{{ trim($itemSource->slug) }}">
                                    {{ $itemSource->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
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

            @include('partials/loadingBars')

            <form id="editForm"
                style="display:none;"
                class="form-horizontal"
                role="form"
                method="POST"
                action="{{ route('guild.prios.assignPrios.submit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => $instance->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="raid_group_id" value="{{ $raidGroup->id }}">
                <input hidden name="instance_id" value="{{ $instance->id }}">

                <div class="row">
                    <div id="pinnableList" class="col-12 mt-3 mb-3 bg-light rounded">
                        @php
                            $oldSourceName = null;
                        @endphp
                        @foreach ($items as $item)
                            @if ($item->source_name != $oldSourceName)
                                <div class="js-pin-sortable pb-3 pt-4 rounded top-divider" data-original-order="{{ $loop->index + $headerCount }}" data-user-order="">
                                    <div>
                                        <h2 class="ml-3 font-weight-medium font-blizz" id="{{ slug($item->source_name) }}">
                                            {{ $item->source_name }}
                                        </h2>
                                    </div>
                                </div>
                                @php
                                    $headerCount++;
                                @endphp
                            @endif

                            @include('guild.prios.partials.itemInput', ['showPin' => true])

                            @php
                                $oldSourceName = $item->source_name;
                            @endphp
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> {{ __("Submit") }}</button>
                    <br>
                    <small>
                        {{ __("WARNING: This form expires if you don't submit it within :hours hours (security reasons)", ['hours' =>  env('SESSION_LIFETIME') / 60]) }}
                    </small>
                </div>
            </form>
        </div>
        @if (!request()->get('hideAds'))
            <div class="d-none col-xl-2 d-xl-block m-0 p-0">
                @include('partials/adRightBanner')
            </div>
        @endif
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

