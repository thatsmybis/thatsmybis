@extends('layouts.app')
@section('title',  'Guild Recipes - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row pt-2 mb-3">
        <div class="col-12 text-center pr-0 pl-0">
            <h1 class="font-weight-medium mb-0 font-blizz">
                <span class="fas fa-fw fa-book text-gold"></span>
                Guild Recipes
            </h1>
        </div>
        <div class="col-12 pr-0 pl-0">
            <div class="col-12 pb-3 pr-2 pl-2 rounded">
                <table id="recipes" class="table table-border table-hover stripe">
                    <thead>
                        <tr>
                            <th>
                                <span class="fas fa-fw fa-book text-muted"></span>
                                Recipe
                            </th>
                            <th>
                                <span class="fas fa-fw fa-users text-muted"></span>
                                Characters
                            </th>
                            <th>
                                <span class="fas fa-fw fa-comment-alt-lines text-muted"></span>
                                Notes
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
    $("#recipes").DataTable({
        "order"  : [], // Disable initial auto-sort; relies on server-side sorting
        "paging" : false,
        "fixedHeader" : true, // Header row sticks to top of window when scrolling down
        "columns" : [
            null,
            { "orderable" : false },
            { "orderable" : false },
        ]
    });
});
</script>
@endsection

