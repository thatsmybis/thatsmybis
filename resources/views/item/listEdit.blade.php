@extends('layouts.app')
@section('title', $instance-> name . " Notes - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12">

            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-wight-medium font-blizz">
                        <span class="fas fa-fw fa-dungeon text-muted"></span>
                        {{ $instance->name }}
                    </h1>
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
                                <a href="#{{ $itemSource->slug }}">
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

            <form class="form-horizontal" role="form" method="POST" action="{{ route('guild.item.list.submit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => $instance->slug]) }}">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-12 mt-3 mb-3 bg-light rounded">
                        @php
                            $oldSourceName = null;
                        @endphp
                        @foreach ($items as $item)
                            @if ($item->source_name != $oldSourceName)
                                <div class="row pb-3 pt-4 rounded top-divider">
                                    <h2 class="ml-3 font-weight-medium font-blizz" id="{{ slug($item->source_name) }}">
                                        {{ $item->source_name }}
                                    </h2>
                                </div>
                            @endif
                            <div class="row striped-light pb-2 pt-3 rounded">

                                <div class="col-lg-4 col-12">
                                    <div class="d-inline-grid align-middle text-5 mb-2">
                                        <label for="items[{{ $loop->iteration }}][name]" class="font-weight-bold d-none d-sm-block">
                                            <span class="sr-only">
                                                Item Name
                                            </span>
                                        </label>
                                        @include('partials/item', ['wowheadLink' => true])
                                    </div>
                                </div>

                                <div class="col-lg-4 col-12 {{ $errors->has('items.' . $loop->iteration . '.note') || $errors->has('items.' . $loop->iteration . '.id') ? 'bg-danger rounded font-weight-bold' : '' }}">
                                    <div class="form-group">

                                        <label for="items[{{ $loop->iteration }}][note]" class="font-weight-bold d-none d-sm-block">
                                            @if ($loop->first)
                                                <span class="fas fa-fw fa-sticky-note text-muted"></span>
                                                Note
                                            @else
                                                <span class="sr-only">Note</span>
                                            @endif
                                        </label>

                                        <input hidden name="items[{{ $loop->iteration }}][id]" value="{{ $item->item_id }}" />


                                        <input name="items[{{ $loop->iteration }}][note]" maxlength="140" data-max-length="140" type="text" placeholder="add a note" class="form-control dark"
                                            value="{{ old('items.' . $loop->iteration . '.note') ? old('items.' . $loop->iteration . '.note') : ($item->guild_note ? $item->guild_note : '') }}">

                                        @if ($errors->has('items.' . $loop->iteration . '.note'))
                                            <div class="'text-danger font-weight-bold'">
                                                {{ $errors->first('items.' . $loop->iteration . '.note') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-lg-4 col-12 {{ $errors->has('items.' . $loop->iteration . '.priority') ? 'bg-danger rounded font-weight-bold' : '' }}">
                                    <div class="form-group">

                                        <label for="priority" class="font-weight-bold d-none d-sm-block">
                                            @if ($loop->first)
                                                <span class="fas fa-fw fa-sort-amount-down text-muted"></span>
                                                Priority
                                            @else
                                                <span class="sr-only">Priority</span>
                                            @endif
                                        </label>

                                        <input name="items[{{ $loop->iteration }}][priority]" maxlength="140" data-max-length="140" type="text" placeholder="eg. mage > warlock > boomkin" class="form-control dark"
                                            value="{{ old('items.' . $loop->iteration . '.priority') ? old('items.' . $loop->iteration . '.priority') : ($item->guild_priority ? $item->guild_priority : '') }}">

                                        @if ($errors->has('items.' . $loop->iteration . '.priority'))
                                            <div class="'text-danger font-weight-bold'">
                                                {{ $errors->first('items.' . $loop->iteration . '.priority') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @php
                                $oldSourceName = $item->source_name;
                            @endphp
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Submit</button>
                    <br>
                    <small>WARNING: This form expires if you don't submit it within {{ env('SESSION_LIFETIME') / 60 }} hours (security reasons)
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('wowheadIconSize', 'medium')
