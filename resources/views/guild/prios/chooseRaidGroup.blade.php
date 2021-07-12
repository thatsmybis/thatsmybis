@extends('layouts.app')
@section('title', __("Item Prios For") . ' ' . $instance-> name . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium font-blizz">
                        <span class="fas fa-fw fa-dungeon text-muted"></span>
                        {{ $instance->name }} {{ __("Prios") }}
                    </h1>
                </div>
                <div class="col-12 pt-3 pb-1 mb-2 bg-light rounded">
                    <div class="text-3 font-weight-medium ml-4 mb-3">
                        {{ __("Choose a raid group") }}
                    </div>
                    @if ($guild->raidGroups->count() > 0)
                        <ol class="no-bullet no-indent striped">
                            @foreach ($guild->raidGroups as $raidGroup)
                                <li class="p-3 mb-3 rounded">
                                    <a href="{{ route('guild.prios.assignPrios', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => $instance->slug, 'raidGroupId' => $raidGroup->id]) }}" class="tag text-4">
                                        <span class="role-circle-large" style="{{ $raidGroup->role ? 'background-color:' . $raidGroup->role->getColor() : '' }}" title="{{ $raidGroup->role ? $raidGroup->role->getColor() : ''}}"></span>
                                        <span class="font-weight-bold text-danger">{{ $raidGroup->disabled_at ? __('ARCHIVED') : '' }}</span>
                                        <span title="{{ $raidGroup->slug }}">{{ $raidGroup->name }}</span>
                                        <span class="fas fa-fw fa-arrow-alt-right text-success"></span>
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    @else
                    <p class="text-4">
                        {{ __("No raid groups yet") }}
                        <a href="{{ route('guild.raidGroup.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" class="btn btn-success">
                            <span class="fas fa-fw fa-plus"></span> {{ __("Create New Raid Group") }}
                        </a>
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
