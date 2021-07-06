@extends('layouts.app')
@section('title', __('Raid Groups') . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-helmet-battle text-dk"></span>
                        {{ __("Raid Groups") }}
                    </h1>
                    <span class="text-muted">
                        {{ __("Each character can be assigned") }} <strong>{{ __("one") }}</strong> {{ __("main raid and") }} <strong>{{ __("many") }}</strong> {{ __("other raids.") }}
                        <br>
                        {{ __("Only a character's main raid will show up beside their name across the site.") }}
                    </span>
                </div>
                <div class="col-12 mt-3 mb-3">
                    <a href="{{ route('guild.raidGroup.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" class="btn btn-success">
                        <span class="fas fa-fw fa-plus"></span> {{ __("Create Raid Group") }}
                    </a>
                </div>
                <div class="col-12 pt-3 pb-1 mb-2 bg-light rounded">
                    @if ($guild->allRaidGroups->count() > 0)
                        <ol class="no-bullet no-indent striped">
                            @foreach ($guild->allRaidGroups->where('disabled_at', null) as $raidGroup)
                                <li class="p-3 mb-3 rounded">
                                    @include('guild/raidGroups/partials/raidGroupListItem')
                                </li>
                            @endforeach
                            @if ($guild->allRaidGroups->whereNotNull('disabled_at')->count() > 0)
                                <li class="pt-2 pl-3 pb-3 pr-3 rounded">
                                    <span class="text-muted">{{ __("Archived raid groups") }}</span>
                                    <br>
                                    <span id="showDisabledRaidGroups" class="small text-muted font-italic cursor-pointer">
                                        {{ __("click to show") }}
                                    </span>
                                    <ol class="js-inactive-raid-groups striped no-bullet no-indent" style="display:none;">
                                        @foreach ($guild->allRaidGroups->whereNotNull('disabled_at') as $raidGroup)
                                            <li class="p-3 mb-3 rounded">
                                                @include('guild/raidGroups/partials/raidGroupListItem')
                                            </li>
                                        @endforeach
                                    </ol>
                                </li>
                            @endif
                        </ol>
                    @else
                    <p class="text-4">
                        {{ __("No raid groups yet") }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
$(document).ready(function () {
    $("#showDisabledRaidGroups").click(function () {
        $(".js-inactive-raid-groups").toggle();
    });
});
</script>
@endsection
