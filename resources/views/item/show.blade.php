@extends('layouts.app')
@section('title', $item->name . ' - ' . config('app.name'))


@section('content')
<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-12">
            <ul class="list-inline">
                <li class="list-inline-item bg-lightest rounded pl-3 pb-3 pr-3 mt-3 item-tooltip align-top">
                    <h1 class="font-weight-bold">
                        @if ($itemJson)
                            {{-- %69 (code for 'i') is a workaround that masks the link so wowhead's script won't parse it, allowing *us* to style it however we want --}}
                            <a class="q{!! $itemJson->quality !!}" href="https://classic.wowhead.com/%69tem={{ $item->item_id}}" target="_blank">
                                <span class="iconlarge">
                                    <ins style='background-image: url("https://wow.zamimg.com/images/wow/icons/large/{!! $itemJson->icon !!}.jpg");'></ins><del></del></span>{!! $itemJson->name !!}
                            </a>
                        @else
                            @include('partials/item', ['wowheadLink' => true])
                        @endif
                    </h1>
                    @if ($itemJson)
                        {!! $itemJson->tooltip !!}
                    @endif
                </li>
                @if ($guild)
                    <li class="list-inline-item bg-lightest rounded p-3 mt-3 align-top item-notes">
                        <form role="form" method="POST" action="{{ route('guild.item.updateNote', ['guildSlug' => $guild->slug]) }}">
                            {{ csrf_field() }}

                            <input hidden name="id" value="{{ $item->item_id }}" />

                            <div class="row mb-3 pt-3">

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
                                    <span class="text-muted font-weight-bold">
                                        <span class="fas fa-fw fa-comment-alt-lines"></span>
                                        Guild Note
                                    </span>
                                </div>
                                <div class="col-12 mb-3 pl-4">
                                    {{ $notes['note'] ? $notes['note'] : '—' }}
                                    @if ($showNoteEdit)
                                        <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                                    @endif
                                </div>
                                @if ($showNoteEdit)
                                    <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                                        <div class="form-group">
                                            <label for="note" class="sr-only">
                                                Item Priority
                                            </label>
                                            <textarea data-max-length="144" name="note" rows="2" placeholder="add a note" class="form-control dark">{{ old('note') ? old('note') : ($item ? $notes['note'] : '') }}</textarea>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <span class="text-muted font-weight-bold">
                                        <span class="fas fa-fw fa-sort-amount-down"></span>
                                        Guild Priority
                                    </span>
                                </div>
                                <div class="col-12 mb-3 pl-4">
                                    {{ $notes['priority'] ? $notes['priority'] : '—' }}
                                    @if ($showNoteEdit)
                                        <span class="js-show-note-edit fas fa-fw fa-pencil text-link cursor-pointer" title="edit"></span>
                                    @endif
                                </div>
                                @if ($showNoteEdit)
                                    <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                                        <div class="form-group">
                                            <label for="priority" class="sr-only">
                                                Item Priority
                                            </label>
                                            <textarea data-max-length="144" name="priority" rows="2" placeholder="eg. mage > warlock > boomkin > arcane shot hunter" class="form-control dark">{{ old('priority') ? old('priority') : ($item ? $notes['priority'] : '') }}</textarea>
                                        </div>
                                    </div>
                                @endif

                                <div class="js-note-input col-12 mb-3 pl-4" style="display:none;">
                                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Save</button>
                                </div>
                            </div>
                        </form>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="row pt-2 mb-3 bg-lightest rounded">
        <div class="col-12">
            <h2 class="font-weight-bold pl-2">Wishlisted</h2>
        </div>
        <div class="col-12 pr-0 pl-0">
            @if ($wishlistCharacters->count() > 0)
                @include('partials/characterDatatable', ['characters' => $wishlistCharacters])
            @else
                <ul>
                    <li class="lead no-bullet">
                        <em>nobody has added this item to their wishlist yet</em>
                    </li>
                </ul>
            @endif
        </div>
    </div>

    <div class="row mr-1 ml-1 mb-3 pt-1  bg-lightest rounded">
        <div class="col-12">
            <h2>Have It</h2>
            @if ($receivedAndRecipeCharacters->count() > 0)
                <ul class="list-inline striped">
                    @foreach ($receivedAndRecipeCharacters as $character)
                        <li class="list-inline-item rounded pt-2 pl-3 pb-3 pr-3">
                            @include('character/partials/header', ['showDetails' => false, 'showEdit' => false, 'showOwner' => false])
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="lead mb-3">
                    <em>nobody has added this item to their character sheet yet</em>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var characters = {!! $showOfficerNote ? $wishlistCharacters->makeVisible('officer_note')->toJson() : $wishlistCharacters->toJson() !!};
    var guild      = {!! $guild->toJson() !!};
    var raids      = {!! $raids->toJson() !!};
    var showOfficerNote = {{ $showOfficerNote ? 'true' : 'false' }};;
</script>
<script src="{{ env('APP_ENV') == 'local' ? asset('/js/roster.js') : mix('js/processed/roster.js') }}"></script>
@endsection

