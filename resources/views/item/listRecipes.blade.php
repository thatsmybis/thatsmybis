@extends('layouts.app')
@section('title',  __('Guild Recipes') . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row pt-2 mb-3">
        <div class="col-12 text-center pr-0 pl-0">
            <h1 class="font-weight-medium mb-0 font-blizz">
                <span class="fas fa-fw fa-book text-gold"></span>
                {{ __("Guild Recipes") }}
            </h1>
        </div>
        <div class="col-12 pr-0 pl-0">
            <div class="pr-2 pl-2">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item text-muted">
                        {{ __("Quick Filters:") }}
                    </li>
                    <li class="list-inline-item font-weight-light">
                        <span data-value="" class="js-quick-filter text-link cursor-pointer">
                            <span class="fas fa-fw fa-undo"></span>
                            {{ __("All") }}
                        </span>
                    </li>
                    @if ($guild->expansion_id > 1)
                        <li class="list-inline-item">&sdot;</li>
                        <li class="list-inline-item">
                            <span data-value="design" class="js-quick-filter text-link cursor-pointer">
                                {{ __("Design") }}
                            </span>
                        </li>
                    @endif
                    <li class="list-inline-item">&sdot;</li>
                    <li class="list-inline-item">
                        <span data-value="enchant" class="js-quick-filter text-link cursor-pointer">
                            {{ __("Enchant") }}
                        </span>
                    </li>
                    <li class="list-inline-item">&sdot;</li>
                    <li class="list-inline-item">
                        <span data-value="formula" class="js-quick-filter text-link cursor-pointer">
                            {{ __("Formula") }}
                        </span>
                    </li>
                    <li class="list-inline-item">&sdot;</li>
                    <li class="list-inline-item">
                        <span data-value="glyph" class="js-quick-filter text-link cursor-pointer">
                            {{ __("Glyph") }}
                        </span>
                    </li>
                    <li class="list-inline-item">&sdot;</li>
                    <li class="list-inline-item">
                        <span data-value="pattern" class="js-quick-filter text-link cursor-pointer">
                            {{ __("Pattern") }}
                        </span>
                    </li>
                    <li class="list-inline-item">&sdot;</li>
                    <li class="list-inline-item">
                        <span data-value="plans" class="js-quick-filter text-link cursor-pointer">
                            {{ __("Plans") }}
                        </span>
                    </li>
                    <li class="list-inline-item">&sdot;</li>
                    <li class="list-inline-item">
                        <span data-value="recipe" class="js-quick-filter text-link cursor-pointer">
                            {{ __("Recipe") }}
                        </span>
                    </li>
                    <li class="list-inline-item">&sdot;</li>
                    <li class="list-inline-item">
                        <span data-value="schematic" class="js-quick-filter text-link cursor-pointer">
                            {{ __("Schematic") }}
                        </span>
                    </li>
                </ul>
            </div>
            <div class="col-12 pb-3 pr-2 pl-2 rounded">
                <table id="recipes" class="table table-border table-hover stripe">
                    <thead>
                        <tr>
                            <th>
                                <span class="fas fa-fw fa-book text-muted"></span>
                                {{ __("Recipe") }}
                            </th>
                            <th>
                                <span class="fas fa-fw fa-users text-muted"></span>
                                {{ __("Characters") }}
                            </th>
                            <th>
                                <span class="fas fa-fw fa-comment-alt-lines text-muted"></span>
                                {{ __("Notes") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>
                                    <ul class="no-indent no-bullet">
                                        <li>
                                            @include('partials/item', ['wowheadLink' => false])
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <ul class="list-inline">
                                        @foreach($item->receivedAndRecipeCharacters->sortBy('name') as $character)
                                            @include('member/partials/listMemberCharacter')
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    @if (!$item->guild_note && !$item->guild_priority)
                                        —
                                    @else
                                        <div>
                                            <span class="js-markdown-inline">{{ $item->guild_note ? $item->guild_note : '' }}</span>
                                        </div>
                                        <div>
                                            <span class="js-markdown-inline">{{ $item->guild_priority ? $item->guild_priority : '' }}</span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    datatable = $("#recipes").DataTable({
        order  : [], // Disable initial auto-sort; relies on server-side sorting
        paging : false,
        fixedHeader : { // Header row sticks to top of window when scrolling down
            headerOffset : 43,
        },
        autoWidth : true,
        oLanguage: {
            sSearch: "<abbr title='{{ __('Fuzzy searching is ON. To search exact text, wrap your search in \"quotes\"') }}'>{{ __('Search') }}</abbr>"
        },
        columns : [
            { class : "width-200" },
            { class : "width-130", orderable : true },
            { class : "width-400", orderable : true },
        ]
    });

    $(".js-quick-filter").click(function () {
        let value = $(this).data('value');
        $("#recipes").DataTable().column(0).search(value).draw();
    });
});
</script>
@endsection

