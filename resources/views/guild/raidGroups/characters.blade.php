@extends('layouts.app')
@section('title', $raidGroup->name . ' ' . ($isSecondary ? __('General Raiders') : __('Mains')) . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
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
                        <span class="fas fa-fw fa-helmet-battle text-{{ $isSecondary ? 'muted' : 'gold' }}"></span>
                        <span style="{{ $raidGroup->role ? 'color:' . $raidGroup->getColor() : '' }}">{{ $raidGroup->name }}</span> {{ $isSecondary ? __('General') : __('Main') }} {{ __("Raiders") }}
                    </h1>
                    <span class="text-muted">
                        {{ __("Each character can be a main raider in just") }} <strong>{{ __("one") }}</strong> {{ __("raid, and a general raider in") }} <strong>{{ __("many") }}</strong> {{ __("other raids.") }}
                        <br>
                        {{ __("Only a character's main raid will show up beside their name across the site.") }}
                        <br>
                        @if ($isSecondary)
                            {{ __("This raid group also has") }}
                            <a href="{{ route('guild.raidGroup.mainCharacters', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id]) }}">
                                {{ $raidGroup->characters_count }} {{ __("main raider") }}{{ $raidGroup->characters_count != 1 ? 's' : '' }}
                            </a>
                        @else
                            {{ __("This raid group also has") }}
                            <a href="{{ route('guild.raidGroup.secondaryCharacters', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id]) }}">
                                {{ $raidGroup->secondary_characters_count }} {{ __("general raider") }}{{ $raidGroup->secondary_characters_count != 1 ? 's' : '' }}
                            </a>
                        @endif
                    </span>
                </div>
                <!-- Doing some funky flex and 100% width magic to get the lists to take up the whole height -->
                <div class="d-flex col-6 pr-2 pt-3 pb-1 mb-2 bg-light rounded">
                    <form id="characterForm"
                        class="d-flex flex-column w-100 form-horizontal"
                        role="form"
                        method="POST"
                        action="{{ route('guild.raidGroup.' . ($isSecondary ? 'updateSecondaryCharacters' : 'updateMainCharacters'), ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                        {{ csrf_field() }}
                        <input hidden name="raid_group_id" value="{{ $raidGroup->id }}" />

                        <div class="sortable-no-empty-header">
                            <h4>
                                {{ __("Selected") }} &nbsp; <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> {{ __("Save") }}</button>
                            </h4>
                        </div>
                        <div class="d-flex" style="flex:1"> <!-- flex:1 will make it take up the full vertical space -->
                            <ul id="selectedCharacters" class="w-100 sortable-no-empty no-bullet no-indent pt-3 pl-3 pr-3 pb-2 bg-dark rounded">
                                @foreach ($selectedCharacters as $character)
                                    @include('guild/raidGroups/partials/sortableCharacter')
                                @endforeach
                            </ul>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> {{ __("Save") }}</button>
                        </div>
                    </form>
                </div>
                <div class="d-flex w-100 flex-column col-6 pl-2 pt-3 pb-1 mb-2 bg-light rounded">
                    <div class="sortable-no-empty-header">
                        <h4>{{ __("Available") }}</h4>
                    </div>
                    <div class="d-flex" style="flex:1">
                        <ul id="availableCharacters" class="w-100 sortable-no-empty no-bullet no-indent pt-3 pl-3 pr-3 pb-2 bg-dark rounded">
                            @foreach ($guild->characters as $character)
                                @include('guild/raidGroups/partials/sortableCharacter')
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ loadScript('raidGroupCharacters.js') }}"></script>
@endsection
